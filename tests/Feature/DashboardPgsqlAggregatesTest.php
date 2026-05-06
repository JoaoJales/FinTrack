<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardPgsqlAggregatesTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_renders_and_runs_postgresql_monthly_performance_query(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_balance_page_renders_and_runs_postgresql_month_extracts(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('balance.index'))
            ->assertOk();
    }
}
