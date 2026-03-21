<?php

namespace App\Http\Requests;

use App\Enums\AccountType;
use App\Enums\TransactionType;
use App\Rules\CheckCategoryLimit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check(); //Voltar aqui...
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', new CheckCategoryLimit()],
            'type' => ['required', new Enum(TransactionType::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Campo obrigatório',
            'type.required' => 'Campo obrigatório',
        ];
    }
}
