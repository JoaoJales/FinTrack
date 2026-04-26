<?php

namespace App\Enums;

enum AccountType: string
{
    case CHECKING = 'checking';
    case SAVINGS = 'savings';
    case INVESTMENT = 'investment';
    case WALLET = 'wallet';
    case CREDIT = 'credit';

    public static function toArray(): array
    {
        $data = [];

        foreach (self::cases() as $case) {
            $data[(string) $case->value] = $case->label();
        }

        return $data;
    }

    public static function toSelect(?array $accountType = null): array
    {
        $data = [];
        $options = self::cases();

        if ($accountType) {
            $options = $accountType;
        }

        foreach ($options as $case) {
            $data[] = [
                'label' => ($case->label()),
                'value' => $case->value,
            ];
        }

        return $data;
    }

    public static function fromMany(array $values): array
    {
        $data = [];

        foreach ($values as $value) {
            $data[] = self::from(intval($value));
        }

        return $data;
    }

    public function label(): string
    {
        return match ($this) {
            self::CHECKING => 'Conta Corrente',
            self::SAVINGS => 'Poupança',
            self::INVESTMENT => 'Investimentos',
            self::WALLET => 'Carteira',
            self::CREDIT => 'Cartão de Crédito',
        };
    }
}
