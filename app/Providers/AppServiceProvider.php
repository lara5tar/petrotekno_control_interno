<?php

namespace App\Providers;

use App\Models\Mantenimiento;
use App\Observers\MantenimientoObserver;
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
        // Registrar observers
        Mantenimiento::observe(MantenimientoObserver::class);
    }
}
