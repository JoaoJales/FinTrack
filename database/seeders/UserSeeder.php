<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::firstOrCreate(
            ['email' => DemoUserSeeder::EMAIL],
            [
                'name'              => DemoUserSeeder::NAME,
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }
}
