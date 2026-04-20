<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController
{
    public function __construct(
        private DashboardService $dashboardService
    )
    {
    }

    public function index(): View
    {
        $data = $this->dashboardService->getDashboardData(auth()->id());

        return \view('dashboard.index', $data);
    }

}
