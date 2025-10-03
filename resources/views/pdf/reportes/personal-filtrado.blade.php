@extends('pdf.layouts.base')

@section('title', 'Reporte de Personal Filtrado')
@section('report-title', 'Reporte de Personal Filtrado')
@section('report-subtitle', 'Listado de personal según criterios de filtrado aplicados')

@section('content')
    <!-- Sección de Estadísticas -->
    @if(isset($estadisticas) && count($estadisticas) > 0)
        <div class="pdf-stats-section">
            <h3 class="stats-title">Resumen Ejecutivo</h3>
            <div class="stats-grid">
                <div class="stats-row">
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['total'] ?? 0 }}</span>
                        <span class="stat-label">Total Personal</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['activos'] ?? 0 }}</span>
                        <span class="stat-label">Activos</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['inactivos'] ?? 0 }}</span>
                        <span class="stat-label">Inactivos</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Información de Filtros Aplicados -->
    @if(isset($filtros) && count(array_filter($filtros)) > 0)
        <div class="pdf-info-section">
            <h3 class="info-title">Filtros Aplicados</h3>
            <div class="info-grid">
                @if(isset($filtros['buscar']) && $filtros['buscar'])
                    <div class="info-row">
                        <div class="info-label">Búsqueda:</div>
                        <div class="info-value">{{ $filtros['buscar'] }}</div>
                    </div>
                @endif
                @if(isset($filtros['estatus']) && $filtros['estatus'])
                    <div class="info-row">
                        <div class="info-label">Estado Filtrado:</div>
                        <div class="info-value">{{ ucfirst($filtros['estatus']) }}</div>
                    </div>
                @endif
                @if(isset($filtros['categoria_id']) && $filtros['categoria_id'])
                    <div class="info-row">
                        <div class="info-label">Categoría Filtrada:</div>
                        <div class="info-value">ID {{ $filtros['categoria_id'] }}</div>
                    </div>
                @endif
                <div class="info-row">
                    <div class="info-label">Total de Registros:</div>
                    <div class="info-value">{{ count($personal) }} empleados</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Fecha de Generación:</div>
                    <div class="info-value">{{ now()->format('d/m/Y H:i:s') }}</div>
                </div>
            </div>
        </div>
    @endif

    <!-- Espacio para separación entre secciones -->
    <div style="margin-bottom: 20px;"></div>

    <!-- Tabla Principal de Personal -->
    <table class="pdf-table">
        <thead>
            <tr>
                <th style="width: 5%;">ID</th>
                <th style="width: 20%;">Nombre Completo</th>
                <th style="width: 13%;">Categoría</th>
                <th style="width: 10%;">RFC</th>
                <th style="width: 12%;">CURP</th>
                <th style="width: 10%;">NSS</th>
                <th style="width: 8%;">INE</th>
                <th style="width: 10%;">Licencia</th>
                <th style="width: 6%;">Estado</th>
                <th style="width: 8%;">Fecha Reg.</th>
            </tr>
        </thead>
        <tbody>
            @forelse($personal as $index => $persona)
                <tr>
                    <td class="text-center">{{ $persona->id }}</td>
                    <td class="text-bold">{{ $persona->nombre_completo }}</td>
                    <td class="text-center font-small">{{ $persona->categoria->nombre_categoria ?? 'Sin categoría' }}</td>
                    <td class="text-center font-small">{{ $persona->rfc ?: 'N/A' }}</td>
                    <td class="text-center font-small">{{ $persona->curp_numero ?: 'N/A' }}</td>
                    <td class="text-center font-small">{{ $persona->nss ?: 'N/A' }}</td>
                    <td class="text-center font-small">{{ $persona->ine ?: 'N/A' }}</td>
                    <td class="text-center font-small">{{ $persona->no_licencia ?: 'N/A' }}</td>
                    <td class="text-center">
                        @php
                            $statusClass = $persona->estatus === 'activo' ? 'status-activo' : 'status-fuera-servicio';
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ ucfirst($persona->estatus) }}
                        </span>
                    </td>
                    <td class="text-center font-small">
                        {{ $persona->created_at ? $persona->created_at->format('d/m/Y') : 'N/A' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center text-muted p-15">
                        No se encontró personal con los criterios especificados
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Resumen por Categorías -->
    @if(count($personal) > 0)
        <div class="pdf-stats-section mt-20">
            <h3 class="stats-title">Distribución por Categoría</h3>
            <div class="stats-grid">
                <div class="stats-row">
                    @php
                        $categorias = $personal->groupBy('categoria.nombre_categoria');
                        $topCategorias = $categorias->sortByDesc(function($items) {
                            return $items->count();
                        })->take(4);
                    @endphp
                    @foreach($topCategorias as $categoria => $items)
                        <div class="stat-item">
                            <span class="stat-number">{{ $items->count() }}</span>
                            <span class="stat-label">{{ $categoria ?: 'Sin categoría' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Notas adicionales -->
    <div class="pdf-notes-section mt-20" style="background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; padding: 12px; page-break-inside: avoid;">
        <h3 style="color: #2c3e50; font-size: 13px; font-weight: bold; margin-bottom: 12px; border-bottom: 1px solid #dee2e6; padding-bottom: 5px; text-transform: uppercase;">Notas Importantes</h3>
        <ul style="list-style-type: disc; padding-left: 20px; margin: 0;">
            <li style="margin-bottom: 5px; color: #2c3e50; font-size: 10px;">Este reporte incluye únicamente el personal que cumple con los criterios de filtrado aplicados.</li>
            <li style="margin-bottom: 5px; color: #2c3e50; font-size: 10px;">Los datos mostrados corresponden a la información más actualizada en el sistema.</li>
            <li style="margin-bottom: 5px; color: #2c3e50; font-size: 10px;"><strong>Información incluida:</strong> ID, Nombre, Categoría, RFC, CURP, NSS, INE, Licencia de Conducir, Estado y Fecha de Registro.</li>
            <li style="margin-bottom: 5px; color: #2c3e50; font-size: 10px;">El estado de cada empleado refleja su situación actual en el sistema de control interno (activo, inactivo, suspendido, vacaciones).</li>
            <li style="margin-bottom: 5px; color: #2c3e50; font-size: 10px;">La información personal (RFC, CURP, NSS, INE) es <strong>confidencial</strong> y debe manejarse de acuerdo a las políticas de privacidad y protección de datos personales.</li>
            @if(isset($filtros['buscar']) && $filtros['buscar'])
                <li style="margin-bottom: 5px; color: #2c3e50; font-size: 10px;">La búsqueda aplicada incluye coincidencias en: nombre completo, RFC, CURP, NSS e INE.</li>
            @endif
            <li style="margin-bottom: 5px; color: #2c3e50; font-size: 10px;">Los campos marcados como "N/A" indican que la información no ha sido registrada en el sistema.</li>
        </ul>
    </div>
@endsection
