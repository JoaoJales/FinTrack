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
            ['name' => 'Saúde', 'type' => 'expense', 'icon' => 'bx bx-health', 'color' => '#96CEB4'],
            ['name' => 'Educação', 'type' => 'expense', 'icon' => 'bx bxs-graduation', 'color' => '#FFEEAD'],
            ['name' => 'Lazer', 'type' => 'expense', 'icon' => 'bx bx-party', 'color' => '#D4A5A5'],

            // RECEITAS (Income)
            ['name' => 'Salário', 'type' => 'income', 'icon' => 'bx bx-money', 'color' => '#2ECC71'],
            ['name' => 'Rendimentos', 'type' => 'income', 'icon' => 'bx bx-trending-up', 'color' => '#27AE60'],
            ['name' => 'Vendas', 'type' => 'income', 'icon' => 'bx bx-store-alt', 'color' => '#1ABC9C'],
            ['name' => 'Outras Receitas', 'type' => 'income', 'icon' => 'bx bx-coin-stack', 'color' => '#16A085'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                [
                    'name' => $category['name'],
                    'type' => $category['type'],
                    'user_id' => null // Categoria Global (do sistema)
                ],
                [
                    'icon' => $category['icon'],
                    'color' => $category['color'],
                    'is_editable' => false // Trava para edição
                ]
            );
        }
    }
}
