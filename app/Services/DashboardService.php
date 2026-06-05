<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Support\TransactionAggregates;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function __construct(
        private AccountService $accountService
    ) {}

    public function getDashboardData(int $userId): array
    {
        $accounts = $this->accountService->getAccountsByUser($userId);
        $transactions = $this->getLastTransactions($userId);

        return [
            'accounts' => $accounts,
            'total_balance' => $this->accountService->getTotalBalance($accounts),
            'balance_variation' => $this->getBalanceVariation($userId),
            'last_transactions' => $transactions,
            'expenses_by_category' => $this->getByCategory($userId, TransactionType::EXPENSE->value),
            'incomes_by_category' => $this->getByCategory($userId, TransactionType::INCOME->value),
            'month_performace' => $this->getCurrentMonthPerformance($userId),
            'monthly_performance' => $this->getMonthlyPerformance($userId),
        ];
    }

    private function getLastTransactions(int $userId): Collection
    {
        return Transaction::where('user_id', $userId)
            ->with(['category', 'account.institution', 'destinationAccount.institution'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();
    }

    private function getByCategory(int $userId, string $type): Collection
    {
        return Transaction::where('transactions.user_id', $userId)
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.type', $type)
            ->whereMonth('transactions.date', now()->month)
            ->whereYear('transactions.date', now()->year)
            ->selectRaw('categories.name, categories.color, SUM(transactions.amount) as total')
            ->groupBy('categories.id', 'categories.name', 'categories.color')
            ->orderBy('total', 'desc')
            ->get();
    }

    private function getCurrentMonthPerformance(int $userId): ?Transaction
    {
        return Transaction::where('transactions.user_id', $userId)
            ->tap(fn ($q) => TransactionAggregates::excludeTransfers($q))
            ->whereBetween('transactions.date', [now()->startOfMonth(), now()->endOfMonth()])
            ->selectRaw(TransactionAggregates::incomeExpenseSelectSql())
            ->first();
    }

    /**
     * Retorna entradas e saídas dos últimos 6 meses.
     */
    private function getMonthlyPerformance(int $userId): Collection
    {
        $monthKey = $this->monthKeySql();

        return Transaction::where('transactions.user_id', $userId)
            ->tap(fn ($q) => TransactionAggregates::excludeTransfers($q))
            ->where('transactions.date', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("
            {$monthKey} as month,
            ".TransactionAggregates::incomeExpenseSelectSql().'
        ')
            ->groupByRaw($monthKey)
            ->orderBy('month')
            ->get();
    }

    private function monthKeySql(): string
    {
        return match (DB::connection()->getDriverName()) {
            'pgsql' => "TO_CHAR(transactions.date, 'YYYY-MM')",
            'sqlite' => "strftime('%Y-%m', transactions.date)",
            default => "DATE_FORMAT(transactions.date, '%Y-%m')",
        };
    }

    private function getBalanceVariation(int $userId): array
    {
        // Resultado líquido do mês atual (entradas - saídas)
        $current = $this->getMonthResult($userId, now()->month, now()->year);

        // Resultado líquido do mês anterior
        $previous = $this->getMonthResult($userId, now()->subMonth()->month, now()->subMonth()->year);

        if ($previous == 0) {
            return ['percentage' => null, 'positive' => true];
        }

        $percentage = round((($current - $previous) / abs($previous)) * 100, 1);

        return [
            'percentage' => abs($percentage),
            'positive' => $percentage >= 0,
        ];
    }

    public function getMonthResult(int $userId, int $month, int $year): float
    {
        $result = Transaction::where('transactions.user_id', $userId)
            ->tap(fn ($q) => TransactionAggregates::excludeTransfers($q))
            ->whereMonth('transactions.date', $month)
            ->whereYear('transactions.date', $year)
            ->selectRaw(TransactionAggregates::netResultSql())
            ->value('result');

        return (float) ($result ?? 0);
    }
}
