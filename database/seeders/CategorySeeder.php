<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // DESPESAS (Expenses)
            ['name' => 'Alimentação', 'type' => 'expense', 'icon' => 'bx bx-restaurant', 'color' => '#FF6B6B'],
            ['name' => 'Moradia', 'type' => 'expense', 'icon' => 'bx bx-home-alt', 'color' => '#4ECDC4'],
            ['name' => 'Transporte', 'type' => 'expense', 'icon' => 'bx bx-car', 'color' => '#45B7D1'],
            ['name' => 'Saúde', 'type' => 'expense', 'icon' => 'bx bx-health', 'color' => '#19BD70'],
            ['name' => 'Educação', 'type' => 'expense', 'icon' => 'bx bxs-graduation', 'color' => '#EDC737'],
            ['name' => 'Lazer', 'type' => 'expense', 'icon' => 'bx bx-party', 'color' => '#D45757'],
            ['name' => 'Viagem', 'type' => 'expense', 'icon' => 'bx bxs-plane-alt', 'color' => '#F39C12'],
            ['name' => 'Transferências', 'type' => 'expense', 'icon' => 'bx bx-transfer', 'color' => '#34495E'],
            ['name' => 'Pet', 'type' => 'expense', 'icon' => 'bx bxs-dog', 'color' => '#9B59B6'],
            ['name' => 'Dívidas', 'type' => 'expense', 'icon' => 'bx bx-receipt', 'color' => '#C0392B'],
            ['name' => 'Outros (gastos)', 'type' => 'expense', 'icon' => 'bx bx-dots-horizontal-rounded', 'color' => '#7F8C8D'],

            // RECEITAS (Income)
            ['name' => 'Salário', 'type' => 'income', 'icon' => 'bx bx-money', 'color' => '#2ECC71'],
            ['name' => 'Rendimentos', 'type' => 'income', 'icon' => 'bx bx-trending-up', 'color' => '#3A79F0'],
            ['name' => 'Vendas', 'type' => 'income', 'icon' => 'bx bx-store-alt', 'color' => '#A723B0'],
            ['name' => 'Outros (ganhos)', 'type' => 'income', 'icon' => 'bx bx-coin-stack', 'color' => '#16A085'],
            ['name' => 'Presentes', 'type' => 'income', 'icon' => 'bx bx-gift', 'color' => '#F1C40F'],
            ['name' => 'Transferências', 'type' => 'income', 'icon' => 'bx bx-transfer-alt', 'color' => '#2980B9'],
            ['name' => 'Outros (ganhos)', 'type' => 'income', 'icon' => 'bx bx-coin-stack', 'color' => '#16A085'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                [
                    'name' => $category['name'],
                    'type' => $category['type'],
                    'user_id' => null, // Categoria Global (do sistema)
                ],
                [
                    'icon' => $category['icon'],
                    'color' => $category['color'],
                    'is_editable' => false, // Trava para edição
                ]
            );
        }
    }
}
