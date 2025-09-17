@extends('layouts.app')

@section('title', 'Historial de Obras por Activo')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Encabezado -->
        <div class="bg-white rounded-lg shadow-sm mb-6 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Historial de Obras por Activo</h1>
                    <p class="text-gray-600 mt-1">Seguimiento completo de asignaciones de activos a obras</p>
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
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Asignaciones</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($estadisticas['total_asignaciones']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Activas</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($estadisticas['asignaciones_activas']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Promedio Días</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $estadisticas['promedio_dias_asignacion'] ? round($estadisticas['promedio_dias_asignacion'], 1) : '0' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Km Recorridos</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($estadisticas['kilometraje_total_recorrido']) }}</dd>
                        </dl>
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
            
            <div id="filtros-panel" class="p-6 {{ request()->hasAny(['vehiculo_id', 'fecha_inicio', 'fecha_fin', 'estado_asignacion', 'obra_id']) ? '' : 'hidden' }}">
                <form method="GET" action="{{ route('reportes.historial-obras-vehiculo') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Filtro por Activo -->
                        <div>
                            <label for="vehiculo_id" class="block text-sm font-medium text-gray-700 mb-2">Activo</label>
                            <select name="vehiculo_id" id="vehiculo_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Todos los activos</option>
                                @foreach($vehiculosDisponibles as $vehiculo)
                                    <option value="{{ $vehiculo->id }}" {{ request('vehiculo_id') == $vehiculo->id ? 'selected' : '' }}>
                                        {{ $vehiculo->marca }} {{ $vehiculo->modelo }} {{ $vehiculo->anio }} - {{ $vehiculo->placas }}
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
                                        {{ $obra->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filtro por Estado -->
                        <div>
                            <label for="estado_asignacion" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                            <select name="estado_asignacion" id="estado_asignacion" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Todos los estados</option>
                                @foreach($estadosAsignacion as $estado)
                                    <option value="{{ $estado }}" {{ request('estado_asignacion') == $estado ? 'selected' : '' }}>
                                        {{ ucfirst($estado) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filtro por Fecha Inicio -->
                        <div>
                            <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">Fecha desde</label>
                            <input type="date" 
                                   name="fecha_inicio" 
                                   id="fecha_inicio" 
                                   value="{{ request('fecha_inicio') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Filtro por Fecha Fin -->
                        <div>
                            <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-2">Fecha hasta</label>
                            <input type="date" 
                                   name="fecha_fin" 
                                   id="fecha_fin" 
                                   value="{{ request('fecha_fin') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div class="flex items-center space-x-3 pt-4">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Aplicar Filtros
                        </button>
                        
                        <a href="{{ route('reportes.historial-obras-vehiculo') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Limpiar Filtros
                        </a>
                    </div>
                </form>
                
                <!-- Nota informativa sobre PDF -->
                <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-blue-900 mb-1">Exportación PDF</h4>
                            <p class="text-sm text-blue-800">
                                El reporte PDF está diseñado para generar el historial de un activo específico. 
                                <strong>Debe seleccionar un activo</strong> antes de poder descargar el PDF.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones de Exportación -->
        <div class="bg-white rounded-lg shadow-sm mb-6 p-4">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    Mostrando {{ number_format($asignaciones->count()) }} asignaciones
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('reportes.historial-obras-vehiculo', array_merge(request()->query(), ['formato' => 'excel'])) }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Exportar Excel
                    </a>
                    
                    <button onclick="validarDescargaPDF()" 
                       class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Exportar PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabla de Resultados -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            @if($asignaciones->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Obra</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operador</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fechas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kilometraje</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duración</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($asignaciones as $asignacion)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $asignacion->vehiculo ? "{$asignacion->vehiculo->marca} {$asignacion->vehiculo->modelo}" : 'Sin activo' }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $asignacion->vehiculo ? "{$asignacion->vehiculo->anio} - {$asignacion->vehiculo->placas}" : 'Sin datos' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $asignacion->obra->nombre_obra ?? 'Sin obra' }}
                                        </div>
                                        @if($asignacion->obra && $asignacion->obra->encargado)
                                            <div class="text-sm text-gray-500">
                                                Encargado: {{ $asignacion->obra->encargado->nombre }} {{ $asignacion->obra->encargado->apellido_paterno }}
                                            </div>
                                        @endif
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $asignacion->operador ? "{$asignacion->operador->nombre} {$asignacion->operador->apellido_paterno}" : 'Sin operador' }}
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <div>Inicio: {{ $asignacion->fecha_asignacion ? $asignacion->fecha_asignacion->format('d/m/Y') : 'Sin fecha' }}</div>
                                            @if($asignacion->fecha_liberacion)
                                                <div class="text-gray-500">Fin: {{ $asignacion->fecha_liberacion->format('d/m/Y') }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $asignacion->estado === 'activa' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $asignacion->estado === 'liberada' ? 'bg-gray-100 text-gray-800' : '' }}
                                            {{ $asignacion->estado === 'transferida' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                            {{ $asignacion->estado_formateado }}
                                        </span>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($asignacion->kilometraje_inicial || $asignacion->kilometraje_final)
                                            <div>Inicial: {{ $asignacion->kilometraje_inicial ? number_format($asignacion->kilometraje_inicial) : 'N/A' }}</div>
                                            <div>Final: {{ $asignacion->kilometraje_final ? number_format($asignacion->kilometraje_final) : 'N/A' }}</div>
                                            @if($asignacion->kilometraje_recorrido)
                                                <div class="font-medium text-blue-600">Recorrido: {{ number_format($asignacion->kilometraje_recorrido) }} km</div>
                                            @endif
                                        @else
                                            <span class="text-gray-400">Sin registro</span>
                                        @endif
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($asignacion->duracion_dias !== null)
                                            {{ $asignacion->duracion_dias }} día{{ $asignacion->duracion_dias != 1 ? 's' : '' }}
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No se encontraron asignaciones</h3>
                    <p class="mt-1 text-sm text-gray-500">No hay asignaciones que coincidan con los filtros aplicados.</p>
                    <div class="mt-6">
                        <a href="{{ route('reportes.historial-obras-vehiculo') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Ver todas las asignaciones
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Resumen adicional -->
        @if($asignaciones->count() > 0)
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Distribución por Estado</h3>
                    @php
                         $porcentajeActivas = $estadisticas['total_asignaciones'] > 0 ? round(($estadisticas['asignaciones_activas'] / $estadisticas['total_asignaciones']) * 100, 2) : 0;
                         $porcentajeLiberadas = $estadisticas['total_asignaciones'] > 0 ? round(($estadisticas['asignaciones_liberadas'] / $estadisticas['total_asignaciones']) * 100, 2) : 0;
                         $porcentajeTransferidas = $estadisticas['total_asignaciones'] > 0 ? round(($estadisticas['asignaciones_transferidas'] / $estadisticas['total_asignaciones']) * 100, 2) : 0;
                     @endphp
                     <div class="space-y-3">
                          <div class="flex justify-between items-center">
                              <span class="text-sm text-gray-600">Activas</span>
                              <div class="flex items-center">
                                  <div class="w-20 bg-gray-200 rounded-full h-2 mr-3">
                                      <div class="bg-green-500 h-2 rounded-full" data-width="{{ $porcentajeActivas }}"></div>
                                  </div>
                                  <span class="text-sm font-medium">{{ $estadisticas['asignaciones_activas'] }}</span>
                              </div>
                          </div>
                          <div class="flex justify-between items-center">
                              <span class="text-sm text-gray-600">Liberadas</span>
                              <div class="flex items-center">
                                  <div class="w-20 bg-gray-200 rounded-full h-2 mr-3">
                                      <div class="bg-gray-500 h-2 rounded-full" data-width="{{ $porcentajeLiberadas }}"></div>
                                  </div>
                                  <span class="text-sm font-medium">{{ $estadisticas['asignaciones_liberadas'] }}</span>
                              </div>
                          </div>
                          <div class="flex justify-between items-center">
                              <span class="text-sm text-gray-600">Transferidas</span>
                              <div class="flex items-center">
                                  <div class="w-20 bg-gray-200 rounded-full h-2 mr-3">
                                      <div class="bg-yellow-500 h-2 rounded-full" data-width="{{ $porcentajeTransferidas }}"></div>
                                  </div>
                                  <span class="text-sm font-medium">{{ $estadisticas['asignaciones_transferidas'] }}</span>
                              </div>
                          </div>
                      </div>
                      <script>
                          document.addEventListener('DOMContentLoaded', function() {
                              document.querySelectorAll('[data-width]').forEach(function(el) {
                                  el.style.width = el.getAttribute('data-width') + '%';
                              });
                          });
                      </script>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Resumen de Recursos</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Activos involucrados</span>
                            <span class="text-sm font-medium text-gray-900">{{ $estadisticas['vehiculos_involucrados'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Obras involucradas</span>
                            <span class="text-sm font-medium text-gray-900">{{ $estadisticas['obras_involucradas'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total kilómetros</span>
                            <span class="text-sm font-medium text-gray-900">{{ number_format($estadisticas['kilometraje_total_recorrido']) }} km</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Información Adicional</h3>
                    <div class="space-y-3">
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
        @endif
    </div>
</div>

<script>
function validarDescargaPDF() {
    const vehiculoId = document.querySelector('select[name="vehiculo_id"]').value;
    
    if (!vehiculoId) {
        // Mostrar alerta moderna
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Activo Requerido',
                text: 'Para generar el PDF debe seleccionar un activo específico en los filtros. El reporte PDF está diseñado para mostrar el historial de un activo individual.',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#f59e0b'
            });
        } else {
            alert('Para generar el PDF debe seleccionar un activo específico en los filtros.');
        }
        return;
    }
    
    // Si hay activo seleccionado, proceder con la descarga
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.set('formato', 'pdf');
    window.open(currentUrl.toString(), '_blank');
}
</script>
@endsection
