<?php

namespace App\Http\Requests;

use App\Helpers\FormatHelper;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check(); //Voltar aqui...
    }

    protected function prepareForValidation(): void
    {
        if ($this->amount) {
            $this->merge([
                'amount' => FormatHelper::brToUS($this->amount),
            ]);
        }

        if ($this->date) {
            $this->merge([
                'date' => Carbon::createFromFormat('d/m/Y', $this->date)->format('Y-m-d'),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'account_id' => ['required', 'exists:accounts,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:255'],
            'date' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Campo (Valor) é obrigatório',
            'category_id.required' => 'Campo obrigatório',
        ];
    }
}
