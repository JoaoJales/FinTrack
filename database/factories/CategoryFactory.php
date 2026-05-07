<?php

namespace Database\Factories;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'user_id' => User::factory(),
            'icon' => 'bx-tag',
            'color' => '#6366f1',
            'type' => TransactionType::EXPENSE,
            'is_editable' => true,
        ];
    }

    public function expense(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => TransactionType::EXPENSE,
        ]);
    }

    public function income(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => TransactionType::INCOME,
        ]);
    }

    /** Categoria global do sistema (somente leitura na UI). */
    public function global(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'is_editable' => false,
        ]);
    }

    public function forUser(User $user): static
    {
        return $this->for($user);
    }

    public function trashed(): static
    {
        return $this->afterCreating(fn (Category $category) => $category->delete());
    }
}
