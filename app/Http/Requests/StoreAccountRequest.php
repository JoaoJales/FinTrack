<?php

namespace App\Http\Requests;

use App\Enums\AccountType;
use App\Rules\CheckAccountLimit;
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', new CheckAccountLimit()],
            'initial_balance' => ['required', 'numeric', 'min:0'],
            'account_type' => ['required', new Enum(AccountType::class)],
            'institution_id' => ['required', 'exists:institutions,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Campo obrigatório',
            'initial_balance.required' => 'Campo obrigatório',
            'account_type.required' => 'Campo obrigatório',
            'institution_id.required' => 'Campo obrigatório',
        ];
    }
}
