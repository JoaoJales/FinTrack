<?php

namespace Database\Factories;

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
            'category_id' => Category::factory()->for($user)->expense(),
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
        ]);
    }
}
