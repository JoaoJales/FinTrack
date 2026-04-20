<?php

namespace App\Services;

use App\Models\Account;
use App\Models\User;
use http\Exception\InvalidArgumentException;
use Illuminate\Support\Collection;

class AccountService
{

    public function getAccountsByUser(int $userId): Collection
    {
        //Busca uma Colletion de contas junto das suas institutions
        return Account::where('user_id', $userId)
            ->with('institution')
            ->orderBy('id')
            ->get();
    }

    public function getTotalBalance(Collection $accounts): float
    {
        return (float) $accounts->sum('current_balance');
    }

    public function store(array $request, int $userId): Account
    {
        //Adiciona o id do user ao dados da request
        $request['user_id'] = $userId;

        if (!Account::where('user_id', $userId)->exists()) {
            $request['is_default'] = true;
        } elseif (!empty($request['is_default'])) {
            Account::default()->where('user_id', $userId)->update(['is_default' => false]);
            $request['is_default'] = true;
        } else {
            $request['is_default'] = false;
        }

        return Account::create($request);
    }

    public function update(Account $account, array $request): void
    {
        $wantsDefault = !empty($request['is_default']);

        if (!$wantsDefault && $account->is_default) {
            throw new InvalidArgumentException('Não é possível desmarcar sua conta principal diretamente. Defina outra conta como principal.');
        }
        if ($wantsDefault && !$account->is_default) {
            Account::default()->where('user_id', $account->user_id)->update(['is_default' => false]);
        }
        $request['is_default'] = $wantsDefault;
        $account->update($request);

        // busca o $account atualizado do banco + carrega o relacionamento institution
//        return $account->fresh('institution');
    }

    public function destroy(Account $account): void
    {
        $totalAccounts = Account::where('user_id', $account->user_id)->count();
        if ($account->is_default && $totalAccounts > 1) {
            throw new InvalidArgumentException("Não é possível deletar sua conta principal enquanto existir outras contas");
        }

        $account->delete();
    }
}
