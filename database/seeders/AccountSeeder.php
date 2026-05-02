<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Institution;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AccountSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $user = DemoUserSeeder::resolve();

        $nubank = Institution::where('name', 'like', '%Nubank%')->first()
            ?? Institution::first();

        $itau = Institution::where('name', 'like', '%Itaú%')->first()
            ?? Institution::first();

        $bb = Institution::where('name', 'like', '%Brasil%')->first()
            ?? Institution::first();

        Account::firstOrCreate(
            ['user_id' => $user->id, 'name' => 'Nubank Conta'],
            [
                'institution_id'  => $nubank->id,
                'account_type'    => 'checking',
                'initial_balance' => 0,
                'is_default'      => true,
            ]
        );

        Account::firstOrCreate(
            ['user_id' => $user->id, 'name' => 'Itaú Corrente'],
            [
                'institution_id'  => $itau->id,
                'account_type'    => 'checking',
                'initial_balance' => 0,
                'is_default'      => false,
            ]
        );

        Account::firstOrCreate(
            ['user_id' => $user->id, 'name' => 'BB Poupança'],
            [
                'institution_id'  => $bb->id,
                'account_type'    => 'savings',
                'initial_balance' => 0,
                'is_default'      => false,
            ]
        );
    }
}
