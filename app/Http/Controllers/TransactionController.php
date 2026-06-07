<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Account;
use App\Models\Transaction;
use App\Services\AccountService;
use App\Services\CategoryService;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService,
        private AccountService $accountService,
        private CategoryService $categoryService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Transaction::class);
        $transactions = $this->transactionService->getAllByUser(Auth::id(), $request);

        $accounts = $this->accountService->getAccountsByUser(Auth::id());
        $defaultAccount = $accounts->firstWhere('is_default', true);

        $categories = $this->categoryService->getAllByUser(Auth::id())->sortBy('id');
        $categoriesByType = $categories->groupBy('type');

        $defaultExpenseCategory = $categoriesByType->get(TransactionType::EXPENSE->value)?->sortBy('id')->first();
        $defaultIncomeCategory = $categoriesByType->get(TransactionType::INCOME->value)?->sortBy('id')->first();

        $defaultAccountData = $this->accountPickerData($defaultAccount);

        $secondAccount = $accounts->count() >= 2 ? $accounts->where('id', '!=', $defaultAccount?->id)->first() : null;

        $defaultDestinationAccountData = $this->accountPickerData($secondAccount);

        $canTransfer = $accounts->count() >= 2;

        $defaultExpenseCategoryData = [
            'id' => $defaultExpenseCategory?->id,
            'name' => $defaultExpenseCategory?->name,
            'icon' => $defaultExpenseCategory?->icon ?? 'bx bx-category',
            'color' => $defaultExpenseCategory?->color ?? '#5c5e5c',
        ];

        return view('transaction.index', compact(
            'transactions',
            'categories',
            'accounts',
            'defaultAccount',
            'categoriesByType',
            'defaultExpenseCategory',
            'defaultIncomeCategory',
            'defaultAccountData',
            'defaultExpenseCategoryData',
            'defaultDestinationAccountData',
            'canTransfer',
        )
        );
    }

    public function store(StoreTransactionRequest $request)
    {
        $this->transactionService->store($request->validated(), Auth::id());

        return to_route('transactions.index')->with('success', 'Transação criada com sucesso!');
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $this->transactionService->update($transaction, $request->validated());

        return to_route('transactions.index')->with('success', 'Transação atualizada com sucesso!');
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorize('delete', $transaction);
        $this->transactionService->destroy($transaction);

        return to_route('transactions.index')->with('success', 'Transação deletada com sucesso!');
    }

    /**
     * @return array{id: int|null, name: string, image: string, color: string}
     */
    private function accountPickerData(?Account $account): array
    {
        if ($account === null) {
            return [
                'id' => null,
                'name' => 'Selecione uma conta',
                'image' => asset('banks-logos/default-bank.svg'),
                'color' => '#6B7280',
            ];
        }

        return [
            'id' => $account->id,
            'name' => $account->name,
            'image' => asset($account->institution?->image ?? 'banks-logos/default-bank.svg'),
            'color' => $account->institution?->color ?? '#6B7280',
        ];
    }
}
