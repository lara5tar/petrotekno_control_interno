@extends('layouts.app')

@section('title', 'Detalles de la Obra')

@section('header', 'Detalles de la Obra')

@section('content')
{{-- Breadcrumb --}}
<x-breadcrumb :items="[
    ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
    ['label' => 'Obras', 'url' => route('obras.index')],
    ['label' => 'Detalle de la Obra']
]" />

<!-- Contenido Principal -->
<div class="p-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Panel Izquierdo - Datos Generales -->
        <div class="space-y-6">
            <!-- Datos Generales -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800">Datos Generales</h3>
                </div>
                <div class="p-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Nombre de la Obra</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->nombre_obra }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">ID de Obra</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ str_pad($obra->id, 6, '0', STR_PAD_LEFT) }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Estado</label>
                            <div class="bg-{{ $obra->estatus === 'en_progreso' ? 'green' : ($obra->estatus === 'completada' ? 'blue' : 'orange') }}-600 text-white px-3 py-2 rounded text-sm font-medium flex items-center">
                                <span class="w-2 h-2 bg-{{ $obra->estatus === 'en_progreso' ? 'green' : ($obra->estatus === 'completada' ? 'blue' : 'orange') }}-300 rounded-full mr-2"></span>
                                {{ ucfirst(str_replace('_', ' ', $obra->estatus)) }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Avance (%)</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->avance ?? 0 }}%
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Fecha Inicio</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->fecha_inicio ? \Carbon\Carbon::parse($obra->fecha_inicio)->format('d/m/Y') : 'No definida' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Fecha Fin</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->fecha_fin ? \Carbon\Carbon::parse($obra->fecha_fin)->format('d/m/Y') : 'No definida' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Asignación de Recursos -->
            @if($obra->vehiculo_id || $obra->operador_id || $obra->encargado_id)
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800">Asignación de Recursos</h3>
                </div>
                <div class="p-4 space-y-4">
                    @if($obra->vehiculo_id)
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Vehículo Asignado</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->vehiculo ? $obra->vehiculo->marca . ' ' . $obra->vehiculo->modelo . ' - ' . $obra->vehiculo->placas : 'Sin asignar' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Fecha Asignación</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->fecha_asignacion ? \Carbon\Carbon::parse($obra->fecha_asignacion)->format('d/m/Y') : 'No definida' }}
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($obra->operador_id || $obra->encargado_id)
                    <div class="grid grid-cols-2 gap-4">
                        @if($obra->operador_id)
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Operador</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->operador ? $obra->operador->nombre_completo : 'Sin asignar' }}
                            </div>
                        </div>
                        @endif
                        @if($obra->encargado_id)
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Encargado</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->encargado ? $obra->encargado->nombre_completo : 'Sin asignar' }}
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Kilometrajes -->
            @if($obra->kilometraje_inicial || $obra->kilometraje_final)
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800">Control de Kilometrajes</h3>
                </div>
                <div class="p-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Kilometraje Inicial</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->kilometraje_inicial ? number_format($obra->kilometraje_inicial) . ' km' : 'No registrado' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Kilometraje Final</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->kilometraje_final ? number_format($obra->kilometraje_final) . ' km' : 'No registrado' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Panel Derecho - Información Adicional -->
        <div class="space-y-6">
            <!-- Observaciones -->
            @if($obra->observaciones)
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800">Observaciones</h3>
                </div>
                <div class="p-4">
                    <div class="bg-gray-100 px-3 py-3 rounded text-sm border border-gray-300">
                        {{ $obra->observaciones }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Botones de Acción Flotantes -->
<div class="fixed bottom-6 right-6 flex space-x-3 z-50">
    @hasPermission('editar_obras')
    <!-- Botón Editar -->
    <a href="{{ route('obras.edit', $obra->id) }}" 
       class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded text-sm transition-colors duration-200 flex items-center space-x-2 shadow-lg"
       title="Editar Obra">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
        </svg>
        <span>Editar</span>
    </a>
    @endhasPermission

    @hasPermission('eliminar_obras')
    <!-- Botón Eliminar -->
    <form action="{{ route('obras.destroy', $obra->id) }}" 
          method="POST" 
          class="inline" 
          onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta obra? Esta acción no se puede deshacer.')">
        @csrf
        @method('DELETE')
        <button type="submit" 
                class="bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded text-sm transition-colors duration-200 flex items-center space-x-2 shadow-lg"
                title="Eliminar Obra">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9zM4 5a2 2 0 012-2h8a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm2.5 4a.5.5 0 01.5-.5h4a.5.5 0 01.5.5v2a.5.5 0 01-.5.5H7a.5.5 0 01-.5-.5V9z" clip-rule="evenodd" />
            </svg>
            <span>Eliminar</span>
        </button>
    </form>
    @endhasPermission
</div>

@endsection
