<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Collection;

class DashboardService
{
    public function __construct(
        private AccountService $accountService
    )
    {
    }

    public function getDashboardData(int $userId): array
    {
        $accounts = $this->accountService->getAccountsByUser($userId);
        $transactions = $this->getLastTransactions($userId);

        return [
            'accounts' => $accounts,
            'total_balance' => $this->accountService->getTotalBalance($accounts),
            'balance_variation'    => $this->getBalanceVariation($userId),
            'last_transactions' => $transactions,
            'expenses_by_category'  => $this->getByCategory($userId, 'expense'),
            'incomes_by_category'   => $this->getByCategory($userId, 'income'),
            'month_performace' => $this->getCurrentMonthPerformance($userId),
            'monthly_performance' => $this->getMonthlyPerformance($userId),
        ];
    }

    private function getLastTransactions(int $userId): Collection
    {
        return Transaction::where('user_id', $userId)
        ->with(['category', 'account'])
        ->orderBy('date', 'desc')
        ->orderBy('id', 'desc')
        ->limit(5)
        ->get();
    }



    private function getByCategory(int $userId, string $type): Collection
    {
        return Transaction::where('transactions.user_id', $userId)
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('categories.type', $type)
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
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->whereBetween('transactions.date', [now()->startOfMonth(), now()->endOfMonth()])
            ->selectRaw("
                SUM (CASE WHEN categories.type = 'income' THEN transactions.amount ELSE 0 END) as total_income,
                SUM(CASE WHEN categories.type = 'expense' THEN transactions.amount ELSE 0 END) as total_expense
            ")
            ->first();
    }

    /**
     * Retorna entradas e saídas dos últimos 6 meses.
     */
    private function getMonthlyPerformance(int $userId): Collection
    {
        return Transaction::where('transactions.user_id', $userId)
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.date', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("
            TO_CHAR(transactions.date, 'YYYY-MM') as month,
            SUM(CASE WHEN categories.type = 'income'  THEN transactions.amount ELSE 0 END) as total_income,
            SUM(CASE WHEN categories.type = 'expense' THEN transactions.amount ELSE 0 END) as total_expense
        ")
            ->groupByRaw("TO_CHAR(transactions.date, 'YYYY-MM')")
            ->orderBy('month')
            ->get();
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
            'positive'   => $percentage >= 0,
        ];
    }

    private function getMonthResult(int $userId, int $month, int $year): float
    {
        $result = Transaction::where('transactions.user_id', $userId)
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->whereMonth('transactions.date', $month)
            ->whereYear('transactions.date', $year)
            ->selectRaw("
            SUM(CASE WHEN categories.type = 'income'  THEN transactions.amount ELSE 0 END) -
            SUM(CASE WHEN categories.type = 'expense' THEN transactions.amount ELSE 0 END)
            as result
        ")
            ->value('result');

        return (float) ($result ?? 0);
    }


}
