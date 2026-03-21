<?php

namespace App\Services;

use App\Models\Account;
use Illuminate\Support\Collection;

class AccountService
{

    public function getAccountsByUser(int $userId): Collection
    {
        //Busca uma Colletion de contas junto das suas institutions
        return Account::where('user_id', $userId)
            ->with('institution')
            ->get();
    }

    public function findAccountByIdAndUserId(int $accountId, int $userId): Account
    {
        return Account::where('id', $accountId)
            ->where('user_id', $userId)
            ->with('institution')
            ->firstOrFail(); // se não encontrar, já lança 404 automaticamente — sem if necessário
    }

    public function store(array $request, int $userId): Account
    {
        //Adiciona o id do user ao dados da request
        $request['user_id'] = $userId;

        return Account::create($request);
    }

    public function update(Account $account, array $request): void
    {
        $account->update($request);

        // busca o $account atualizado do banco + carrega o relacionamento institution
//        return $account->fresh('institution');
    }

    public function destroy(Account $account): void
    {
        $account->delete();
    }

    public function getTrashedByUser(int $userId): Collection
    {
        return Account::onlyTrashed()  //Retorna as contas "deletadas" (soft) do user
            ->where('user_id', $userId)
            ->with('institution')
            ->get();
    }
}
