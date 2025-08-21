<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAccion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogController extends Controller
{
    /**
     * Mostrar la lista de logs del sistema
     */
    public function index(Request $request)
    {
        // Verificar permisos
        if (!Auth::user()->hasPermission('ver_logs')) {
            return redirect()->route('admin.configuracion.index')
                ->with('error', 'No tienes permisos para ver los logs del sistema');
        }

        // Obtener parámetros de filtrado
        $filtros = $request->only(['usuario_id', 'accion', 'tabla_afectada', 'fecha_desde', 'fecha_hasta']);
        
        // Query base
        $query = LogAccion::with(['usuario', 'usuario.personal'])->orderBy('fecha_hora', 'desc');

        // Aplicar filtros
        if (!empty($filtros['usuario_id'])) {
            $query->where('usuario_id', $filtros['usuario_id']);
        }

        if (!empty($filtros['accion'])) {
            $query->where('accion', 'like', '%' . $filtros['accion'] . '%');
        }

        if (!empty($filtros['tabla_afectada'])) {
            $query->where('tabla_afectada', $filtros['tabla_afectada']);
        }

        if (!empty($filtros['fecha_desde'])) {
            $query->whereDate('fecha_hora', '>=', $filtros['fecha_desde']);
        }

        if (!empty($filtros['fecha_hasta'])) {
            $query->whereDate('fecha_hora', '<=', $filtros['fecha_hasta']);
        }

        // Paginación
        $logs = $query->paginate(20)->appends($filtros);

        // Obtener usuarios para el filtro
        $usuarios = User::with('personal')
            ->whereNull('deleted_at')
            ->get();

        // Obtener acciones únicas para el filtro
        $acciones = LogAccion::select('accion')
            ->distinct()
            ->whereNotNull('accion')
            ->orderBy('accion')
            ->pluck('accion');

        // Obtener tablas afectadas únicas para el filtro
        $tablas = LogAccion::select('tabla_afectada')
            ->distinct()
            ->whereNotNull('tabla_afectada')
            ->orderBy('tabla_afectada')
            ->pluck('tabla_afectada');

        return view('admin.logs.index', compact('logs', 'usuarios', 'acciones', 'tablas', 'filtros'));
    }
}