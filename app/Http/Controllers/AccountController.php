<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\Account;
use App\Models\Institution;
use App\Services\AccountService;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class AccountController extends Controller
{
    public function __construct(
        private AccountService $accountService,
    ) {}

    public function index()
    {
        $this->authorize('viewAny', Account::class);
        $accounts = $this->accountService->getAccountsByUser(Auth::id());
        $institutions = Institution::all();
        $accountsCount = $accounts->count();
        $total_balance = $this->accountService->getTotalBalance($accounts);

        return view('accounts.index', compact('accounts', 'institutions', 'accountsCount', 'total_balance'));
    }

    public function store(StoreAccountRequest $request)
    {
        try {
            $this->accountService->store($request->validated(), Auth::id());

            return to_route('accounts.index')->with('success', 'Conta criada com sucesso!');
        } catch (InvalidArgumentException $e) {
            return to_route('accounts.index')->with('error', $e->getMessage());
        }
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
