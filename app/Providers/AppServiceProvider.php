<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //Diretiva @moneyBr
        Blade::directive('moneyBr', function ($expression) {
            return "<?php echo number_format($expression, 2, ',', '.'); ?>";
        });
    }
}
