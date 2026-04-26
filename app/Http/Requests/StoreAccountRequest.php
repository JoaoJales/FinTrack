<?php

namespace App\Http\Requests;

use App\Enums\AccountType;
use App\Helpers\FormatHelper;
use App\Rules\CheckAccountLimit;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->initial_balance) {
            $this->merge([
                'initial_balance' => FormatHelper::brToUS($this->initial_balance),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', new CheckAccountLimit],
            'initial_balance' => ['required', 'numeric', 'min:0'],
            'account_type' => ['required', new Enum(AccountType::class)],
            'institution_id' => ['required', 'exists:institutions,id'],
            'is_default' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Campo (Nome) é obrigatório',
            'initial_balance.required' => 'Campo (Saldo inicial) é obrigatório',
            'account_type.required' => 'Campo (Tipo de conta) é obrigatório',
            'institution_id.required' => 'Campo Banco é obrigatório',
        ];
    }
}
