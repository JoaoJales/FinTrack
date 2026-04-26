<?php

namespace App\Http\Requests;

use App\Enums\AccountType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $account = $this->route('account');

        return $account && $this->user()->can('update', $account);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'account_type' => ['required', new Enum(AccountType::class)],
            'institution_id' => ['required', 'exists:institutions,id'],
            'is_default' => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Campo (Nome) é obrigatório',
            'account_type.required' => 'Campo (Tipo de Conta) é obrigatório',
            'institution_id.required' => 'Campo (Banco) é obrigatório',
        ];
    }
}
