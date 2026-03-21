<?php

namespace Database\Seeders;

use App\Models\Institution;
use Illuminate\Database\Seeder;

class InstitutionSeeder extends Seeder
{
    public function run(): void
    {
        $institutions = [
            ['name' => 'Nubank', 'color' => '#8A05BE'],
            ['name' => 'Itaú Unibanco', 'color' => '#EC7000'],
            ['name' => 'Banco do Brasil', 'color' => '#FCEB00'],
            ['name' => 'Caixa Econômica', 'color' => '#005CA9'],
            ['name' => 'Bradesco', 'color' => '#CC092F'],
            ['name' => 'Santander', 'color' => '#EC0000'],
            ['name' => 'Banco Inter', 'color' => '#FF7A00'],
            ['name' => 'C6 Bank', 'color' => '#242424'],
        ];

        foreach ($institutions as $institution) {
            Institution::updateOrCreate(
                ['name' => $institution['name']],
                ['color' => $institution['color']]
            );
        }
    }
}
