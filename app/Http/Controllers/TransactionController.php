<?php

namespace App\Http\Controllers;

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
    public function index()
    {
        $this->authorize('viewAny', Transaction::class);
        $transactions = $this->transactionService->getAllByUser(auth()->id());

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $this->authorize('create', Transaction::class);
        $accounts = $this->accountService->getAccountsByUser(auth()->id());
        $categories = $this->categoryService->getAllByUser(auth()->id());

        return view('transactions.create', compact('accounts', 'categories'));
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
