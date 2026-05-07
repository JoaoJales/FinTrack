<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class WebMiddlewareAndProfileTest extends TestCase
{
    use RefreshDatabase;

    public static function protectedRouteNamesProvider(): array
    {
        return [
            'dashboard' => ['dashboard'],
            'accounts' => ['accounts.index'],
            'transactions' => ['transactions.index'],
            'categories' => ['categories.index'],
            'balance' => ['balance.index'],
        ];
    }

    #[DataProvider('protectedRouteNamesProvider')]
    public function test_guest_is_redirected_to_login(string $routeName): void
    {
        $response = $this->get(route($routeName));

        $response->assertRedirect(route('login'));
    }

    // Desativado enquanto o middleware `verified` está comentado em routes/web.php
    // public function test_unverified_user_cannot_access_verified_routes(): void
    // {
    //     $user = User::factory()->unverified()->create();
    //
    //     $response = $this->actingAs($user)->get(route('dashboard'));
    //
    //     $response->assertRedirect(route('verification.notice'));
    // }

    public function test_profile_update_rejects_duplicate_email(): void
    {
        $userA = User::factory()->create(['email' => 'alice@example.com']);
        User::factory()->create(['email' => 'bob@example.com']);

        $response = $this->actingAs($userA)->from(route('profile.edit'))->patch(
            route('profile.update'),
            [
                'name' => $userA->name,
                'email' => 'bob@example.com',
            ],
        );

        $response->assertRedirect(route('profile.edit'))
            ->assertSessionHasErrors('email');

        $this->assertSame('alice@example.com', $userA->fresh()->email);
    }
}
