<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoCategorySeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $user = DemoUserSeeder::resolve();

        $categories = [
            // DESPESAS personalizadas
            ['name' => 'Streaming',         'type' => 'expense', 'icon' => 'bx bx-tv',           'color' => '#8E44AD'],
            ['name' => 'Restaurante',       'type' => 'expense', 'icon' => 'bx bx-food-menu',    'color' => '#E67E22'],
            ['name' => 'Mercado',           'type' => 'expense', 'icon' => 'bx bx-cart',         'color' => '#27AE60'],
            ['name' => 'Farmácia',          'type' => 'expense', 'icon' => 'bx bx-plus-medical', 'color' => '#2ECC71'],
            ['name' => 'Gasolina',          'type' => 'expense', 'icon' => 'bx bx-gas-pump',     'color' => '#F39C12'],

            // RECEITAS personalizadas
            ['name' => 'Freelance',         'type' => 'income',  'icon' => 'bx bx-laptop',       'color' => '#3498DB'],
            ['name' => 'Aluguel recebido',  'type' => 'income',  'icon' => 'bx bx-building-house', 'color' => '#1ABC9C'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'name' => $category['name'],
                    'type' => $category['type'],
                ],
                [
                    'icon' => $category['icon'],
                    'color' => $category['color'],
                    'is_editable' => true,
                ]
            );
        }
    }
}
