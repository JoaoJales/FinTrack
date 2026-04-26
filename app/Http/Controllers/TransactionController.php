<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaction;
use App\Services\AccountService;
use App\Services\CategoryService;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService,
        private AccountService $accountService,
        private CategoryService $categoryService
    )
    {
    }
    public function index(Request $request)
    {
        $this->authorize('viewAny', Transaction::class);
        $transactions = $this->transactionService->getAllByUser(auth()->id(), $request);

        $accounts = $this->accountService->getAccountsByUser(auth()->id());
        $defaultAccount = $accounts->firstWhere('is_default', true);

        $categories = $this->categoryService->getAllByUser(auth()->id())->sortBy('id');
        $categoriesByType = $categories->groupBy('type');

        $defaultExpenseCategory = $categoriesByType->get(TransactionType::EXPENSE->value)?->sortBy('id')->first();
        $defaultIncomeCategory  = $categoriesByType->get(TransactionType::INCOME->value)?->sortBy('id')->first();

        $defaultAccountData = [
            'id'    => $defaultAccount?->id,
            'name'  => $defaultAccount?->name,
            'image' => $defaultAccount?->institution?->image,
            'color' => $defaultAccount?->institution?->color ?? '#6B7280',
        ];

        $defaultExpenseCategoryData = [
            'id'    => $defaultExpenseCategory?->id,
            'name'  => $defaultExpenseCategory?->name,
            'icon'  => $defaultExpenseCategory?->icon ?? 'bx bx-category',
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
            )
        );
    }


    public function create()
    {
        $this->authorize('create', Transaction::class);

        return view('transaction.create');
    }

    public function store(StoreTransactionRequest $request)
    {
        $this->transactionService->store($request->validated(), auth()->id());

        return to_route('transactions.index')->with('success', 'Transação criada com sucesso!');
    }

    public function show(Transaction $transaction)
    {
        $this->authorize('view', $transaction);

        return view('transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        $this->authorize('update', $transaction);
        $accounts = $this->accountService->getAccountsByUser(auth()->id());
        $categories = $this->categoryService->getAllByUser(auth()->id());

        return view('transactions.edit', compact('transaction', 'accounts', 'categories'));
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
}
