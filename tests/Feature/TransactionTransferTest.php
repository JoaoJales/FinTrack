<?php

namespace Tests\Feature;

use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Services\AccountService;
use App\Services\DashboardService;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class TransactionTransferTest extends TestCase
{
    use RefreshDatabase;

    private function createTwoAccounts(User $user): array
    {
        $origin = Account::factory()->for($user)->default()->create([
            'name' => 'Conta Origem',
            'institution_id' => null,
            'initial_balance' => 1000,
        ]);

        $destination = Account::factory()->for($user)->create([
            'name' => 'Conta Destino',
            'institution_id' => null,
            'initial_balance' => 500,
            'is_default' => false,
        ]);

        return [$origin, $destination];
    }

    public function test_store_creates_valid_transfer(): void
    {
        $user = User::factory()->create();
        [$origin, $destination] = $this->createTwoAccounts($user);

        $response = $this->actingAs($user)->postJson(route('transactions.store'), [
            'type' => TransactionType::TRANSFER->value,
            'account_id' => $origin->id,
            'destination_account_id' => $destination->id,
            'amount' => '200,00',
            'description' => 'Entre contas',
            'date' => now()->format('d/m/Y'),
        ]);

        $response->assertRedirect(route('transactions.index'));

        $transaction = Transaction::query()->where('user_id', $user->id)->first();
        $this->assertSame(TransactionType::TRANSFER, $transaction->type);
        $this->assertNull($transaction->category_id);
        $this->assertSame($destination->id, $transaction->destination_account_id);

        $this->assertSame(800.0, (float) $origin->fresh()->current_balance);
        $this->assertSame(700.0, (float) $destination->fresh()->current_balance);
    }

    public function test_store_rejects_same_origin_and_destination_account(): void
    {
        $user = User::factory()->create();
        [$origin] = $this->createTwoAccounts($user);

        $response = $this->actingAs($user)->postJson(route('transactions.store'), [
            'type' => TransactionType::TRANSFER->value,
            'account_id' => $origin->id,
            'destination_account_id' => $origin->id,
            'amount' => '100,00',
            'date' => now()->format('d/m/Y'),
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['destination_account_id']);
    }

    public function test_store_rejects_foreign_destination_account(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        [$originA] = $this->createTwoAccounts($userA);
        $destinationB = Account::factory()->for($userB)->create([
            'institution_id' => null,
            'initial_balance' => 0,
        ]);

        $response = $this->actingAs($userA)->postJson(route('transactions.store'), [
            'type' => TransactionType::TRANSFER->value,
            'account_id' => $originA->id,
            'destination_account_id' => $destinationB->id,
            'amount' => '100,00',
            'date' => now()->format('d/m/Y'),
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['destination_account_id']);
    }

    public function test_transfer_does_not_affect_month_result(): void
    {
        $user = User::factory()->create();
        [$origin, $destination] = $this->createTwoAccounts($user);

        $expenseCategory = Category::factory()->for($user)->expense()->create(['name' => 'Gastos']);
        Transaction::factory()->create([
            'user_id' => $user->id,
            'account_id' => $origin->id,
            'category_id' => $expenseCategory->id,
            'type' => TransactionType::EXPENSE,
            'amount' => 100,
            'date' => now()->toDateString(),
        ]);

        Transaction::factory()->create([
            'user_id' => $user->id,
            'account_id' => $origin->id,
            'destination_account_id' => $destination->id,
            'category_id' => null,
            'type' => TransactionType::TRANSFER,
            'amount' => 300,
            'date' => now()->toDateString(),
        ]);

        $dashboard = app(DashboardService::class);
        $data = $dashboard->getDashboardData($user->id);

        $this->assertSame(100.0, (float) $data['month_performace']->total_expense);
        $this->assertSame(0.0, (float) $data['month_performace']->total_income);
    }

    public function test_total_balance_unchanged_after_transfer(): void
    {
        $user = User::factory()->create();
        [$origin, $destination] = $this->createTwoAccounts($user);

        $accountService = app(AccountService::class);
        $before = $accountService->getTotalBalance($accountService->getAccountsByUser($user->id));

        Transaction::factory()->create([
            'user_id' => $user->id,
            'account_id' => $origin->id,
            'destination_account_id' => $destination->id,
            'category_id' => null,
            'type' => TransactionType::TRANSFER,
            'amount' => 250,
            'date' => now()->toDateString(),
        ]);

        $after = $accountService->getTotalBalance($accountService->getAccountsByUser($user->id));

        $this->assertSame($before, $after);
    }

    public function test_income_and_expense_require_category(): void
    {
        $user = User::factory()->create();
        [$origin] = $this->createTwoAccounts($user);

        $response = $this->actingAs($user)->postJson(route('transactions.store'), [
            'type' => TransactionType::EXPENSE->value,
            'account_id' => $origin->id,
            'amount' => '50,00',
            'date' => now()->format('d/m/Y'),
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['category_id']);
    }

    public function test_update_transfer_changes_accounts_and_amount(): void
    {
        $user = User::factory()->create();
        [$origin, $destination] = $this->createTwoAccounts($user);

        $third = Account::factory()->for($user)->create([
            'institution_id' => null,
            'initial_balance' => 0,
            'is_default' => false,
        ]);

        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'account_id' => $origin->id,
            'destination_account_id' => $destination->id,
            'category_id' => null,
            'type' => TransactionType::TRANSFER,
            'amount' => 100,
            'date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($user)->patchJson(route('transactions.update', $transaction), [
            'type' => TransactionType::TRANSFER->value,
            'account_id' => $origin->id,
            'destination_account_id' => $third->id,
            'amount' => '150,00',
            'date' => now()->format('d/m/Y'),
        ]);

        $response->assertRedirect(route('transactions.index'));

        $transaction->refresh();
        $this->assertSame($third->id, $transaction->destination_account_id);
        $this->assertSame('150.00', (string) $transaction->amount);
    }

    public function test_filter_by_transfer_type_returns_only_transfers(): void
    {
        $user = User::factory()->create();
        [$origin, $destination] = $this->createTwoAccounts($user);

        $category = Category::factory()->for($user)->expense()->create(['name' => 'Gasto']);

        Transaction::factory()->create([
            'user_id' => $user->id,
            'account_id' => $origin->id,
            'category_id' => $category->id,
            'type' => TransactionType::EXPENSE,
            'amount' => 50,
            'date' => now()->toDateString(),
        ]);

        Transaction::factory()->create([
            'user_id' => $user->id,
            'account_id' => $origin->id,
            'destination_account_id' => $destination->id,
            'category_id' => null,
            'type' => TransactionType::TRANSFER,
            'amount' => 100,
            'date' => now()->toDateString(),
        ]);

        $service = app(TransactionService::class);
        $paginator = $service->getAllByUser(
            $user->id,
            Request::create('/transactions', 'GET', ['type' => TransactionType::TRANSFER->value]),
        );

        $this->assertCount(1, $paginator->items());
        $this->assertSame(TransactionType::TRANSFER, $paginator->items()[0]->type);
    }

    public function test_filter_by_account_includes_origin_or_destination(): void
    {
        $user = User::factory()->create();
        [$origin, $destination] = $this->createTwoAccounts($user);

        Transaction::factory()->create([
            'user_id' => $user->id,
            'account_id' => $origin->id,
            'destination_account_id' => $destination->id,
            'category_id' => null,
            'type' => TransactionType::TRANSFER,
            'amount' => 100,
            'date' => now()->toDateString(),
        ]);

        $service = app(TransactionService::class);

        $byOrigin = $service->getAllByUser(
            $user->id,
            Request::create('/transactions', 'GET', ['account_id' => $origin->id]),
        );
        $byDestination = $service->getAllByUser(
            $user->id,
            Request::create('/transactions', 'GET', ['account_id' => $destination->id]),
        );

        $this->assertCount(1, $byOrigin->items());
        $this->assertCount(1, $byDestination->items());
    }
}
