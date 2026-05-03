<?php

namespace App\Services;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BalanceService
{
    public function __construct(
        private AccountService $accountService
    ) {}

    public function getBalanceDetails(int $userId): array
    {
        $accounts = $this->accountService->getAccountsByUser($userId);

        return [
            'total_balance' => $this->accountService->getTotalBalance($accounts),
            'year_summary' => $this->getYearSummary($userId),
            'monthly_results' => $this->getMonthlyResults($userId),
            'line_chart' => $this->getLineChartData($userId),
        ];
    }

    // Resultado acumulado do ano atual
    private function getYearSummary(int $userId): array
    {
        $result = Transaction::where('transactions.user_id', $userId)
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->whereYear('transactions.date', now()->year)
            ->selectRaw("
                SUM(CASE WHEN categories.type = 'income'  THEN transactions.amount ELSE 0 END) as total_income,
                SUM(CASE WHEN categories.type = 'expense' THEN transactions.amount ELSE 0 END) as total_expense
            ")
            ->first();

        $income = (float) ($result->total_income ?? 0);
        $expense = (float) ($result->total_expense ?? 0);
        $net = $income - $expense;

        return [
            'income' => $income,
            'expense' => $expense,
            'net' => $net,
            'positive' => $net >= 0,
            'year' => now()->year,
        ];
    }

    // Resultado por mês (PostgreSQL)
    private function getMonthlyResults(int $userId): Collection
    {

        return Transaction::where('transactions.user_id', $userId)
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->whereYear('transactions.date', now()->year)
            ->selectRaw("
                EXTRACT(MONTH FROM transactions.date) as month_number,
                SUM(CASE WHEN categories.type = 'income'  THEN transactions.amount ELSE 0 END) as total_income,
                SUM(CASE WHEN categories.type = 'expense' THEN transactions.amount ELSE 0 END) as total_expense
            ")
            ->groupByRaw('EXTRACT(MONTH FROM transactions.date)')
            ->orderByRaw('EXTRACT(MONTH FROM transactions.date)')
            ->get()
            ->map(function ($row) {
                $month = (int) $row->month_number;
                $net = (float) $row->total_income - (float) $row->total_expense;
                $label = Carbon::createFromDate(now()->year, $month, 1)
                    ->translatedFormat('F/Y');

                return [
                    'month_number' => $month,
                    'month_label' => ucfirst($label),
                    'income' => (float) $row->total_income,
                    'expense' => (float) $row->total_expense,
                    'net' => $net,
                    'positive' => $net >= 0,
                ];
            });
    }

    // Dados para gráfico
    private function getLineChartData(int $userId): array
    {
        $rows = Transaction::where('transactions.user_id', $userId)
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->whereYear('transactions.date', now()->year)
            ->selectRaw("
                EXTRACT(MONTH FROM transactions.date) as month_number,
                SUM(CASE WHEN categories.type = 'income'  THEN transactions.amount ELSE 0 END) as total_income,
                SUM(CASE WHEN categories.type = 'expense' THEN transactions.amount ELSE 0 END) as total_expense
            ")
            ->groupByRaw('EXTRACT(MONTH FROM transactions.date)')
            ->orderByRaw('EXTRACT(MONTH FROM transactions.date)')
            ->get()
            ->keyBy(function ($row) {
                return (int) $row->month_number;
            });

        $labels = [];
        $income = [];
        $expense = [];
        $net = [];

        for ($m = 1; $m <= 12; $m++) {
            $labels[] = Carbon::create()->month($m)->translatedFormat('M');

            $row = $rows->get($m);

            $inc = $row ? (float) $row->total_income : 0;
            $exp = $row ? (float) $row->total_expense : 0;

            $income[] = round($inc, 2);
            $expense[] = round($exp, 2);
            $net[] = round($inc - $exp, 2);
        }

        return compact('labels', 'income', 'expense', 'net');
    }
}
