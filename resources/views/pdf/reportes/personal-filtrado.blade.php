@extends('pdf.layouts.base')

@section('title', 'Reporte de Personal Filtrado')
@section('report-title', 'Reporte de Personal Filtrado')
@section('report-subtitle', 'Listado de personal según criterios de filtrado aplicados')

@section('additional-styles')
<style>
    /* Configuración específica para orientación horizontal */
    @page {
        margin: 1cm 0.8cm 1.5cm 0.8cm;
        size: A4 landscape; /* Cambiar a horizontal para más espacio */
    }
    
    body {
        font-size: 9px; /* Reducir fuente base */
    }
    
    /* Optimización específica para tabla de personal */
    .personal-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        font-size: 8px; /* Fuente muy pequeña para que quepa */
        table-layout: fixed; /* Forzar ancho fijo */
    }
    
    .personal-table th {
        background-color: var(--color-primary);
        color: white;
        font-weight: bold;
        font-size: 7px;
        text-transform: uppercase;
        letter-spacing: 0.2px;
        padding: 4px 2px;
        text-align: center;
        border: 1px solid var(--color-primary-dark);
        vertical-align: middle;
        line-height: 1.1;
    }
    
    .personal-table td {
        padding: 3px 2px;
        border: 1px solid var(--color-border);
        vertical-align: top;
        font-size: 7px;
        line-height: 1.2;
        word-wrap: break-word;
        overflow: hidden;
    }
    
    .personal-table tr:nth-child(even) {
        background-color: #fafbfc;
    }
    
    /* Anchos específicos optimizados para landscape */
    .col-id { width: 4%; }
    .col-nombre { width: 18%; }
    .col-categoria { width: 12%; }
    .col-rfc { width: 9%; }
    .col-curp { width: 12%; }
    .col-nss { width: 9%; }
    .col-ine { width: 8%; }
    .col-licencia { width: 9%; }
    .col-estado { width: 8%; }
    .col-fecha { width: 11%; }
    
    /* Estilos específicos para personal */
    .personal-status-badge {
        padding: 1px 3px;
        border-radius: 2px;
        font-size: 6px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.1px;
        white-space: nowrap;
        display: inline-block;
    }
    
    .personal-nombre {
        font-weight: bold;
        font-size: 8px;
    }
    
    .personal-code {
        font-family: 'Courier New', monospace;
        font-size: 6px;
        letter-spacing: 0.5px;
    }
    
    /* Ajustes para estadísticas en landscape */
    .stats-compact {
        display: table;
        width: 100%;
        margin-bottom: 15px;
    }
    
    .stats-compact .stat-item {
        display: table-cell;
        width: 16.66%;
        text-align: center;
        padding: 8px 4px;
        border-right: 1px solid var(--color-border);
    }
    
    .stats-compact .stat-item:last-child {
        border-right: none;
    }
    
    .stats-compact .stat-number {
        font-size: 14px;
        font-weight: bold;
        color: var(--color-primary);
        margin-bottom: 2px;
        display: block;
    }
    
    .stats-compact .stat-label {
        font-size: 8px;
        color: var(--color-text-light);
        text-transform: uppercase;
        letter-spacing: 0.2px;
    }
</style>
@endsection

@section('content')
    <!-- Sección de Estadísticas Compacta -->
    @if(isset($estadisticas) && count($estadisticas) > 0)
        <div class="pdf-stats-section">
            <h3 class="stats-title" style="font-size: 11px; margin-bottom: 8px;">Resumen Ejecutivo</h3>
            <div class="stats-compact">
                <div class="stat-item">
                    <span class="stat-number">{{ $estadisticas['total'] ?? 0 }}</span>
                    <span class="stat-label">Total</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ $estadisticas['activos'] ?? 0 }}</span>
                    <span class="stat-label">Activos</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ $estadisticas['inactivos'] ?? 0 }}</span>
                    <span class="stat-label">Inactivos</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ isset($estadisticas['suspendidos']) ? $estadisticas['suspendidos'] : 0 }}</span>
                    <span class="stat-label">Suspendidos</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ count($personal) }}</span>
                    <span class="stat-label">Mostrados</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ now()->format('d/m/Y') }}</span>
                    <span class="stat-label">Fecha</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Información de Filtros Aplicados (Compacta) -->
    @if(isset($filtros) && count(array_filter($filtros)) > 0)
        <div style="background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 3px; padding: 8px; margin-bottom: 15px; font-size: 8px;">
            <strong style="color: #2c3e50; font-size: 9px;">Filtros:</strong>
            @if(isset($filtros['buscar']) && $filtros['buscar'])
                <span style="margin-right: 15px;">🔍 Búsqueda: "{{ $filtros['buscar'] }}"</span>
            @endif
            @if(isset($filtros['estatus']) && $filtros['estatus'])
                <span style="margin-right: 15px;">📊 Estado: {{ ucfirst($filtros['estatus']) }}</span>
            @endif
            @if(isset($filtros['categoria_id']) && $filtros['categoria_id'])
                <span style="margin-right: 15px;">🏷️ Puesto: ID {{ $filtros['categoria_id'] }}</span>
            @endif
            <span>📋 Total: {{ count($personal) }} registros</span>
        </div>
    @endif

    <!-- Tabla Principal de Personal Optimizada -->
    <table class="personal-table">
        <thead>
            <tr>
                <th class="col-id">ID</th>
                <th class="col-nombre">Nombre Completo</th>
                <th class="col-categoria">Puesto</th>
                <th class="col-rfc">RFC</th>
                <th class="col-curp">CURP</th>
                <th class="col-nss">NSS</th>
                <th class="col-ine">INE</th>
                <th class="col-licencia">No. Licencia</th>
                <th class="col-estado">Estado</th>
                <th class="col-fecha">Fecha Registro</th>
            </tr>
        </thead>
        <tbody>
            @forelse($personal as $index => $persona)
                <tr>
                    <td class="text-center">{{ $persona->id }}</td>
                    <td class="personal-nombre">{{ $persona->nombre_completo }}</td>
                    <td class="text-center">{{ $persona->categoria->nombre_categoria ?? 'Sin puesto' }}</td>
                    <td class="text-center personal-code">{{ $persona->rfc ?: 'N/A' }}</td>
                    <td class="text-center personal-code">{{ $persona->curp_numero ?: 'N/A' }}</td>
                    <td class="text-center personal-code">{{ $persona->nss ?: 'N/A' }}</td>
                    <td class="text-center personal-code">{{ $persona->ine ?: 'N/A' }}</td>
                    <td class="text-center personal-code">{{ $persona->no_licencia ?: 'N/A' }}</td>
                    <td class="text-center">
                        @php
                            $statusClass = '';
                            switch($persona->estatus) {
                                case 'activo':
                                    $statusClass = 'status-activo';
                                    break;
                                case 'inactivo':
                                    $statusClass = 'status-fuera-servicio';
                                    break;
                                case 'suspendido':
                                    $statusClass = 'status-baja';
                                    break;
                                default:
                                    $statusClass = 'status-fuera-servicio';
                            }
                        @endphp
                        <span class="personal-status-badge {{ $statusClass }}">
                            {{ ucfirst($persona->estatus) }}
                        </span>
                    </td>
                    <td class="text-center personal-code">
                        {{ $persona->created_at ? $persona->created_at->format('d/m/y') : 'N/A' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center text-muted" style="padding: 15px; font-size: 9px;">
                        No se encontró personal con los criterios especificados
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Resumen por Categorías (Solo si hay datos) -->
    @if(count($personal) > 0)
        <div style="margin-top: 15px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 3px; padding: 10px; page-break-inside: avoid;">
            <h3 style="color: #2c3e50; font-size: 10px; font-weight: bold; margin-bottom: 8px;">Distribución por Puesto</h3>
            <div style="display: table; width: 100%;">
                @php
                    $categorias = $personal->groupBy('categoria.nombre_categoria');
                    $topCategorias = $categorias->sortByDesc(function($items) {
                        return $items->count();
                    })->take(6);
                @endphp
                @foreach($topCategorias as $categoria => $items)
                    <div style="display: table-cell; text-align: center; padding: 5px; border-right: 1px solid #dee2e6; width: {{ 100/6 }}%;">
                        <div style="font-size: 12px; font-weight: bold; color: #f39c12;">{{ $items->count() }}</div>
                        <div style="font-size: 7px; color: #7f8c8d;">{{ Str::limit($categoria ?: 'Sin puesto', 15) }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Notas compactas -->
    <div style="background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 3px; padding: 8px; margin-top: 15px; page-break-inside: avoid;">
        <h3 style="color: #2c3e50; font-size: 9px; font-weight: bold; margin-bottom: 6px;">Notas Importantes</h3>
        <div style="font-size: 7px; color: #2c3e50; line-height: 1.3;">
            • Este reporte incluye únicamente el personal que cumple con los criterios de filtrado aplicados. 
            • Los datos corresponden a la información más actualizada en el sistema. 
            • La información personal (RFC, CURP, NSS, INE) es <strong>confidencial</strong> y debe manejarse según políticas de privacidad. 
            • Los campos "N/A" indican información no registrada en el sistema.
            @if(isset($filtros['buscar']) && $filtros['buscar'])
                • La búsqueda incluye coincidencias en: nombre, RFC, CURP, NSS e INE.
            @endif
        </div>
    </div>
@endsection
