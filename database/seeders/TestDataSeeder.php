<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(AccountSeeder::class);
        $this->call(DemoCategorySeeder::class);
        $this->call(TransactionSeeder::class);
    }
}
