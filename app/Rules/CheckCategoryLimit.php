<?php

namespace App\Rules;

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
        $count = Category::where('user_id', $user->id);

        if ($count >= $limit) {
            $fail("Você atingiu o limite máximo de {$limit} categorias permitidas.");
        }
    }
}
