<?php

namespace App\Services;

use App\Models\Transaction;
use App\Support\TransactionAggregates;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
            ->tap(fn ($q) => TransactionAggregates::excludeTransfers($q))
            ->whereYear('transactions.date', now()->year)
            ->selectRaw(TransactionAggregates::incomeExpenseSelectSql())
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

    // Agregação mensal do ano corrente (expressões compatíveis apenas com PostgreSQL)
    private function getMonthlyResults(int $userId): Collection
    {
        $monthNumber = $this->monthNumberSql();

        return Transaction::where('transactions.user_id', $userId)
            ->tap(fn ($q) => TransactionAggregates::excludeTransfers($q))
            ->whereYear('transactions.date', now()->year)
            ->selectRaw("
                {$monthNumber} as month_number,
                ".TransactionAggregates::incomeExpenseSelectSql().'
            ')
            ->groupByRaw($monthNumber)
            ->orderByRaw($monthNumber)
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
        $monthNumber = $this->monthNumberSql();

        $rows = Transaction::where('transactions.user_id', $userId)
            ->tap(fn ($q) => TransactionAggregates::excludeTransfers($q))
            ->whereYear('transactions.date', now()->year)
            ->selectRaw("
                {$monthNumber} as month_number,
                ".TransactionAggregates::incomeExpenseSelectSql().'
            ')
            ->groupByRaw($monthNumber)
            ->orderByRaw($monthNumber)
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

    private function monthNumberSql(): string
    {
        return match (DB::connection()->getDriverName()) {
            'pgsql' => 'EXTRACT(MONTH FROM transactions.date)',
            'sqlite' => "CAST(strftime('%m', transactions.date) AS INTEGER)",
            default => 'MONTH(transactions.date)',
        };
    }
}
