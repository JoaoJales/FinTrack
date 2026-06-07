<?php

namespace App\Http\Controllers;

use App\Services\BalanceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BalanceController extends Controller
{
    public function __construct(
        private BalanceService $balanceService
    ) {}

    public function index(): View
    {
        $data = $this->balanceService->getBalanceDetails(Auth::id());

        return view('balance.index', $data);
    }
}
