<?php

namespace Database\Seeders;

use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class TransactionSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $user = DemoUserSeeder::resolve();

        $nubank = Account::where('user_id', $user->id)->where('name', 'Nubank Conta')->firstOrFail();
        $itau = Account::where('user_id', $user->id)->where('name', 'Itaú Corrente')->firstOrFail();
        $bb = Account::where('user_id', $user->id)->where('name', 'BB Poupança')->firstOrFail();

        // Busca globais (user_id null) + personalizadas do usuário
        $expenses = Category::where('type', TransactionType::EXPENSE->value)
            ->where(fn ($q) => $q->whereNull('user_id')->orWhere('user_id', $user->id))
            ->get();

        $incomes = Category::where('type', TransactionType::INCOME->value)
            ->where(fn ($q) => $q->whereNull('user_id')->orWhere('user_id', $user->id))
            ->get();

        abort_if($expenses->isEmpty(), 500, 'Nenhuma categoria de gasto encontrada. Rode CategorySeeder e DemoCategorySeeder antes.');
        abort_if($incomes->isEmpty(), 500, 'Nenhuma categoria de ganho encontrada. Rode CategorySeeder e DemoCategorySeeder antes.');

        for ($monthsAgo = 5; $monthsAgo >= 0; $monthsAgo--) {
            $ref = Carbon::now()->subMonths($monthsAgo);

            // Receitas — globais + personalizadas
            $this->create($user->id, $nubank->id, $incomes, 'Salário', $ref->copy()->day(5), random_int(450000, 600000));
            $this->create($user->id, $itau->id, $incomes, 'Freelance', $ref->copy()->day(random_int(10, 20)), random_int(80000, 200000));
            $this->create($user->id, $bb->id, $incomes, 'Rendimentos', $ref->copy()->day(1), random_int(500, 3000));

            // Despesas fixas — globais
            $this->create($user->id, $nubank->id, $expenses, 'Moradia', $ref->copy()->day(1), 150000);
            $this->create($user->id, $nubank->id, $expenses, 'Academia', $ref->copy()->day(8), 9900);
            $this->create($user->id, $nubank->id, $expenses, 'Streaming', $ref->copy()->day(18), random_int(2490, 5990));

            // Despesas variáveis — personalizadas
            $this->create($user->id, $nubank->id, $expenses, 'Mercado', $ref->copy()->day(random_int(1, 28)), random_int(30000, 80000));
            $this->create($user->id, $nubank->id, $expenses, 'Restaurante', $ref->copy()->day(random_int(1, 28)), random_int(5000, 20000));
            $this->create($user->id, $nubank->id, $expenses, 'Farmácia', $ref->copy()->day(random_int(1, 28)), random_int(3000, 15000));
            $this->create($user->id, $itau->id, $expenses, 'Gasolina', $ref->copy()->day(random_int(1, 28)), random_int(15000, 40000));

            if (random_int(0, 1)) {
                $this->create($user->id, $nubank->id, $expenses, 'Lazer', $ref->copy()->day(random_int(1, 28)), random_int(10000, 40000));
            }

            if (random_int(0, 1)) {
                $this->create($user->id, $nubank->id, $expenses, 'Saúde', $ref->copy()->day(random_int(1, 28)), random_int(2000, 8000));
            }
        }
    }

    private function create(
        int $userId,
        int $accountId,
        Collection $categories,
        string $description,
        Carbon $date,
        int $amountInCents
    ): void {
        $category = $categories->first(
            fn ($c) => str_contains(strtolower($c->name), strtolower($description))
        ) ?? $categories->random();

        Transaction::firstOrCreate(
            [
                'user_id' => $userId,
                'account_id' => $accountId,
                'category_id' => $category->id,
                'description' => $description,
                'date' => $date->format('Y-m-d'),
            ],
            [
                'amount' => $amountInCents / 100,
            ]
        );
    }
}
