<?php

namespace Tests\Unit\Support;

use App\Enums\TransactionType;
use App\Support\TransactionAggregates;
use Tests\TestCase;

class TransactionAggregatesTest extends TestCase
{
    public function test_income_and_expense_sql_use_transactions_type(): void
    {
        $this->assertStringContainsString('transactions.type', TransactionAggregates::incomeSumSql());
        $this->assertStringContainsString('transactions.type', TransactionAggregates::expenseSumSql());
        $this->assertStringContainsString(TransactionType::INCOME->value, TransactionAggregates::incomeSumSql());
        $this->assertStringContainsString(TransactionType::EXPENSE->value, TransactionAggregates::expenseSumSql());
        $this->assertStringNotContainsString('categories.type', TransactionAggregates::incomeExpenseSelectSql());
    }

    public function test_account_balance_sql_includes_transfer_legs(): void
    {
        $sql = TransactionAggregates::accountBalanceSelectSql(42);

        $this->assertStringContainsString('total_transfer_out', $sql);
        $this->assertStringContainsString('total_transfer_in', $sql);
        $this->assertStringContainsString('account_id = 42', $sql);
        $this->assertStringContainsString('destination_account_id = 42', $sql);
    }
}
