<?php

namespace App\Http\Controllers;

use App\Models\Personal;
use App\Models\HistorialOperadorVehiculo;
use App\Models\AsignacionObra;
use App\Models\Obra;
use App\Exports\OperadoresObrasFiltradosExport;
use App\Traits\PdfGeneratorTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class OperadorObraController extends Controller
{
    use PdfGeneratorTrait;
    
    public function index(Request $request)
    {
        $query = Personal::whereHas('historialOperadorVehiculo', function ($subquery) {
            $subquery->whereNotNull('obra_id');
        });

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre_completo', 'like', "%{$buscar}%")
                  ->orWhere('curp_numero', 'like', "%{$buscar}%")
                  ->orWhere('rfc', 'like', "%{$buscar}%")
                  ->orWhere('nss', 'like', "%{$buscar}%")
                  ->orWhere('no_licencia', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estatus', $request->estado);
        }

        if ($request->filled('obra_id')) {
            $query->whereHas('historialOperadorVehiculo', function ($subquery) use ($request) {
                $subquery->where('obra_id', $request->obra_id);
            });
        }

        if ($request->filled('solo_activos') && $request->solo_activos === 'true') {
            $query->where('estatus', 'activo');
        }

        $operadoresConObras = $query->withCount(['historialOperadorVehiculo as total_asignaciones_obra' => function ($subquery) {
                $subquery->whereNotNull('obra_id');
            }])
            ->with(['historialOperadorVehiculo.obra.encargado', 'historialOperadorVehiculo.vehiculo'])
            ->orderBy('nombre_completo')
            ->get();

        $estadosOptions = Personal::select('estatus')->distinct()->pluck('estatus', 'estatus');
        $obrasOptions = Obra::select('id', 'nombre_obra')->orderBy('nombre_obra')->get();

        $estadisticas = [
            'total_operadores' => $operadoresConObras->count(),
            'total_asignaciones' => $operadoresConObras->sum('total_asignaciones_obra'),
            'operadores_activos' => $operadoresConObras->where('estatus', 'activo')->count(),
            'promedio_asignaciones' => $operadoresConObras->count() > 0 ? round($operadoresConObras->sum('total_asignaciones_obra') / $operadoresConObras->count(), 1) : 0,
        ];

        return view('operadores.obras-por-operador', compact('operadoresConObras', 'estadosOptions', 'obrasOptions', 'estadisticas'));
    }

    public function show(Personal $operador)
    {
        $historialObras = HistorialOperadorVehiculo::historialObrasPorOperador($operador->id);
        
        $estadisticas = [
            'total_obras' => $historialObras->count(),
            'total_asignaciones' => HistorialOperadorVehiculo::where('operador_nuevo_id', $operador->id)
                ->whereNotNull('obra_id')
                ->count(),
            'obra_actual' => $this->obtenerObraActual($operador->id),
            'vehiculo_actual' => $operador->vehiculoActual(),
        ];

        $historialDetallado = HistorialOperadorVehiculo::where('operador_nuevo_id', $operador->id)
            ->whereNotNull('obra_id')
            ->with(['vehiculo', 'obra', 'usuarioAsigno'])
            ->orderBy('fecha_asignacion', 'desc')
            ->paginate(20);

        return view('operadores.detalle-obras-operador', compact(
            'operador', 
            'historialObras', 
            'estadisticas', 
            'historialDetallado'
        ));
    }

    private function obtenerObraActual($operadorId)
    {
        $asignacionActiva = AsignacionObra::where('operador_id', $operadorId)
            ->where('estado', 'activa')
            ->with('obra')
            ->first();

        return $asignacionActiva?->obra;
    }

    public function descargarReportePdf(Request $request)
    {
        if (!Auth::user() || !Auth::user()->can('ver_personal')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para descargar reportes de operadores'], 403);
            }
            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para acceder a esta sección']);
        }

        $query = Personal::whereHas('historialOperadorVehiculo', function ($subquery) {
            $subquery->whereNotNull('obra_id');
        });

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre_completo', 'like', "%{$buscar}%")
                  ->orWhere('curp_numero', 'like', "%{$buscar}%")
                  ->orWhere('rfc', 'like', "%{$buscar}%")
                  ->orWhere('nss', 'like', "%{$buscar}%")
                  ->orWhere('no_licencia', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estatus', $request->estado);
        }

        if ($request->filled('obra_id')) {
            $query->whereHas('historialOperadorVehiculo', function ($subquery) use ($request) {
                $subquery->where('obra_id', $request->obra_id);
            });
        }

        if ($request->filled('solo_activos') && $request->solo_activos === 'true') {
            $query->where('estatus', 'activo');
        }

        $operadoresConObras = $query->withCount(['historialOperadorVehiculo as total_asignaciones_obra' => function ($subquery) {
                $subquery->whereNotNull('obra_id');
            }])
            ->with(['historialOperadorVehiculo.obra.encargado', 'historialOperadorVehiculo.vehiculo'])
            ->orderBy('nombre_completo')
            ->limit(2000)
            ->get();

        $estadisticas = [
            'total_operadores' => $operadoresConObras->count(),
            'total_asignaciones' => $operadoresConObras->sum('total_asignaciones_obra'),
            'operadores_activos' => $operadoresConObras->where('estatus', 'activo')->count(),
            'promedio_asignaciones' => $operadoresConObras->count() > 0 ? round($operadoresConObras->sum('total_asignaciones_obra') / $operadoresConObras->count(), 1) : 0,
            'por_estado' => [
                'activo' => $operadoresConObras->where('estatus', 'activo')->count(),
                'inactivo' => $operadoresConObras->where('estatus', 'inactivo')->count(),
                'suspendido' => $operadoresConObras->where('estatus', 'suspendido')->count(),
            ],
        ];

        $filtrosAplicados = [
            'buscar' => $request->get('buscar'),
            'estado' => $request->get('estado'),
            'obra_id' => $request->get('obra_id'),
            'solo_activos' => $request->get('solo_activos'),
        ];

        $pdf = $this->createStandardPdf(
            'pdf.reportes.operadores-obras-filtrados', 
            compact('operadoresConObras', 'estadisticas', 'filtrosAplicados'),
            'landscape'
        );

        return $pdf->download('reporte-operadores-obras-filtrados-' . now()->format('Y-m-d-H-i-s') . '.pdf');
    }

    public function descargarReporteExcel(Request $request)
    {
        if (!Auth::user() || !Auth::user()->can('ver_personal')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para descargar reportes de operadores'], 403);
            }
            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para acceder a esta sección']);
        }

        $query = Personal::whereHas('historialOperadorVehiculo', function ($subquery) {
            $subquery->whereNotNull('obra_id');
        });

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre_completo', 'like', "%{$buscar}%")
                  ->orWhere('curp_numero', 'like', "%{$buscar}%")
                  ->orWhere('rfc', 'like', "%{$buscar}%")
                  ->orWhere('nss', 'like', "%{$buscar}%")
                  ->orWhere('no_licencia', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estatus', $request->estado);
        }

        if ($request->filled('obra_id')) {
            $query->whereHas('historialOperadorVehiculo', function ($subquery) use ($request) {
                $subquery->where('obra_id', $request->obra_id);
            });
        }

        if ($request->filled('solo_activos') && $request->solo_activos === 'true') {
            $query->where('estatus', 'activo');
        }

        $operadoresConObras = $query->withCount(['historialOperadorVehiculo as total_asignaciones_obra' => function ($subquery) {
                $subquery->whereNotNull('obra_id');
            }])
            ->with(['historialOperadorVehiculo.obra.encargado', 'historialOperadorVehiculo.vehiculo'])
            ->orderBy('nombre_completo')
            ->limit(5000)
            ->get();

        $estadisticas = [
            'total_operadores' => $operadoresConObras->count(),
            'total_asignaciones' => $operadoresConObras->sum('total_asignaciones_obra'),
            'operadores_activos' => $operadoresConObras->where('estatus', 'activo')->count(),
            'promedio_asignaciones' => $operadoresConObras->count() > 0 ? round($operadoresConObras->sum('total_asignaciones_obra') / $operadoresConObras->count(), 1) : 0,
            'por_estado' => [
                'activo' => $operadoresConObras->where('estatus', 'activo')->count(),
                'inactivo' => $operadoresConObras->where('estatus', 'inactivo')->count(),
                'suspendido' => $operadoresConObras->where('estatus', 'suspendido')->count(),
            ],
        ];

        $filtrosAplicados = [
            'buscar' => $request->get('buscar'),
            'estado' => $request->get('estado'),
            'obra_id' => $request->get('obra_id'),
            'solo_activos' => $request->get('solo_activos'),
        ];

        return Excel::download(
            new OperadoresObrasFiltradosExport($operadoresConObras, $filtrosAplicados, $estadisticas),
            'reporte-operadores-obras-filtrados-' . now()->format('Y-m-d-H-i-s') . '.xlsx'
        );
    }
}
