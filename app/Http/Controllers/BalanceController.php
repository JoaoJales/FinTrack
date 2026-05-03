<?php

namespace App\Http\Controllers;

use App\Services\BalanceService;
use Illuminate\View\View;

class BalanceController extends Controller
{
    public function __construct(
        private BalanceService $balanceService
    ) {}

    public function index(): View
    {
        $data = $this->balanceService->getBalanceDetails(auth()->id());

        return view('balance.index', $data);
    }
}
