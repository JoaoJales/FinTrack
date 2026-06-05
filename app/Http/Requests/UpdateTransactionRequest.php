<?php

namespace App\Http\Requests;

use App\Enums\TransactionType;
use App\Helpers\FormatHelper;
use App\Http\Requests\Concerns\ValidatesTransactionReferences;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTransactionRequest extends FormRequest
{
    use ValidatesTransactionReferences;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $transaction = $this->route('transaction');

        return $transaction && $this->user()->can('update', $transaction);
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

        if (! $this->type) {
            $this->merge([
                'type' => TransactionType::EXPENSE->value,
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
        $isTransfer = $this->isTransferType();

        return [
            'type' => ['required', Rule::enum(TransactionType::class)],
            'account_id' => $this->accountIdRules(),
            'category_id' => $isTransfer
                ? ['prohibited']
                : $this->categoryIdRules(),
            'destination_account_id' => $isTransfer
                ? $this->destinationAccountIdRules()
                : ['prohibited'],
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
            'category_id.prohibited' => 'Transferências não possuem categoria',
            'destination_account_id.required' => 'Selecione a conta de destino',
            'destination_account_id.different' => 'A conta de destino deve ser diferente da conta de origem',
            'destination_account_id.prohibited' => 'Conta de destino só é permitida em transferências',
        ];
    }
}
