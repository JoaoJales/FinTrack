<?php

namespace App\Enums;

enum TransactionType: string
{
    case INCOME = 'income';
    case EXPENSE = 'expense';
    case TRANSFER = 'transfer';

    public function label(): string
    {
        return match ($this) {
            self::INCOME => 'Ganho',
            self::EXPENSE => 'Gasto',
            self::TRANSFER => 'Transferência',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::INCOME => 'text-emerald-600',
            self::EXPENSE => 'text-rose-600',
            self::TRANSFER => 'text-blue-600',
        };
    }
}
