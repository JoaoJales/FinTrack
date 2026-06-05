<?php

namespace App\Support;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Builder;

final class TransactionAggregates
{
    public static function incomeSumSql(
        string $amount = 'transactions.amount',
        string $type = 'transactions.type',
    ): string {
        return "SUM(CASE WHEN {$type} = '".TransactionType::INCOME->value."' THEN {$amount} ELSE 0 END)";
    }

    public static function expenseSumSql(
        string $amount = 'transactions.amount',
        string $type = 'transactions.type',
    ): string {
        return "SUM(CASE WHEN {$type} = '".TransactionType::EXPENSE->value."' THEN {$amount} ELSE 0 END)";
    }

    public static function incomeExpenseSelectSql(): string
    {
        return self::incomeSumSql().' as total_income, '.self::expenseSumSql().' as total_expense';
    }

    public static function netResultSql(): string
    {
        return self::incomeSumSql().' - '.self::expenseSumSql().' as result';
    }

    public static function excludeTransfers(Builder $query): Builder
    {
        return $query->whereIn('transactions.type', [
            TransactionType::INCOME->value,
            TransactionType::EXPENSE->value,
        ]);
    }

    public static function transferOutSumSql(int $accountId): string
    {
        return "SUM(CASE WHEN transactions.account_id = {$accountId} AND transactions.type = '"
            .TransactionType::TRANSFER->value."' THEN transactions.amount ELSE 0 END)";
    }

    public static function transferInSumSql(int $accountId): string
    {
        return "SUM(CASE WHEN transactions.destination_account_id = {$accountId} AND transactions.type = '"
            .TransactionType::TRANSFER->value."' THEN transactions.amount ELSE 0 END)";
    }

    public static function accountBalanceSelectSql(int $accountId): string
    {
        $income = "SUM(CASE WHEN transactions.account_id = {$accountId} AND transactions.type = '"
            .TransactionType::INCOME->value."' THEN transactions.amount ELSE 0 END)";
        $expense = "SUM(CASE WHEN transactions.account_id = {$accountId} AND transactions.type = '"
            .TransactionType::EXPENSE->value."' THEN transactions.amount ELSE 0 END)";

        return "{$income} as total_income, {$expense} as total_expense, "
            .self::transferOutSumSql($accountId).' as total_transfer_out, '
            .self::transferInSumSql($accountId).' as total_transfer_in';
    }
}
