<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class TransactionService
{
    public function getAllByUser(int $userId, ?Request $request = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = Transaction::where('transactions.user_id', $userId)
            ->with(['category', 'account.institution'])
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->select('transactions.*');

        if ($request) {
            if ($search = $request->search) {
                $query->whereRaw('LOWER(transactions.description) LIKE LOWER(?)', ["%{$search}%"]);
            }

            if ($type = $request->type) {
                $query->where('categories.type', $type);
            }

            if ($accountId = $request->account_id) {
                $query->where('transactions.account_id', $accountId);
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

        //        return Transaction::where('user_id', $userId)
        //            ->with(['category', 'account'])
        //            ->orderBy('date', 'desc')
        //            ->orderBy('id', 'desc')   // Desempate: se tiverem a mesma data, a última criada aparece primeiro
        //            ->paginate($perPage)->withQueryString();
    }

    public function store(array $request, int $userId): Transaction
    {
        $request['user_id'] = $userId;
        $request['date'] = $request['date'] ?? now();

        $this->assertAccountAndCategoryAllowedForUser(
            $userId,
            (int) $request['account_id'],
            (int) $request['category_id'],
        );

        return Transaction::create($request);
    }

    public function update(Transaction $transaction, array $request): void
    {
        $this->assertAccountAndCategoryAllowedForUser(
            (int) $transaction->user_id,
            (int) $request['account_id'],
            (int) $request['category_id'],
        );

        $transaction->update($request);

        //        return $transaction->fresh();
    }

    /**
     * Account must belong to the user; category must be owned by the user or global (non-editable).
     */
    private function assertAccountAndCategoryAllowedForUser(int $userId, int $accountId, int $categoryId): void
    {
        $accountBelongsToUser = Account::where('id', $accountId)
            ->where('user_id', $userId)
            ->exists();

        if (! $accountBelongsToUser) {
            abort(403);
        }

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
