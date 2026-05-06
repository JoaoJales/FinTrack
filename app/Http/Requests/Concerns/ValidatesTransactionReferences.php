<?php

namespace App\Http\Requests\Concerns;

use Illuminate\Validation\Rule;

trait ValidatesTransactionReferences
{
    /**
     * @return array<int, \Illuminate\Contracts\Validation\ValidationRule|string>
     */
    protected function accountIdRules(): array
    {
        return [
            'required',
            Rule::exists('accounts', 'id')->where('user_id', $this->user()->id),
        ];
    }

    /**
     * @return array<int, \Illuminate\Contracts\Validation\ValidationRule|string>
     */
    protected function categoryIdRules(): array
    {
        return [
            'required',
            Rule::exists('categories', 'id')
                ->whereNull('deleted_at')
                ->where(function ($query) {
                    $query->where('user_id', $this->user()->id)
                        ->orWhere('is_editable', false);
                }),
        ];
    }
}
