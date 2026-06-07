<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController
{
    public function __construct(
        private DashboardService $dashboardService
    ) {}

    public function index(): View
    {
        $data = $this->dashboardService->getDashboardData(Auth::id());

        return \view('dashboard.index', $data);
    }
}
