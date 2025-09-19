<?php

namespace App\Providers;

use App\Models\Mantenimiento;
use App\Models\Personal;
use App\Models\Vehiculo;
use App\Observers\MantenimientoObserver;
use App\Observers\PersonalObserver;
use App\Observers\VehiculoObserver;
use App\View\Composers\AlertasComposer;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;

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
        // Forzar HTTPS en producción y desarrollo
        if ($this->app->environment(['production', 'staging']) || request()->isSecure()) {
            URL::forceScheme('https');
        }

        // Registrar observers
        Mantenimiento::observe(MantenimientoObserver::class);
        Personal::observe(PersonalObserver::class);
        Vehiculo::observe(VehiculoObserver::class);

        // Registrar View Composers
        View::composer('layouts.app', AlertasComposer::class);

        // Registrar directivas Blade personalizadas
        Blade::directive('hasPermission', function ($permission) {
            return "<?php if(auth()->check() && auth()->user() && auth()->user()->hasPermission($permission)): ?>";
        });

        Blade::directive('endhasPermission', function () {
            return "<?php endif; ?>";
        });

        // Registrar gates para usar con @can
        $permissions = [
            'ver_vehiculos', 'crear_vehiculos', 'editar_vehiculos', 'eliminar_vehiculos',
            'ver_personal', 'crear_personal', 'editar_personal', 'eliminar_personal',
            'ver_obras', 'crear_obras', 'actualizar_obras', 'eliminar_obras',
            'ver_mantenimientos', 'crear_mantenimientos', 'actualizar_mantenimientos', 'eliminar_mantenimientos',
            'ver_asignaciones', 'crear_asignaciones', 'editar_asignaciones', 'eliminar_asignaciones'
        ];

        foreach ($permissions as $permission) {
            \Illuminate\Support\Facades\Gate::define($permission, function ($user) use ($permission) {
                return $user->hasPermission($permission);
            });
        }

        // Definir gates específicos de kilometrajes (ya existentes)
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
