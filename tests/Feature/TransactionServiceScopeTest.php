<?php

namespace Tests\Feature;

use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class TransactionServiceScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_paginated_list_never_includes_other_users_transactions(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $accountA = Account::factory()->for($userA)->default()->create([
            'name' => 'A',
            'institution_id' => null,
            'initial_balance' => 0,
        ]);

        $catA = Category::factory()->for($userA)->create(['name' => 'CA']);

        $accountB = Account::factory()->for($userB)->default()->create([
            'name' => 'B',
            'institution_id' => null,
            'initial_balance' => 0,
        ]);

        $catB = Category::factory()->for($userB)->create(['name' => 'CB']);

        Transaction::factory()->create([
            'user_id' => $userA->id,
            'account_id' => $accountA->id,
            'category_id' => $catA->id,
            'amount' => 10,
            'description' => 'Alpha unique',
            'date' => now()->toDateString(),
        ]);

        Transaction::factory()->create([
            'user_id' => $userB->id,
            'account_id' => $accountB->id,
            'category_id' => $catB->id,
            'amount' => 20,
            'description' => 'Alpha unique',
            'date' => now()->toDateString(),
        ]);

        $service = app(TransactionService::class);
        $paginator = $service->getAllByUser(
            $userA->id,
            Request::create('/transactions', 'GET', ['search' => 'Alpha unique']),
        );

        $this->assertCount(1, $paginator->items());
        $this->assertSame($userA->id, $paginator->items()[0]->user_id);
    }

    public function test_type_filter_applies_only_within_user_scope(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $accountA = Account::factory()->for($userA)->default()->create([
            'name' => 'A',
            'institution_id' => null,
            'initial_balance' => 0,
        ]);

        $expA = Category::factory()->for($userA)->expense()->create(['name' => 'ExpA']);
        $incA = Category::factory()->for($userA)->income()->create(['name' => 'IncA']);

        $accountB = Account::factory()->for($userB)->default()->create([
            'name' => 'B',
            'institution_id' => null,
            'initial_balance' => 0,
        ]);

        $expB = Category::factory()->for($userB)->create(['name' => 'ExpB']);

        Transaction::factory()->create([
            'user_id' => $userA->id,
            'account_id' => $accountA->id,
            'category_id' => $incA->id,
            'amount' => 100,
            'description' => 'Income A',
            'date' => now()->toDateString(),
        ]);

        Transaction::factory()->create([
            'user_id' => $userB->id,
            'account_id' => $accountB->id,
            'category_id' => $expB->id,
            'amount' => 50,
            'description' => 'Expense B only',
            'date' => now()->toDateString(),
        ]);

        $service = app(TransactionService::class);
        $paginator = $service->getAllByUser(
            $userA->id,
            Request::create('/transactions', 'GET', ['type' => TransactionType::EXPENSE->value]),
        );

        $this->assertCount(0, $paginator->items());
    }
}
