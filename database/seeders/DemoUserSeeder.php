<?php

namespace Database\Seeders;

use App\Models\User;

class DemoUserSeeder
{
    const EMAIL = 'demo@fintrack.com';

    const NAME = 'Demo User';

    public static function resolve(): User
    {
        return User::where('email', self::EMAIL)->firstOrFail();
    }
}
