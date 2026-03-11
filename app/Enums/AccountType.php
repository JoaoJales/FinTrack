<?php

namespace App\Enums;

enum AccountType: string
{
    case CHECKING = 'checking';
    case SAVINGS = 'savings';
    case INVESTMENT = 'investment';
    case WALLET = 'wallet';
    case CREDIT = 'credit';

    public function label(): string {
        return match ($this) {
            self::CHECKING => 'Conta Corrente',
            self::SAVINGS => 'Poupança',
            self::INVESTMENT => 'Investimentos',
            self::WALLET => 'Carteira',
            self::CREDIT => 'Cartão de Crédito',
        };
    }
}
