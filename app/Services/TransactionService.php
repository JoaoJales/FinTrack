<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class TransactionService
{
    public function getAllByUser(int $userId, ?Request $request = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = Transaction::where('transactions.user_id', $userId)
            ->with(['category', 'account.institution', 'destinationAccount.institution'])
            ->leftJoin('categories', 'transactions.category_id', '=', 'categories.id')
            ->select('transactions.*');

        if ($request) {
            if ($search = $request->search) {
                $query->whereRaw('LOWER(transactions.description) LIKE LOWER(?)', ["%{$search}%"]);
            }

            if ($type = $request->type) {
                $query->where('transactions.type', $type);
            }

            if ($accountId = $request->account_id) {
                $query->where(function ($q) use ($accountId) {
                    $q->where('transactions.account_id', $accountId)
                        ->orWhere('transactions.destination_account_id', $accountId);
                });
            }

            if ($categoryId = $request->category_id) {
                $query->where('transactions.category_id', $categoryId);
            }

            // Mês rápido (ex: "2025-03") — tem prioridade sobre date_start/date_end
            if ($month = $request->month) {
                $date = Carbon::createFromFormat('Y-m', $month);
                $query->whereMonth('transactions.date', $date->month)
                    ->whereYear('transactions.date', $date->year);
            } else {
                if ($dateStart = $request->date_start) {
                    $query->where('transactions.date', '>=', $dateStart);
                }
                if ($dateEnd = $request->date_end) {
                    $query->where('transactions.date', '<=', $dateEnd);
                }
            }

            if ($amountMin = $request->amount_min) {
                $query->where('transactions.amount', '>=', $amountMin);
            }
            if ($amountMax = $request->amount_max) {
                $query->where('transactions.amount', '<=', $amountMax);
            }
        }

        return $query
            ->orderBy('transactions.date', 'desc')
            ->orderBy('transactions.id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function store(array $request, int $userId): Transaction
    {
        $request['user_id'] = $userId;
        $request['date'] = $request['date'] ?? now();
        $request = $this->normalizePayload($request);

        $this->assertPayloadAllowedForUser($userId, $request);

        return Transaction::create($request);
    }

    public function update(Transaction $transaction, array $request): void
    {
        $request = $this->normalizePayload($request);

        $this->assertPayloadAllowedForUser((int) $transaction->user_id, $request);

        $transaction->update($request);
    }

    /**
     * @param  array<string, mixed>  $request
     * @return array<string, mixed>
     */
    private function normalizePayload(array $request): array
    {
        $type = TransactionType::from($request['type']);

        if ($type === TransactionType::TRANSFER) {
            $request['category_id'] = null;
        } else {
            $request['destination_account_id'] = null;
        }

        return $request;
    }

    /**
     * @param  array<string, mixed>  $request
     */
    private function assertPayloadAllowedForUser(int $userId, array $request): void
    {
        $type = TransactionType::from($request['type']);

        $this->assertAccountBelongsToUser($userId, (int) $request['account_id']);

        if ($type === TransactionType::TRANSFER) {
            if ((int) $request['account_id'] === (int) $request['destination_account_id']) {
                throw ValidationException::withMessages([
                    'destination_account_id' => ['A conta de destino deve ser diferente da conta de origem.'],
                ]);
            }

            $this->assertAccountBelongsToUser($userId, (int) $request['destination_account_id']);

            return;
        }

        $this->assertCategoryAllowedForUser($userId, (int) $request['category_id']);

        $category = Category::findOrFail($request['category_id']);
        if ($category->type !== $type) {
            throw ValidationException::withMessages([
                'category_id' => ['O tipo da transação deve coincidir com o tipo da categoria.'],
            ]);
        }
    }

    private function assertAccountBelongsToUser(int $userId, int $accountId): void
    {
        $accountBelongsToUser = Account::where('id', $accountId)
            ->where('user_id', $userId)
            ->exists();

        if (! $accountBelongsToUser) {
            abort(403);
        }
    }

    private function assertCategoryAllowedForUser(int $userId, int $categoryId): void
    {
        $categoryAllowed = Category::where('id', $categoryId)
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhere('is_editable', false);
            })
            ->exists();

        if (! $categoryAllowed) {
            abort(403);
        }
    }

    public function destroy(Transaction $transaction): void
    {
        $transaction->delete();
    }
}
