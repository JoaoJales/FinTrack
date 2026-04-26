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

        $accountBelongsToUser = Account::where('id', $request['account_id'])
            ->where('user_id', $userId)
            ->exists();

        if (! $accountBelongsToUser) {
            abort(403);
        }

        $categoryBelongsToUser = Category::where('id', $request['category_id'])
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId) // Categorias do user
                    ->orWhere('is_editable', false); // Ou Globais
            })
            ->exists();

        if (! $categoryBelongsToUser) {
            abort(403);
        }

        return Transaction::create($request);
    }

    public function update(Transaction $transaction, array $request): void
    {
        $transaction->update($request);

        //        return $transaction->fresh();
    }

    public function destroy(Transaction $transaction): void
    {
        $transaction->delete();
    }
}
