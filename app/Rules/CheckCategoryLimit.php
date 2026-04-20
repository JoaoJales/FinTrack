<?php

namespace App\Rules;

use App\Enums\TransactionType;
use App\Models\Category;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class CheckCategoryLimit implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = auth()->user();
        $limit = 10;
        $countExpense = Category::where('user_id', $user->id)->where('type', TransactionType::EXPENSE->value)->count();
        $countIncome = Category::where('user_id', $user->id)->where('type', TransactionType::INCOME->value)->count();

        if ($countExpense >= $limit) {
            $fail("Você atingiu o limite máximo de {$limit} categorias do tipo gasto permitidas.");
        }

        if ($countIncome >= $limit) {
            $fail("Você atingiu o limite máximo de {$limit} categorias do tipo ganho permitidas.");
        }
    }
}
