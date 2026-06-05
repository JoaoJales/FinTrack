<?php

namespace Tests\Feature;

use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionAuthorizationAndStoreTest extends TestCase
{
    use RefreshDatabase;

    private function storePayload(Account $account, Category $category): array
    {
        return [
            'type' => $category->type->value,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'amount' => '10,00',
            'description' => 'Nova',
        ];
    }

    private function accountForUser(User $user): Account
    {
        return Account::factory()->for($user)->default()->create([
            'name' => 'Conta A',
            'institution_id' => null,
            'initial_balance' => 0,
        ]);
    }

    public function test_user_cannot_update_another_users_transaction(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $account = $this->accountForUser($userA);
        $category = Category::factory()->for($userA)->create(['name' => 'Cat A']);

        $transaction = Transaction::factory()->create([
            'user_id' => $userA->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'type' => $category->type,
            'amount' => 25,
            'description' => 'Original',
            'date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($userB)->patchJson(
            route('transactions.update', $transaction),
            [
                'type' => TransactionType::EXPENSE->value,
                'account_id' => $account->id,
                'category_id' => $category->id,
                'amount' => '99,00',
                'description' => 'Hack',
            ],
        );

        $response->assertForbidden();
        $this->assertSame('Original', $transaction->fresh()->description);
    }

    public function test_user_cannot_delete_another_users_transaction(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $account = $this->accountForUser($userA);
        $category = Category::factory()->for($userA)->create(['name' => 'Cat A']);

        $transaction = Transaction::factory()->create([
            'user_id' => $userA->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'type' => $category->type,
            'amount' => 25,
            'description' => 'Original',
            'date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($userB)->deleteJson(
            route('transactions.destroy', $transaction),
        );

        $response->assertForbidden();
        $this->assertNotNull($transaction->fresh());
    }

    public function test_store_rejects_foreign_account_id(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $accountB = Account::factory()->for($userB)->default()->create([
            'name' => 'Conta B',
            'institution_id' => null,
            'initial_balance' => 0,
        ]);

        $categoryA = Category::factory()->for($userA)->create(['name' => 'Cat A']);
        $accountA = $this->accountForUser($userA);

        $response = $this->actingAs($userA)->postJson(
            route('transactions.store'),
            [
                'type' => TransactionType::EXPENSE->value,
                'account_id' => $accountB->id,
                'category_id' => $categoryA->id,
                'amount' => '10,00',
                'description' => 'X',
            ],
        );

        $response->assertUnprocessable()->assertJsonValidationErrors(['account_id']);
        $this->assertSame(0, Transaction::query()->where('user_id', $userA->id)->count());
    }

    public function test_store_rejects_foreign_private_category_id(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $accountA = $this->accountForUser($userA);
        $categoryB = Category::factory()->for($userB)->create(['name' => 'Cat B']);

        $response = $this->actingAs($userA)->postJson(
            route('transactions.store'),
            [
                'type' => TransactionType::EXPENSE->value,
                'account_id' => $accountA->id,
                'category_id' => $categoryB->id,
                'amount' => '10,00',
                'description' => 'X',
            ],
        );

        $response->assertUnprocessable()->assertJsonValidationErrors(['category_id']);
        $this->assertSame(0, Transaction::query()->where('user_id', $userA->id)->count());
    }

    public function test_store_accepts_global_non_editable_category(): void
    {
        $userA = User::factory()->create();

        $accountA = $this->accountForUser($userA);

        $global = Category::factory()->global()->create([
            'name' => 'Global',
            'type' => TransactionType::EXPENSE,
            'icon' => 'bx-tag',
            'color' => '#111111',
        ]);

        $response = $this->actingAs($userA)->postJson(
            route('transactions.store'),
            $this->storePayload($accountA, $global),
        );

        $response->assertRedirect(route('transactions.index'));
        $this->assertSame(1, Transaction::query()->where('user_id', $userA->id)->count());
        $this->assertSame($global->id, Transaction::query()->where('user_id', $userA->id)->value('category_id'));
    }

    public function test_store_rejects_soft_deleted_category(): void
    {
        $userA = User::factory()->create();

        $accountA = $this->accountForUser($userA);

        $category = Category::factory()->for($userA)->create([
            'name' => 'Trashed',
            'icon' => 'bx-tag',
            'color' => '#222222',
        ]);
        $category->delete();

        $response = $this->actingAs($userA)->postJson(
            route('transactions.store'),
            $this->storePayload($accountA, $category),
        );

        $response->assertUnprocessable()->assertJsonValidationErrors(['category_id']);
    }

    public function test_update_rejects_soft_deleted_category(): void
    {
        $userA = User::factory()->create();

        $accountA = $this->accountForUser($userA);

        $categoryActive = Category::factory()->for($userA)->create([
            'name' => 'Active',
            'icon' => 'bx-tag',
            'color' => '#333333',
        ]);

        $categoryTrashed = Category::factory()->for($userA)->create([
            'name' => 'Trashed',
            'icon' => 'bx-tag',
            'color' => '#444444',
        ]);
        $categoryTrashed->delete();

        $transaction = Transaction::factory()->create([
            'user_id' => $userA->id,
            'account_id' => $accountA->id,
            'category_id' => $categoryActive->id,
            'amount' => 25,
            'description' => 'Original',
            'date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($userA)->patchJson(
            route('transactions.update', $transaction),
            [
                'type' => TransactionType::EXPENSE->value,
                'account_id' => $accountA->id,
                'category_id' => $categoryTrashed->id,
                'amount' => '50,00',
                'description' => 'Updated',
            ],
        );

        $response->assertUnprocessable()->assertJsonValidationErrors(['category_id']);
        $this->assertSame($categoryActive->id, $transaction->fresh()->category_id);
    }

    public function test_update_accepts_global_non_editable_category(): void
    {
        $userA = User::factory()->create();

        $accountA = $this->accountForUser($userA);

        $categoryOwn = Category::factory()->for($userA)->create([
            'name' => 'Own',
            'icon' => 'bx-tag',
            'color' => '#555555',
        ]);

        $global = Category::factory()->global()->create([
            'name' => 'Global',
            'type' => TransactionType::EXPENSE,
            'icon' => 'bx-tag',
            'color' => '#666666',
        ]);

        $transaction = Transaction::factory()->create([
            'user_id' => $userA->id,
            'account_id' => $accountA->id,
            'category_id' => $categoryOwn->id,
            'amount' => 25,
            'description' => 'Original',
            'date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($userA)->patchJson(
            route('transactions.update', $transaction),
            [
                'type' => TransactionType::EXPENSE->value,
                'account_id' => $accountA->id,
                'category_id' => $global->id,
                'amount' => '50,00',
                'description' => 'Com global',
            ],
        );

        $response->assertRedirect(route('transactions.index'));
        $this->assertSame($global->id, $transaction->fresh()->category_id);
    }
}
