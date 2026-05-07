<?php

namespace Tests\Feature;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryContractTest extends TestCase
{
    use RefreshDatabase;

    private function validStorePayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Minha categoria',
            'type' => TransactionType::EXPENSE->value,
            'color' => '#6366f1',
            'icon' => 'bx-tag',
        ], $overrides);
    }

    private function validUpdatePayload(Category $category, array $overrides = []): array
    {
        return array_merge([
            'name' => $category->name,
            'type' => $category->type->value,
            'color' => $category->color ?? '#6366f1',
            'icon' => $category->icon ?? 'bx-tag',
        ], $overrides);
    }

    public function test_user_cannot_update_another_users_category(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $category = Category::factory()->for($userA)->create([
            'name' => 'De A',
            'icon' => 'bx-tag',
            'color' => '#111111',
        ]);

        $response = $this->actingAs($userB)->patchJson(
            route('categories.update', $category),
            $this->validUpdatePayload($category, ['name' => 'Hacked']),
        );

        $response->assertForbidden();
        $this->assertSame('De A', $category->fresh()->name);
    }

    public function test_user_cannot_delete_another_users_category(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $category = Category::factory()->for($userA)->create([
            'name' => 'De A',
            'icon' => 'bx-tag',
            'color' => '#222222',
        ]);

        $response = $this->actingAs($userB)->deleteJson(route('categories.destroy', $category));

        $response->assertForbidden();
        $this->assertNotNull($category->fresh());
    }

    public function test_global_category_cannot_be_updated(): void
    {
        $user = User::factory()->create();

        $global = Category::factory()->global()->create([
            'name' => 'Global',
            'type' => TransactionType::EXPENSE,
            'icon' => 'bx-tag',
            'color' => '#333333',
        ]);

        $response = $this->actingAs($user)->patchJson(
            route('categories.update', $global),
            $this->validUpdatePayload($global, ['name' => 'Tentativa']),
        );

        $response->assertForbidden();
        $this->assertSame('Global', $global->fresh()->name);
    }

    public function test_global_category_cannot_be_deleted(): void
    {
        $user = User::factory()->create();

        $global = Category::factory()->global()->create([
            'name' => 'Global',
            'type' => TransactionType::EXPENSE,
            'icon' => 'bx-tag',
            'color' => '#444444',
        ]);

        $response = $this->actingAs($user)->deleteJson(route('categories.destroy', $global));

        $response->assertForbidden();
        $this->assertNotNull($global->fresh());
    }

    public function test_store_fails_after_ten_expense_categories(): void
    {
        $user = User::factory()->create();

        for ($i = 1; $i <= 10; $i++) {
            $this->actingAs($user)->post(
                route('categories.store'),
                $this->validStorePayload([
                    'name' => "Gasto {$i}",
                    'type' => TransactionType::EXPENSE->value,
                ]),
            )->assertRedirect(route('categories.index'));
        }

        $this->actingAs($user)->from(route('categories.index', ['type' => TransactionType::EXPENSE->value]))->post(
            route('categories.store'),
            $this->validStorePayload([
                'name' => 'Gasto extra',
                'type' => TransactionType::EXPENSE->value,
            ]),
        )->assertRedirect(route('categories.index', ['type' => TransactionType::EXPENSE->value]))
            ->assertSessionHasErrors('name');
    }

    public function test_store_fails_after_ten_income_categories(): void
    {
        $user = User::factory()->create();

        for ($i = 1; $i <= 10; $i++) {
            $this->actingAs($user)->post(
                route('categories.store'),
                $this->validStorePayload([
                    'name' => "Ganho {$i}",
                    'type' => TransactionType::INCOME->value,
                ]),
            )->assertRedirect(route('categories.index'));
        }

        $this->actingAs($user)->from(route('categories.index', ['type' => TransactionType::INCOME->value]))->post(
            route('categories.store'),
            $this->validStorePayload([
                'name' => 'Ganho extra',
                'type' => TransactionType::INCOME->value,
            ]),
        )->assertRedirect(route('categories.index', ['type' => TransactionType::INCOME->value]))
            ->assertSessionHasErrors('name');
    }

    public function test_index_lists_only_current_users_created_categories(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        Category::factory()->for($userB)->create([
            'name' => 'SecretB',
            'icon' => 'bx-tag',
            'color' => '#555555',
        ]);

        Category::factory()->for($userA)->create([
            'name' => 'VisibleA',
            'icon' => 'bx-tag',
            'color' => '#666666',
        ]);

        $response = $this->actingAs($userA)->get(
            route('categories.index', ['type' => TransactionType::EXPENSE->value]),
        );

        $response->assertOk();
        $categories = $response->viewData('categories');
        $this->assertCount(1, $categories);
        $this->assertSame('VisibleA', $categories->first()->name);
        $this->assertSame($userA->id, $categories->first()->user_id);
    }
}
