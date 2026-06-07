<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Transaction;
use App\Support\TransactionAggregates;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class AccountService
{
    public function getAccountsByUser(int $userId): Collection
    {
        $accounts = Account::where('user_id', $userId)
            ->with('institution')
            ->orderBy('id')
            ->get();

        return $this->hydrateCurrentBalances($accounts);
    }

    public function getTotalBalance(Collection $accounts): float
    {
        return (float) $accounts->sum('current_balance');
    }

    private function hydrateCurrentBalances(Collection $accounts): Collection
    {
        if ($accounts->isEmpty()) {
            return $accounts;
        }

        $accountIds = $accounts->pluck('id');

        $incomeExpense = Transaction::query()
            ->whereIn('account_id', $accountIds)
            ->selectRaw('account_id, '.TransactionAggregates::incomeExpenseSelectSql())
            ->groupBy('account_id')
            ->get()
            ->keyBy('account_id');

        $transferOut = Transaction::query()
            ->whereIn('account_id', $accountIds)
            ->where('type', TransactionType::TRANSFER->value)
            ->selectRaw('account_id, SUM(amount) as total_transfer_out')
            ->groupBy('account_id')
            ->pluck('total_transfer_out', 'account_id');

        $transferIn = Transaction::query()
            ->whereIn('destination_account_id', $accountIds)
            ->where('type', TransactionType::TRANSFER->value)
            ->selectRaw('destination_account_id as account_id, SUM(amount) as total_transfer_in')
            ->groupBy('destination_account_id')
            ->pluck('total_transfer_in', 'account_id');

        return $accounts->each(function (Account $account) use ($incomeExpense, $transferOut, $transferIn) {
            $row = $incomeExpense->get($account->id);

            $balance = (float) $account->initial_balance
                + (float) ($row->total_income ?? 0)
                - (float) ($row->total_expense ?? 0)
                - (float) ($transferOut->get($account->id) ?? 0)
                + (float) ($transferIn->get($account->id) ?? 0);

            $account->setAttribute('current_balance', $balance);
        });
    }

    public function store(array $request, int $userId): Account
    {
        // Adiciona o id do user ao dados da request
        $request['user_id'] = $userId;

        if (! Account::where('user_id', $userId)->exists()) {
            $request['is_default'] = true;
        } elseif (! empty($request['is_default'])) {
            Account::default()->where('user_id', $userId)->update(['is_default' => false]);
            $request['is_default'] = true;
        } else {
            $request['is_default'] = false;
        }

        return Account::create($request);
    }

    public function update(Account $account, array $request): void
    {
        if (array_key_exists('is_default', $request)) {
            $wantsDefault = (bool) $request['is_default'];

            if (! $wantsDefault && $account->is_default) {
                throw new InvalidArgumentException(
                    'Não é possível desmarcar sua conta principal diretamente. Defina outra conta como principal.'
                );
            }

            if ($wantsDefault && ! $account->is_default) {
                Account::default()
                    ->where('user_id', $account->user_id)
                    ->update(['is_default' => false]);
            }

            $request['is_default'] = $wantsDefault;
        }
        $account->update($request);
    }

    public function destroy(Account $account): void
    {
        $totalAccounts = Account::where('user_id', $account->user_id)->count();
        if ($account->is_default && $totalAccounts > 1) {
            throw new InvalidArgumentException('Não é possível deletar sua conta principal enquanto existir outras contas');
        }

        $account->delete();
    }
}
