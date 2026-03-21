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
        $accounts = $this->getAccountsWithBalance($userId);
        $transactions = $this->getLastTransactions($userId);

        return [
            'accounts' => $accounts,
            'total_balance' => $this->getTotalBalance($accounts),
            'last_transactions' => $transactions,
            'expenses_by_category' => $this->getExpensesByCategory($userId),
            'month_performace' => $this->getCurrentMonthPerformance($userId),
        ];
    }

    private function getAccountsWithBalance(int $userId): Collection
    {
        return $this->accountService->getAccountsByUser($userId);
    }

    private function getLastTransactions(int $userId): Collection
    {
        return Transaction::where('user_id', $userId)
        ->with(['category', 'account'])
        ->orderBy('date', 'desc')
        ->orderBy('id', 'desc')
        ->limit(10)
        ->get();
    }

    private function getTotalBalance(Collection $accounts): float
    {
        return (float) $accounts->sum('current_balance');
    }

    private function getExpensesByCategory(int $userId): Collection
    {
        return Transaction::where('transactions.user_id', $userId)
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('categories.type', 'expense')
            ->whereMonth('transactions.date', now()->month)
            ->whereYear('transactions.date', now()->year)
            ->selectRaw('categories.name, SUM(transactions.amount) as total')
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total', 'desc')
            ->get();
    }

    private function getCurrentMonthPerformance(int $userId)
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


}
