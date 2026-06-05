<?php

namespace App\Http\Requests\Concerns;

use App\Enums\TransactionType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

trait ValidatesTransactionReferences
{
    /**
     * @return array<int, ValidationRule|string>
     */
    protected function accountIdRules(): array
    {
        return [
            'required',
            Rule::exists('accounts', 'id')->where('user_id', $this->user()->id),
        ];
    }

    /**
     * @return array<int, ValidationRule|string>
     */
    protected function destinationAccountIdRules(): array
    {
        return [
            'required',
            Rule::exists('accounts', 'id')->where('user_id', $this->user()->id),
            'different:account_id',
        ];
    }

    /**
     * @return array<int, ValidationRule|string>
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

    protected function isTransferType(): bool
    {
        return $this->input('type') === TransactionType::TRANSFER->value;
    }
}
