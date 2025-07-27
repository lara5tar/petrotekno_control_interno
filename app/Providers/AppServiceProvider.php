<?php

namespace App\Providers;

use App\Models\Mantenimiento;
use App\Observers\MantenimientoObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

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

        // Registrar directiva personalizada para verificar permisos
        Blade::directive('hasPermission', function ($permission) {
            return "<?php if(auth()->check() && auth()->user()->hasPermission($permission)): ?>";
        });

        Blade::directive('endhasPermission', function () {
            return "<?php endif; ?>";
        });

        // Registrar directiva personalizada para verificar roles
        Blade::directive('hasRole', function ($role) {
            return "<?php if(auth()->check() && auth()->user()->hasRole($role)): ?>";
        });

        Blade::directive('endhasRole', function () {
            return "<?php endif; ?>";
        });
    }
}
