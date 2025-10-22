@extends('pdf.layouts.base')

@section('title', 'Reporte de Operadores con Obras Filtrados')
@section('report-title', 'Reporte de Operadores con Obras Filtrados')
@section('report-subtitle', 'Listado de operadores con historial de obras según criterios de filtrado aplicados')

@section('content')
    <!-- Sección de Estadísticas -->
    @if(isset($estadisticas) && count($estadisticas) > 0)
        <div class="pdf-stats-section">
            <h3 class="stats-title">Resumen Ejecutivo</h3>
            <div class="stats-grid">
                <div class="stats-row">
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['total_operadores'] ?? 0 }}</span>
                        <span class="stat-label">Total Operadores</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['total_asignaciones'] ?? 0 }}</span>
                        <span class="stat-label">Total Asignaciones</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['operadores_activos'] ?? 0 }}</span>
                        <span class="stat-label">Operadores Activos</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['promedio_asignaciones'] ?? 0 }}</span>
                        <span class="stat-label">Promedio Asignaciones</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas por Estado -->
        <div class="pdf-stats-section">
            <h3 class="stats-title">Distribución por Estado</h3>
            <div class="stats-grid">
                <div class="stats-row">
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['por_estado']['activo'] ?? 0 }}</span>
                        <span class="stat-label">Activos</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['por_estado']['inactivo'] ?? 0 }}</span>
                        <span class="stat-label">Inactivos</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['por_estado']['suspendido'] ?? 0 }}</span>
                        <span class="stat-label">Suspendidos</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Información de Filtros Aplicados -->
    @if(isset($filtrosAplicados) && count(array_filter($filtrosAplicados)) > 0)
        <div class="pdf-info-section">
            <h3 class="info-title">Filtros Aplicados</h3>
            <div class="info-grid">
                @if(isset($filtrosAplicados['buscar']) && $filtrosAplicados['buscar'])
                    <div class="info-row">
                        <div class="info-label">Búsqueda:</div>
                        <div class="info-value">{{ $filtrosAplicados['buscar'] }}</div>
                    </div>
                @endif
                @if(isset($filtrosAplicados['estado']) && $filtrosAplicados['estado'])
                    <div class="info-row">
                        <div class="info-label">Estado:</div>
                        <div class="info-value">{{ ucfirst($filtrosAplicados['estado']) }}</div>
                    </div>
                @endif
                @if(isset($filtrosAplicados['obra_id']) && $filtrosAplicados['obra_id'])
                    <div class="info-row">
                        <div class="info-label">Obra ID:</div>
                        <div class="info-value">{{ $filtrosAplicados['obra_id'] }}</div>
                    </div>
                @endif
                @if(isset($filtrosAplicados['solo_activos']) && $filtrosAplicados['solo_activos'])
                    <div class="info-row">
                        <div class="info-label">Solo Activos:</div>
                        <div class="info-value">{{ $filtrosAplicados['solo_activos'] === 'true' ? 'Sí' : 'No' }}</div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Información adicional -->
    <div class="pdf-info-section">
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Total de Registros:</div>
                <div class="info-value">{{ isset($operadoresConObras) ? $operadoresConObras->count() : 0 }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Fecha de Generación:</div>
                <div class="info-value">{{ now()->format('d/m/Y H:i:s') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Generado por:</div>
                <div class="info-value">{{ Auth::user()->nombre ?? 'Sistema' }}</div>
            </div>
        </div>
    </div>

    <!-- Tabla de Operadores -->
    @if(isset($operadoresConObras) && $operadoresConObras->count() > 0)
        <div class="pdf-table-section">
            <h3 class="table-title">Listado de Operadores con Obras</h3>
            <table class="pdf-table">
                <thead>
                    <tr>
                        <th style="width: 5%">ID</th>
                        <th style="width: 20%">Nombre Completo</th>
                        <th style="width: 8%">Estado Op.</th>
                        <th style="width: 25%">Obra Asignada</th>
                        <th style="width: 15%">Ubicación Obra</th>
                        <th style="width: 8%">Avance</th>
                        <th style="width: 19%">Responsable Obra</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($operadoresConObras as $operador)
                        @php
                            // Obtener información de la última asignación y obra
                            $ultimaAsignacion = $operador->historialOperadorVehiculo
                                ->where('obra_id', '!=', null)
                                ->sortByDesc('fecha_asignacion')
                                ->first();
                            
                            $nombreObra = 'N/A';
                            $ubicacionObra = 'N/A';
                            $avanceObra = 'N/A';
                            $responsableObra = 'N/A';
                            
                            if ($ultimaAsignacion && $ultimaAsignacion->obra) {
                                $obra = $ultimaAsignacion->obra;
                                $nombreObra = $obra->nombre_obra ?? 'N/A';
                                $ubicacionObra = $obra->ubicacion ?? 'N/A';
                                $avanceObra = $obra->avance ? $obra->avance . '%' : 'N/A';
                                
                                // Obtener responsable/encargado
                                if ($obra->encargado) {
                                    $responsableObra = $obra->encargado->nombre_completo;
                                } elseif ($obra->encargado_id) {
                                    try {
                                        $encargado = \App\Models\Personal::find($obra->encargado_id);
                                        $responsableObra = $encargado ? $encargado->nombre_completo : 'N/A';
                                    } catch (\Exception $e) {
                                        $responsableObra = 'N/A';
                                    }
                                }
                            }
                        @endphp
                        <tr>
                            <td class="text-center">{{ $operador->id }}</td>
                            <td>{{ $operador->nombre_completo ?? 'N/A' }}</td>
                            <td class="text-center">
                                <span class="badge badge-{{ $operador->estatus === 'activo' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($operador->estatus ?? 'N/A') }}
                                </span>
                            </td>
                            <td>{{ $nombreObra }}</td>
                            <td>{{ $ubicacionObra }}</td>
                            <td class="text-center">{{ $avanceObra }}</td>
                            <td>{{ $responsableObra }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="pdf-no-data">
            <p>No se encontraron operadores con obras que coincidan con los criterios de filtrado aplicados.</p>
        </div>
    @endif

    <!-- Información adicional en el pie -->
    <div class="pdf-footer-info">
        <p><strong>Nota:</strong> Este reporte muestra información general de obras y responsables. Para informes más extensos, considere aplicar filtros adicionales o utilice el formato Excel.</p>
        <p><strong>Sistema:</strong> Solupatch Control Interno - Módulo de Operadores y Obras</p>
    </div>
@endsection

@section('additional-styles')
<style>
    .badge {
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 0.75em;
        font-weight: bold;
        color: white;
        display: inline-block;
    }
    
    .badge-success {
        background-color: #28a745;
    }
    
    .badge-secondary {
        background-color: #6c757d;
    }
    
    .text-center {
        text-align: center;
    }
</style>
@endsection