<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\Personal;
use App\Models\Kilometraje;
use App\Models\Mantenimiento;
use App\Models\Obra;
use App\Models\LogAccion;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Obtener estadísticas reales del sistema
        $estadisticas = $this->obtenerEstadisticas();
        
        // Obtener actividad reciente
        $actividadReciente = $this->obtenerActividadReciente();
        
        // Obtener vehículos que necesitan mantenimiento próximo
        $alertasMantenimiento = $this->obtenerAlertasMantenimiento();
        
        return view('home', compact(
            'estadisticas',
            'actividadReciente',
            'alertasMantenimiento'
        ));
    }

    /**
     * Obtener estadísticas reales del sistema
     */
    private function obtenerEstadisticas()
    {
        // Conteo de vehículos por estado
        $vehiculosEstados = Vehiculo::selectRaw('estatus, COUNT(*) as total')
            ->groupBy('estatus')
            ->pluck('total', 'estatus');

        // Conteo de mantenimientos por estado
        $mantenimientosProgramados = Mantenimiento::whereNull('fecha_fin')->count();
        $mantenimientosCompletados = Mantenimiento::whereNotNull('fecha_fin')->count();

        // Conteo de obras por estado
        $obrasEstados = Obra::selectRaw('estatus, COUNT(*) as total')
            ->groupBy('estatus')
            ->pluck('total', 'estatus');

        return [
            'total_vehiculos' => Vehiculo::count(),
            'vehiculos_disponibles' => $vehiculosEstados['disponible'] ?? 0,
            'vehiculos_asignados' => $vehiculosEstados['asignado'] ?? 0,
            'vehiculos_mantenimiento' => $vehiculosEstados['en_mantenimiento'] ?? 0,
            'total_personal' => Personal::where('estatus', 'activo')->count(),
            'total_obras' => Obra::count(),
            'obras_activas' => $obrasEstados['en_progreso'] ?? 0,
            'mantenimientos_programados' => $mantenimientosProgramados,
            'mantenimientos_completados' => $mantenimientosCompletados,
            'total_mantenimientos' => Mantenimiento::count(),
        ];
    }

    /**
     * Obtener actividad reciente del sistema
     */
    private function obtenerActividadReciente()
    {
        // Últimos mantenimientos completados
        $mantenimientosRecientes = Mantenimiento::with('vehiculo')
            ->whereNotNull('fecha_fin')
            ->orderBy('updated_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($mantenimiento) {
                return [
                    'tipo' => 'mantenimiento_completado',
                    'descripcion' => 'Mantenimiento ' . strtolower($mantenimiento->tipo_servicio) . ' completado',
                    'vehiculo' => $mantenimiento->vehiculo ? 
                        $mantenimiento->vehiculo->marca . ' ' . $mantenimiento->vehiculo->modelo . ' - ' . $mantenimiento->vehiculo->placas :
                        'Vehículo ID ' . $mantenimiento->vehiculo_id,
                    'fecha' => $mantenimiento->fecha_fin,
                    'usuario' => 'Sistema'
                ];
            });

        // Últimos kilometrajes registrados
        $kilometrajesRecientes = Kilometraje::with('vehiculo')
            ->orderBy('fecha_captura', 'desc')
            ->limit(2)
            ->get()
            ->map(function ($kilometraje) {
                return [
                    'tipo' => 'kilometraje_registrado',
                    'descripcion' => 'Kilometraje registrado: ' . number_format($kilometraje->kilometraje) . ' km',
                    'vehiculo' => $kilometraje->vehiculo ? 
                        $kilometraje->vehiculo->marca . ' ' . $kilometraje->vehiculo->modelo . ' - ' . $kilometraje->vehiculo->placas :
                        'Vehículo ID ' . $kilometraje->vehiculo_id,
                    'fecha' => $kilometraje->fecha_captura,
                    'usuario' => 'Sistema'
                ];
            });

        return $mantenimientosRecientes->concat($kilometrajesRecientes)
            ->sortByDesc('fecha')
            ->take(5);
    }

    /**
     * Obtener alertas de mantenimiento
     */
    private function obtenerAlertasMantenimiento()
    {
        // Vehículos que podrían necesitar mantenimiento basado en kilometraje
        return Vehiculo::where('kilometraje_actual', '>', 0)
            ->whereNotNull('intervalo_km_motor')
            ->get()
            ->filter(function ($vehiculo) {
                // Verificar si el vehículo está cerca del siguiente mantenimiento
                $ultimoKm = $vehiculo->kilometraje_actual;
                $intervalo = $vehiculo->intervalo_km_motor;
                
                if ($intervalo > 0) {
                    $kmProximoMantenimiento = (floor($ultimoKm / $intervalo) + 1) * $intervalo;
                    $diferencia = $kmProximoMantenimiento - $ultimoKm;
                    
                    // Si está a menos de 1000 km del próximo mantenimiento
                    return $diferencia <= 1000;
                }
                
                return false;
            })
            ->map(function ($vehiculo) {
                $ultimoKm = $vehiculo->kilometraje_actual;
                $intervalo = $vehiculo->intervalo_km_motor;
                $kmProximoMantenimiento = (floor($ultimoKm / $intervalo) + 1) * $intervalo;
                $diferencia = $kmProximoMantenimiento - $ultimoKm;
                
                return [
                    'vehiculo' => $vehiculo->marca . ' ' . $vehiculo->modelo . ' - ' . $vehiculo->placas,
                    'km_actual' => $ultimoKm,
                    'km_proximo_mantenimiento' => $kmProximoMantenimiento,
                    'km_restantes' => $diferencia,
                    'tipo_alerta' => $diferencia <= 500 ? 'critica' : 'advertencia'
                ];
            })
            ->take(5);
    }
}
