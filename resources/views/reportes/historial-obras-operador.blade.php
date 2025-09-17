@extends('layouts.app')

@section('title', 'Historial de Obras por Operador')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Encabezado -->
        <div class="bg-white rounded-lg shadow-sm mb-6 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Historial de Obras por Operador</h1>
                    <p class="text-gray-600 mt-1">Seguimiento completo de asignaciones y movimientos de operadores</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('reportes.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Volver a Reportes
                    </a>
                </div>
            </div>

            <!-- Filtros aplicados -->
            @if(isset($filtrosAplicados) && $filtrosAplicados['total_filtros'] > 0)
                <x-filtros-aplicados :filtros="$filtrosAplicados" />
            @endif
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Movimientos</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($estadisticas['total_movimientos']) }}</dd>
                    </div>
                    <div class="ml-4">
                        <div class="bg-blue-100 rounded-full p-2">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <dt class="text-sm font-medium text-gray-500 truncate">Asignaciones Iniciales</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($estadisticas['asignaciones_iniciales']) }}</dd>
                    </div>
                    <div class="ml-4">
                        <div class="bg-green-100 rounded-full p-2">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <dt class="text-sm font-medium text-gray-500 truncate">Cambios de Operador</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($estadisticas['cambios_operador']) }}</dd>
                    </div>
                    <div class="ml-4">
                        <div class="bg-yellow-100 rounded-full p-2">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <dt class="text-sm font-medium text-gray-500 truncate">Activos Diferentes</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($estadisticas['vehiculos_diferentes']) }}</dd>
                    </div>
                    <div class="ml-4">
                        <div class="bg-purple-100 rounded-full p-2">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="p-4 border-b border-gray-200">
                <button type="button" 
                        onclick="document.getElementById('filtros-panel').classList.toggle('hidden')"
                        class="flex items-center text-gray-700 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z"/>
                    </svg>
                    Filtros de Búsqueda
                </button>
            </div>
            
            <div id="filtros-panel" class="p-6 {{ request()->hasAny(['operador_id', 'fecha_inicio', 'fecha_fin', 'estado_asignacion', 'obra_id']) ? '' : 'hidden' }}">
                <form method="GET" action="{{ route('reportes.historial-obras-operador') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        
                        <!-- Filtro por Operador -->
                        <div>
                            <label for="operador_id" class="block text-sm font-medium text-gray-700 mb-2">Operador</label>
                            <select name="operador_id" id="operador_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Todos los operadores</option>
                                @foreach($operadoresDisponibles as $operador)
                                    <option value="{{ $operador->id }}" {{ request('operador_id') == $operador->id ? 'selected' : '' }}>
                                        {{ $operador->nombre_completo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filtro por Obra -->
                        <div>
                            <label for="obra_id" class="block text-sm font-medium text-gray-700 mb-2">Obra</label>
                            <select name="obra_id" id="obra_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Todas las obras</option>
                                @foreach($obrasDisponibles as $obra)
                                    <option value="{{ $obra->id }}" {{ request('obra_id') == $obra->id ? 'selected' : '' }}>
                                        {{ $obra->nombre_obra }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filtro por Fecha Inicio -->
                        <div>
                            <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ request('fecha_inicio') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Filtro por Fecha Fin -->
                        <div>
                            <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" value="{{ request('fecha_fin') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div class="flex space-x-3">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                            Aplicar Filtros
                        </button>
                        <a href="{{ route('reportes.historial-obras-operador') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors">
                            Limpiar Filtros
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Acciones de Exportación -->
        <div class="bg-white rounded-lg shadow-sm mb-6 p-4">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    Mostrando {{ number_format($asignaciones->count()) }} movimientos
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('reportes.historial-obras-operador', array_merge(request()->query(), ['formato' => 'excel'])) }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Exportar Excel
                    </a>
                    
                    <!-- Dropdown para PDF por Operador -->
                    <div class="relative inline-block text-left">
                        <button type="button" id="pdf-dropdown-button" 
                                class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Exportar PDF
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div id="pdf-dropdown-menu" class="hidden absolute right-0 mt-2 w-72 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                            <div class="py-1">
                                <div class="px-4 py-3 border-b border-gray-200">
                                    <h4 class="text-sm font-medium text-gray-900">Seleccionar Operador para PDF</h4>
                                    <p class="text-xs text-gray-500 mt-1">Genere un PDF individual con el historial completo de un operador específico</p>
                                </div>
                                
                                <div class="px-4 py-3">
                                    <label for="operador-pdf-select" class="block text-xs font-medium text-gray-700 mb-2">
                                        Operador:
                                    </label>
                                    <select id="operador-pdf-select" class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                        <option value="">Seleccionar operador...</option>
                                        @foreach($operadoresDisponibles as $operador)
                                            <option value="{{ $operador->id }}">
                                                {{ $operador->nombre_completo }}
                                            </option>
                                        @endforeach
                                    </select>
                                    
                                    <button onclick="descargarPDFOperador()" 
                                            class="w-full mt-3 bg-red-600 hover:bg-red-700 text-white font-medium text-sm py-2 px-3 rounded-md transition-colors flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Generar PDF del Operador
                                    </button>
                                </div>
                                
                                <div class="px-4 py-2 bg-gray-50 border-t border-gray-200">
                                    <p class="text-xs text-gray-500">
                                        💡 El PDF incluirá todo el historial de asignaciones y movimientos del operador seleccionado
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Resultados -->
        @if($asignaciones->count() > 0)
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fecha y Hora
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tipo Movimiento
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Activo
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Operador Anterior
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Operador Nuevo
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Obra
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Usuario
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($asignaciones as $asignacion)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $asignacion->fecha_asignacion->format('d/m/Y') }}
                                        <br>
                                        <span class="text-xs text-gray-500">{{ $asignacion->fecha_asignacion->format('H:i') }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $tipoClasses = [
                                                'asignacion_inicial' => 'bg-green-100 text-green-800',
                                                'cambio_operador' => 'bg-yellow-100 text-yellow-800',
                                                'remocion_operador' => 'bg-red-100 text-red-800'
                                            ];
                                            $tipoTextos = [
                                                'asignacion_inicial' => 'Asignación Inicial',
                                                'cambio_operador' => 'Cambio de Operador',
                                                'remocion_operador' => 'Remoción de Operador'
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $tipoClasses[$asignacion->tipo_movimiento] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $tipoTextos[$asignacion->tipo_movimiento] ?? ucfirst(str_replace('_', ' ', $asignacion->tipo_movimiento)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($asignacion->vehiculo)
                                            <div class="font-medium">{{ $asignacion->vehiculo->marca }} {{ $asignacion->vehiculo->modelo }}</div>
                                            <div class="text-gray-500">{{ $asignacion->vehiculo->placas }} ({{ $asignacion->vehiculo->anio }})</div>
                                        @else
                                            <span class="text-gray-400">Sin activo</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($asignacion->operadorAnterior)
                                            {{ $asignacion->operadorAnterior->nombre_completo }}
                                        @else
                                            <span class="text-gray-400">Sin operador anterior</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($asignacion->operadorNuevo)
                                            {{ $asignacion->operadorNuevo->nombre_completo }}
                                        @else
                                            <span class="text-gray-400">Sin operador nuevo</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($asignacion->obra)
                                            {{ $asignacion->obra->nombre_obra }}
                                        @else
                                            <span class="text-gray-400">Sin obra asignada</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $asignacion->usuarioAsigno ? $asignacion->usuarioAsigno->email : 'Sistema' }}
                                    </td>
                                </tr>

                                <!-- Fila expandible con detalles -->
                                @if($asignacion->motivo || $asignacion->observaciones)
                                    <tr class="bg-gray-50">
                                        <td colspan="7" class="px-6 py-3">
                                            <div class="text-xs text-gray-600">
                                                @if($asignacion->motivo)
                                                    <div class="mb-1"><strong>Motivo:</strong> {{ $asignacion->motivo }}</div>
                                                @endif
                                                @if($asignacion->observaciones)
                                                    <div><strong>Observaciones:</strong> {{ $asignacion->observaciones }}</div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <!-- Estado vacío -->
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No se encontraron movimientos</h3>
                <p class="mt-1 text-sm text-gray-500">No hay movimientos de operadores que coincidan con los filtros aplicados.</p>
                <div class="mt-6">
                    <a href="{{ route('reportes.historial-obras-operador') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Limpiar Filtros
                    </a>
                </div>
            </div>
        @endif

        <!-- Información adicional -->
        @if($asignaciones->count() > 0)
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Información del Reporte</h3>
                        <div class="mt-2 text-sm text-blue-700 space-y-1">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Fecha de generación</span>
                                <span class="text-sm font-medium text-gray-900">{{ now()->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Total de registros</span>
                                <span class="text-sm font-medium text-gray-900">{{ number_format($asignaciones->count()) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
// JavaScript para manejar dropdown de PDF
document.addEventListener('DOMContentLoaded', function() {
    const dropdownButton = document.getElementById('pdf-dropdown-button');
    const dropdownMenu = document.getElementById('pdf-dropdown-menu');

    if (dropdownButton && dropdownMenu) {
        dropdownButton.addEventListener('click', function() {
            dropdownMenu.classList.toggle('hidden');
        });

        // Cerrar dropdown al hacer click fuera
        document.addEventListener('click', function(event) {
            if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
                dropdownMenu.classList.add('hidden');
            }
        });
    }
});

function descargarPDFOperador() {
    const operadorId = document.getElementById('operador-pdf-select').value;
    
    if (!operadorId) {
        // Mostrar alerta moderna
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Operador Requerido',
                text: 'Debe seleccionar un operador para generar el PDF individual.',
                confirmButtonColor: '#dc2626'
            });
        } else {
            alert('Debe seleccionar un operador para generar el PDF individual.');
        }
        return;
    }

    // Construir URL con filtros actuales más el operador seleccionado
    const params = new URLSearchParams(window.location.search);
    params.set('operador_id', operadorId);
    params.set('formato', 'pdf');
    
    const url = `{{ route('reportes.historial-obras-operador') }}?${params.toString()}`;
    window.location.href = url;
}
</script>
@endsection
