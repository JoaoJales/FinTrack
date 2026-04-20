<?php

namespace Database\Seeders;

use App\Models\Institution;
use Illuminate\Database\Seeder;

class InstitutionSeeder extends Seeder
{
    public function run(): void
    {
        $institutions = [
            ['name' => 'Nubank', 'color' => '#8A05BE', 'image' => 'banks-logos/nubank.svg'],
            ['name' => 'Itaú Unibanco', 'color' => '#EC7000', 'image' => 'banks-logos/itau.svg'],
            ['name' => 'Banco do Brasil', 'color' => '#FCEB00', 'image' => 'banks-logos/banco-do-brasil.svg'],
            ['name' => 'Caixa Econômica', 'color' => '#005CA9', 'image' => 'banks-logos/caixa-economica-federal.svg'],
            ['name' => 'Bradesco', 'color' => '#CC092F', 'image' => 'banks-logos/bradesco.svg'],
            ['name' => 'Santander', 'color' => '#EC0000', 'image' => 'banks-logos/banco-santander.svg'],
            ['name' => 'C6 Bank', 'color' => '#242424', 'image' => 'banks-logos/c6-bank.svg'],
            ['name' => 'Banco Inter', 'color' => '#FF7A00', 'image' => 'banks-logos/inter.svg'],
            ['name' => 'BRB', 'color' => '#009AD6', 'image' => 'banks-logos/brb.svg'],
            ['name' => 'Mercado Pago', 'color' => '#009EE3', 'image' => 'banks-logos/mercado-pago.svg'],
            ['name' => 'XP Investimentos', 'color' => '#000000', 'image' => 'banks-logos/xp-investimentos.svg'],
            ['name' => 'Banrisul', 'color' => '#005CA9', 'image' => 'banks-logos/banrisul-logo.svg'],
            ['name' => 'Pic Pay', 'color' => '#21C25E', 'image' => 'banks-logos/pic-pay.svg'],
        ];

        foreach ($institutions as $institution) {
            Institution::updateOrCreate(
                ['name' => $institution['name']],
                ['color' => $institution['color'], 'image' => $institution['image']]
            );
        }
    }
}
