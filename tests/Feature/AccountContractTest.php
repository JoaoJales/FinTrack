<?php

namespace Tests\Feature;

use App\Enums\AccountType;
use App\Models\Account;
use App\Models\Institution;
use App\Models\User;
use App\Services\AccountService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AccountContractTest extends TestCase
{
    use RefreshDatabase;

    private function institution(): Institution
    {
        return Institution::factory()->create([
            'name' => 'Test Bank',
            'image' => null,
            'color' => '#112233',
        ]);
    }

    private function validStorePayload(Institution $institution, array $overrides = []): array
    {
        return array_merge([
            'name' => 'Minha conta',
            'initial_balance' => '100,00',
            'account_type' => AccountType::CHECKING->value,
            'institution_id' => $institution->id,
        ], $overrides);
    }

    private function validUpdatePayload(Institution $institution, Account $account, array $overrides = []): array
    {
        return array_merge([
            'name' => $account->name,
            'account_type' => $account->account_type->value,
            'institution_id' => $institution->id,
        ], $overrides);
    }

    public function test_first_stored_account_is_marked_default(): void
    {
        $user = User::factory()->create();
        $institution = $this->institution();

        $this->actingAs($user)->post(
            route('accounts.store'),
            $this->validStorePayload($institution, ['name' => 'Principal']),
        )->assertRedirect(route('accounts.index'));

        $this->assertDatabaseHas('accounts', [
            'user_id' => $user->id,
            'name' => 'Principal',
            'is_default' => true,
        ]);
    }

    public function test_user_can_switch_default_account(): void
    {
        $user = User::factory()->create();
        $institution = $this->institution();

        $this->actingAs($user)->post(route('accounts.store'), $this->validStorePayload($institution, ['name' => 'C1']));
        $this->actingAs($user)->post(route('accounts.store'), $this->validStorePayload($institution, [
            'name' => 'C2',
            'account_type' => AccountType::SAVINGS->value,
        ]));

        $acc1 = Account::query()->where('user_id', $user->id)->where('name', 'C1')->firstOrFail();
        $acc2 = Account::query()->where('user_id', $user->id)->where('name', 'C2')->firstOrFail();

        $this->assertTrue($acc1->is_default);
        $this->assertFalse($acc2->is_default);

        $this->actingAs($user)->patch(
            route('accounts.update', $acc2),
            $this->validUpdatePayload($institution, $acc2, ['is_default' => '1']),
        )->assertRedirect(route('accounts.index'));

        $this->assertFalse($acc1->fresh()->is_default);
        $this->assertTrue($acc2->fresh()->is_default);
    }

    public function test_cannot_clear_default_without_selecting_another(): void
    {
        $user = User::factory()->create();
        $institution = $this->institution();

        $this->actingAs($user)->post(route('accounts.store'), $this->validStorePayload($institution, ['name' => 'C1']));
        $this->actingAs($user)->post(route('accounts.store'), $this->validStorePayload($institution, [
            'name' => 'C2',
            'account_type' => AccountType::SAVINGS->value,
        ]));

        $acc1 = Account::query()->where('user_id', $user->id)->where('name', 'C1')->firstOrFail();
        $this->assertTrue($acc1->is_default);

        $this->actingAs($user)->from(route('accounts.index'))->patch(
            route('accounts.update', $acc1),
            $this->validUpdatePayload($institution, $acc1, ['is_default' => false]),
        )->assertRedirect(route('accounts.index'))->assertSessionHas('error');

        $this->assertTrue($acc1->fresh()->is_default);
    }

    public function test_cannot_delete_default_account_when_user_has_multiple_accounts(): void
    {
        $user = User::factory()->create();
        $institution = $this->institution();

        $this->actingAs($user)->post(route('accounts.store'), $this->validStorePayload($institution, ['name' => 'C1']));
        $this->actingAs($user)->post(route('accounts.store'), $this->validStorePayload($institution, [
            'name' => 'C2',
            'account_type' => AccountType::SAVINGS->value,
        ]));

        $default = Account::query()->where('user_id', $user->id)->where('is_default', true)->firstOrFail();

        $this->actingAs($user)->from(route('accounts.index'))->delete(
            route('accounts.destroy', $default),
        )->assertRedirect(route('accounts.index'))->assertSessionHas('error');

        $this->assertNotNull($default->fresh());
    }

    public function test_user_cannot_update_another_users_account(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $institution = $this->institution();

        $this->actingAs($userA)->post(route('accounts.store'), $this->validStorePayload($institution, ['name' => 'De A']));
        $accountA = Account::query()->where('user_id', $userA->id)->firstOrFail();

        $response = $this->actingAs($userB)->patchJson(
            route('accounts.update', $accountA),
            $this->validUpdatePayload($institution, $accountA, ['name' => 'Hacked']),
        );

        $response->assertForbidden();
        $this->assertSame('De A', $accountA->fresh()->name);
    }

    public function test_user_cannot_delete_another_users_account(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $institution = $this->institution();

        $this->actingAs($userA)->post(route('accounts.store'), $this->validStorePayload($institution, ['name' => 'De A']));
        $accountA = Account::query()->where('user_id', $userA->id)->firstOrFail();

        $response = $this->actingAs($userB)->deleteJson(route('accounts.destroy', $accountA));

        $response->assertForbidden();
        $this->assertNotNull($accountA->fresh());
    }

    public function test_store_fails_when_account_limit_reached(): void
    {
        $user = User::factory()->create();
        $institution = $this->institution();

        for ($i = 1; $i <= 15; $i++) {
            $this->actingAs($user)->post(
                route('accounts.store'),
                $this->validStorePayload($institution, [
                    'name' => "Conta {$i}",
                    'initial_balance' => '0,00',
                ]),
            )->assertRedirect(route('accounts.index'));
        }

        $this->assertSame(15, Account::query()->where('user_id', $user->id)->count());

        $this->actingAs($user)->from(route('accounts.index'))->post(
            route('accounts.store'),
            $this->validStorePayload($institution, [
                'name' => 'Uma a mais',
                'initial_balance' => '0,00',
            ]),
        )->assertRedirect(route('accounts.index'))->assertSessionHasErrors('name');
    }

    public function test_get_accounts_by_user_does_not_trigger_n_plus_one_balance_queries(): void
    {
        $user = User::factory()->create();
        $institution = $this->institution();

        for ($i = 1; $i <= 5; $i++) {
            Account::factory()->for($user)->create([
                'institution_id' => $institution->id,
                'name' => "Conta {$i}",
            ]);
        }

        DB::enableQueryLog();
        DB::flushQueryLog();

        $accounts = app(AccountService::class)->getAccountsByUser($user->id);
        $accounts->each(fn (Account $account) => $account->current_balance);

        $transactionQueries = collect(DB::getQueryLog())
            ->filter(fn (array $query) => str_contains($query['query'], 'transactions'))
            ->count();

        $this->assertLessThanOrEqual(3, $transactionQueries);
    }
}
