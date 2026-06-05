<?php

namespace Database\Factories;

use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory();

        return [
            'user_id' => $user,
            'account_id' => Account::factory()->for($user)->state([
                'institution_id' => null,
                'is_default' => true,
            ]),
            'destination_account_id' => null,
            'category_id' => Category::factory()->for($user)->expense(),
            'type' => TransactionType::EXPENSE,
            'amount' => fake()->randomFloat(2, 1, 5000),
            'date' => fake()->date(),
            'description' => fake()->sentence(),
        ];
    }

    public function forUser(User $user): static
    {
        return $this->for($user)->state([
            'account_id' => Account::factory()->for($user)->state([
                'institution_id' => null,
                'is_default' => true,
            ]),
            'category_id' => Category::factory()->for($user)->expense(),
            'type' => TransactionType::EXPENSE,
        ]);
    }

    public function income(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => TransactionType::INCOME,
            'category_id' => Category::factory()->income(),
        ]);
    }

    public function expense(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => TransactionType::EXPENSE,
            'category_id' => Category::factory()->expense(),
        ]);
    }

    public function transfer(?Account $destination = null): static
    {
        return $this->state(function (array $attributes) use ($destination) {
            $userId = $attributes['user_id'] ?? User::factory();

            return [
                'type' => TransactionType::TRANSFER,
                'category_id' => null,
                'destination_account_id' => $destination ?? Account::factory()->for($userId)->state([
                    'institution_id' => null,
                    'is_default' => false,
                ]),
            ];
        });
    }
}
