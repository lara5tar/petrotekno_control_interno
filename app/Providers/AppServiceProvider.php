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

        // Registrar directivas Blade personalizadas
        Blade::directive('hasPermission', function ($permission) {
            return "<?php if(auth()->check() && auth()->user()->hasPermission($permission)): ?>";
        });

        Blade::directive('endhasPermission', function () {
            return "<?php endif; ?>";
        });

        // Definir gate para autorización adicional
        \Illuminate\Support\Facades\Gate::define('ver_kilometrajes', function ($user) {
            return $user->hasPermission('ver_kilometrajes');
        });

        \Illuminate\Support\Facades\Gate::define('crear_kilometrajes', function ($user) {
            return $user->hasPermission('crear_kilometrajes');
        });

        \Illuminate\Support\Facades\Gate::define('editar_kilometrajes', function ($user) {
            return $user->hasPermission('editar_kilometrajes');
        });

        \Illuminate\Support\Facades\Gate::define('eliminar_kilometrajes', function ($user) {
            return $user->hasPermission('eliminar_kilometrajes');
        });
    }
}
