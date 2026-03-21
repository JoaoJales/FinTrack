<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\Account;
use App\Services\AccountService;
use Illuminate\Http\Request;
class AccountController extends Controller
{
    public function __construct(
        private AccountService $accountService,
    )
    {

    }

    public function index() //View Exibir contas
    {
        $this->authorize('viewAny', Account::class);
        $accounts = $this->accountService->getAccountsByUser(auth()->id());

        return view('accounts.index', compact('accounts'));
    }

    public function create()  //View criar contas
    {
        $this->authorize('create', Account::class);

        // Apenas busca todos os bancos. Não precisa de policy e controller para institutions.
//        $institutions = Institution::all();
//
//        return view('accounts.create', compact('institutions'));

        return view('accounts.create');
    }

    public function store(StoreAccountRequest $request)
    {
        $this->accountService->store($request->validated(), auth()->id());

        return to_route('accounts.index')->with('success', 'Conta criada com sucesso!');
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
        $this->accountService->update($account, $request->validated());
        return to_route('accounts.index')->with('success', 'Conta atualizada com sucesso!');
    }

    public function destroy(Account $account)
    {
        $this->authorize('delete', $account);
        $this->accountService->destroy($account);

        return to_route('accounts.index')->with('success', 'Conta deletada com sucesso!');
    }

}
