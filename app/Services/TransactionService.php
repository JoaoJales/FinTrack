<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Support\Collection;

class TransactionService
{
    public function getAllByUser(int $userId): Collection
    {
        return Transaction::where('user_id', $userId)
            ->with(['category', 'account'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')   // Desempate: se tiverem a mesma data, a última criada aparece primeiro
            ->get();
    }

    public function store(array $request, int $userId): Transaction
    {
        $request['user_id'] = $userId;
        $request['date'] = $request['date'] ?? now();

        $accountBelongsToUser = Account::where('id', $request['account_id'])
            ->where('user_id', $userId)
            ->exists();

        if (!$accountBelongsToUser) {
            abort(403);
        }

        $categoryBelongsToUser = Category::where('id', $request['category_id'])
            ->where(function ($query) use ($userId) {
               $query->where('user_id', $userId) //Categorias do user
                   ->orWhere('is_editable', false); //Ou Globais
            })
            ->exists();


        if (!$categoryBelongsToUser) {
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
