<?php

namespace Tests\Feature;

use App\Enums\AccountType;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionUpdateIdorTest extends TestCase
{
    use RefreshDatabase;

    private function payload(Transaction $transaction): array
    {
        return [
            'account_id' => $transaction->account_id,
            'category_id' => $transaction->category_id,
            'amount' => '50,00',
            'description' => 'Test',
        ];
    }

    public function test_user_cannot_point_transaction_update_to_another_users_account(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $accountA = Account::factory()->for($userA)->default()->create([
            'name' => 'Conta A',
            'institution_id' => null,
            'initial_balance' => 0,
        ]);

        $accountB = Account::factory()->for($userB)->default()->create([
            'name' => 'Conta B',
            'institution_id' => null,
            'initial_balance' => 0,
        ]);

        $categoryA = Category::factory()->for($userA)->create(['name' => 'Cat A']);

        $transaction = Transaction::factory()->create([
            'user_id' => $userA->id,
            'account_id' => $accountA->id,
            'category_id' => $categoryA->id,
            'amount' => 25,
            'description' => 'Original',
            'date' => now()->toDateString(),
        ]);

        $payload = $this->payload($transaction);
        $payload['account_id'] = $accountB->id;

        $response = $this->actingAs($userA)->patchJson(
            route('transactions.update', $transaction),
            $payload,
        );

        $response->assertUnprocessable()->assertJsonValidationErrors(['account_id']);

        $this->assertSame($accountA->id, $transaction->fresh()->account_id);
    }

    public function test_user_cannot_point_transaction_update_to_another_users_private_category(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $accountA = Account::factory()->for($userA)->default()->create([
            'name' => 'Conta A',
            'institution_id' => null,
            'initial_balance' => 0,
        ]);

        $categoryA = Category::factory()->for($userA)->create(['name' => 'Cat A']);
        $categoryB = Category::factory()->for($userB)->create(['name' => 'Cat B']);

        $transaction = Transaction::factory()->create([
            'user_id' => $userA->id,
            'account_id' => $accountA->id,
            'category_id' => $categoryA->id,
            'amount' => 25,
            'description' => 'Original',
            'date' => now()->toDateString(),
        ]);

        $payload = $this->payload($transaction);
        $payload['category_id'] = $categoryB->id;

        $response = $this->actingAs($userA)->patchJson(
            route('transactions.update', $transaction),
            $payload,
        );

        $response->assertUnprocessable()->assertJsonValidationErrors(['category_id']);

        $this->assertSame($categoryA->id, $transaction->fresh()->category_id);
    }

    public function test_owner_can_update_transaction_with_own_account_and_category(): void
    {
        $userA = User::factory()->create();

        $accountA = Account::factory()->for($userA)->default()->create([
            'name' => 'Conta A',
            'institution_id' => null,
            'initial_balance' => 0,
        ]);

        $accountA2 = Account::factory()->for($userA)->create([
            'name' => 'Conta A2',
            'institution_id' => null,
            'account_type' => AccountType::SAVINGS,
            'initial_balance' => 0,
            'is_default' => false,
        ]);

        $categoryA = Category::factory()->for($userA)->create(['name' => 'Cat A']);
        $categoryA2 = Category::factory()->for($userA)->create(['name' => 'Cat A2']);

        $transaction = Transaction::factory()->create([
            'user_id' => $userA->id,
            'account_id' => $accountA->id,
            'category_id' => $categoryA->id,
            'amount' => 25,
            'description' => 'Original',
            'date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($userA)->patchJson(
            route('transactions.update', $transaction),
            [
                'account_id' => $accountA2->id,
                'category_id' => $categoryA2->id,
                'amount' => '75,50',
                'description' => 'Updated',
            ],
        );

        $response->assertRedirect(route('transactions.index'));

        $transaction->refresh();
        $this->assertSame($accountA2->id, $transaction->account_id);
        $this->assertSame($categoryA2->id, $transaction->category_id);
        $this->assertEqualsWithDelta(75.5, (float) $transaction->amount, 0.001);
        $this->assertSame('Updated', $transaction->description);
    }
}
