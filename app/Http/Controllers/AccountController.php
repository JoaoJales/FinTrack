<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\Account;
use App\Models\Institution;
use App\Services\AccountService;
use InvalidArgumentException;

class AccountController extends Controller
{
    public function __construct(
        private AccountService $accountService,
    ) {}

    public function index() // View Exibir contas
    {
        $this->authorize('viewAny', Account::class);
        $accounts = $this->accountService->getAccountsByUser(auth()->id());
        $institutions = Institution::all();
        $accountsCount = $accounts->count();
        $total_balance = $this->accountService->getTotalBalance($accounts);

        return view('accounts.index', compact('accounts', 'institutions', 'accountsCount', 'total_balance'));
    }

    public function create()  // View criar contas
    {
        $this->authorize('create', Account::class);

        return view('accounts.create');
    }

    public function store(StoreAccountRequest $request)
    {
        try {
            $this->accountService->store($request->validated(), auth()->id());

            return to_route('accounts.index')->with('success', 'Conta criada com sucesso!');
        } catch (InvalidArgumentException $e) {
            return to_route('accounts.index')->with('error', $e->getMessage());
        }
    }

    public function show(Account $account)
    {
        $this->authorize('view', $account);

        return view('accounts.show', compact('account'));
    }

    public function edit(Account $account)
    {
        $this->authorize('update', $account);

        return view('accounts.edit', compact('account'));
    }

    public function update(UpdateAccountRequest $request, Account $account)
    {
        try {
            $this->accountService->update($account, $request->validated());

            return to_route('accounts.index')->with('success', 'Conta atualizada com sucesso!');
        } catch (InvalidArgumentException $e) {
            return to_route('accounts.index')->with('error', $e->getMessage());
        }
    }

    public function destroy(Account $account)
    {
        $this->authorize('delete', $account);
        try {
            $this->accountService->destroy($account);

            return to_route('accounts.index')->with('success', 'Conta excluída com sucesso!');
        } catch (InvalidArgumentException $e) {
            return to_route('accounts.index')->with('error', $e->getMessage());
        }
    }
}
