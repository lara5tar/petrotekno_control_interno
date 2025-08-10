@extends('layouts.app')

@section('title', 'Detalles de Asignación')

@section('header', 'Gestión de Asignaciones')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Asignaciones', 'url' => route('asignaciones-obra.index')],
        ['label' => 'Detalles de Asignación'
    ]" />

    {{-- Mensajes de estado --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- Header principal --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Asignación #{{ $asignacion->id }}</h2>
            <p class="text-sm text-gray-600 mt-1">
                @if($asignacion->esta_activa)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                            <circle cx="4" cy="4" r="3"/>
                        </svg>
                        Asignación Activa
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                            <circle cx="4" cy="4" r="3"/>
                        </svg>
                        Asignación Liberada
                    </span>
                @endif
            </p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('asignaciones-obra.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Volver al listado
            </a>
            
            @if($asignacion->esta_activa)
                <button onclick="abrirModalTransferir()" 
                        class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.707 3.293a1 1 0 010 1.414L5.414 7H11a7 7 0 017 7v2a1 1 0 11-2 0v-2A5 5 0 0011 9H5.414l2.293 2.293a1 1 0 11-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    Transferir
                </button>
                
                <button onclick="abrirModalLiberar()" 
                        class="bg-orange-600 hover:bg-orange-700 text-white font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd" />
                    </svg>
                    Liberar
                </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Información Principal --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center mb-6">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Información de la Asignación</h3>
                    <p class="text-sm text-gray-600">Detalles principales de la asignación</p>
                </div>
            </div>

            <dl class="space-y-4">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">Vehículo:</dt>
                    <dd class="text-sm text-gray-900 font-medium">
                        {{ $asignacion->vehiculo->marca }} {{ $asignacion->vehiculo->modelo }}
                        <span class="text-gray-500">({{ $asignacion->vehiculo->placas }})</span>
                    </dd>
                </div>
                
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">Obra:</dt>
                    <dd class="text-sm text-gray-900 font-medium">{{ $asignacion->obra->nombre_obra }}</dd>
                </div>
                
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">Operador:</dt>
                    <dd class="text-sm text-gray-900 font-medium">{{ $asignacion->personal->nombre_completo }}</dd>
                </div>
                
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">Asignado por:</dt>
                    <dd class="text-sm text-gray-900 font-medium">
                        {{ $asignacion->encargado->personal->nombre_completo ?? 'N/A' }}
                    </dd>
                </div>
                
                @if($asignacion->fecha_liberacion)
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-500">Fecha de liberación:</dt>
                        <dd class="text-sm text-gray-900 font-medium">
                            {{ $asignacion->fecha_liberacion->format('d/m/Y H:i') }}
                        </dd>
                    </div>
                @endif
                
                <div class="flex justify-between py-2">
                    <dt class="text-sm font-medium text-gray-500">Duración:</dt>
                    <dd class="text-sm text-gray-900 font-medium">
                        {{ $estadisticas['duracion_dias'] }} 
                        {{ $estadisticas['duracion_dias'] == 1 ? 'día' : 'días' }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Información Técnica --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center mb-6">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Información Técnica</h3>
                    <p class="text-sm text-gray-600">Datos técnicos y de rendimiento</p>
                </div>
            </div>

            <dl class="space-y-4">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">Kilometraje inicial:</dt>
                    <dd class="text-sm text-gray-900 font-mono">{{ number_format($asignacion->kilometraje_inicial) }} km</dd>
                </div>
                
                @if($asignacion->kilometraje_final)
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-500">Kilometraje final:</dt>
                        <dd class="text-sm text-gray-900 font-mono">{{ number_format($asignacion->kilometraje_final) }} km</dd>
                    </div>
                    
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-500">Kilometraje recorrido:</dt>
                        <dd class="text-sm text-gray-900 font-mono font-bold text-blue-600">
                            {{ number_format($estadisticas['kilometraje_recorrido']) }} km
                        </dd>
                    </div>
                @endif
                
                @if($asignacion->combustible_inicial)
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-500">Combustible inicial:</dt>
                        <dd class="text-sm text-gray-900 font-mono">{{ number_format($asignacion->combustible_inicial, 2) }} L</dd>
                    </div>
                @endif
                
                @if($asignacion->combustible_final)
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-500">Combustible final:</dt>
                        <dd class="text-sm text-gray-900 font-mono">{{ number_format($asignacion->combustible_final, 2) }} L</dd>
                    </div>
                @endif
                
                @if($estadisticas['suministrado'])
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-500">Combustible suministrado:</dt>
                        <dd class="text-sm text-gray-900 font-mono">{{ number_format($estadisticas['suministrado'], 2) }} L</dd>
                    </div>
                @endif
                
                @if($estadisticas['consumido'])
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-500">Combustible consumido:</dt>
                        <dd class="text-sm text-gray-900 font-mono">{{ number_format($estadisticas['consumido'], 2) }} L</dd>
                    </div>
                @endif
                
                @if($estadisticas['eficiencia'])
                    <div class="flex justify-between py-2">
                        <dt class="text-sm font-medium text-gray-500">Eficiencia:</dt>
                        <dd class="text-sm text-gray-900 font-mono font-bold text-green-600">
                            {{ number_format($estadisticas['eficiencia'], 2) }} km/L
                        </dd>
                    </div>
                @endif
            </dl>
        </div>

        {{-- Observaciones --}}
        @if($asignacion->observaciones)
            <div class="bg-white p-6 rounded-lg shadow-md lg:col-span-2">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Observaciones</h3>
                        <p class="text-sm text-gray-600">Notas y comentarios de la asignación</p>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $asignacion->observaciones }}</p>
                </div>
            </div>
        @endif

        {{-- Historial de Combustible --}}
        @if($asignacion->historial_combustible && count($asignacion->historial_combustible) > 0)
            <div class="bg-white p-6 rounded-lg shadow-md lg:col-span-2">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Historial de Combustible</h3>
                        <p class="text-sm text-gray-600">Registro de suministros de combustible</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Litros</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Costo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio/L</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estación</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kilometraje</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($asignacion->historial_combustible as $registro)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($registro['fecha'])->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                                        {{ number_format($registro['litros'], 2) }} L
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                                        ${{ number_format($registro['costo'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                                        ${{ number_format($registro['precio_por_litro'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $registro['estacion'] ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                                        {{ $registro['kilometraje_actual'] ? number_format($registro['kilometraje_actual']) . ' km' : 'N/A' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    function abrirModalLiberar() {
        // TODO: Implementar modal para liberar asignación
        alert('Modal de liberación - En desarrollo');
    }

    function abrirModalTransferir() {
        // TODO: Implementar modal para transferir asignación
        alert('Modal de transferencia - En desarrollo');
    }
</script>
@endpush
