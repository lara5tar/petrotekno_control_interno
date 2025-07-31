@extends('layouts.app')

@section('title', 'Detalles de la Obra')

@section('content')
<!-- Breadcrumb -->
<x-breadcrumb :items="[
    ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
    ['label' => 'Obras', 'url' => route('obras.index')],
    ['label' => 'Detalle de la Obra']
]" />

<div class="h-[calc(100vh-120px)] flex flex-col gap-4">
    <!-- Header -->
    <div class="bg-white border border-gray-300 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-lg font-bold text-gray-900">
                    {{ $obra->nombre_obra }}
                </h1>
                <p class="text-sm text-gray-600">ID: <span class="font-medium">#{{ $obra->id }}</span></p>
            </div>
            
            <div class="flex space-x-2">
                <a href="{{ route('asignaciones-obra.index') }}?obra_id={{ $obra->id }}" 
                   class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-2 rounded text-sm transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Asignaciones
                </a>

                <a href="{{ route('asignaciones-obra.create') }}?obra_id={{ $obra->id }}" 
                   class="bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-2 rounded text-sm transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Nueva Asignación
                </a>
                
                @hasPermission('editar_obras')
                <a href="{{ route('obras.edit', $obra) }}" 
                   class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded text-sm transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Editar
                </a>
                @endhasPermission
                
                <a href="{{ route('obras.index') }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded text-sm transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Mensajes de éxito y error -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Contenido Principal en Grid 50/50 -->
    <div class="flex-1 grid grid-cols-2 gap-4">
        <!-- Panel Izquierdo -->
                <!-- Panel Izquierdo -->
        <div class="flex flex-col gap-4">

            <!-- Información General de la Obra -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800">Información General</h3>
                </div>
                <div class="p-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Obra</label>
                        <p class="text-lg font-medium text-gray-900">{{ $obra->nombre_obra }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                                @if($obra->estatus === 'activo') bg-green-100 text-green-800
                                @elseif($obra->estatus === 'en_proceso') bg-blue-100 text-blue-800
                                @elseif($obra->estatus === 'pausado') bg-yellow-100 text-yellow-800
                                @elseif($obra->estatus === 'completado') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $obra->estatus)) }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Avance</label>
                            <div class="flex items-center space-x-2">
                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $obra->avance ?? 0 }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $obra->avance ?? 0 }}%</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Inicio</label>
                            <p class="text-gray-900">
                                @if($obra->fecha_inicio)
                                    {{ \Carbon\Carbon::parse($obra->fecha_inicio)->format('d/m/Y') }}
                                @else
                                    <span class="text-gray-400">No definida</span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Fin</label>
                            <p class="text-gray-900">
                                @if($obra->fecha_fin)
                                    {{ \Carbon\Carbon::parse($obra->fecha_fin)->format('d/m/Y') }}
                                @else
                                    <span class="text-gray-400">No definida</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($obra->fecha_inicio && $obra->fecha_fin)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Duración Estimada</label>
                            <p class="text-gray-900">
                                {{ \Carbon\Carbon::parse($obra->fecha_inicio)->diffInDays(\Carbon\Carbon::parse($obra->fecha_fin)) }} días
                            </p>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                        <div>
                            <label class="block font-medium">Creado</label>
                            <p>{{ $obra->fecha_creacion ? \Carbon\Carbon::parse($obra->fecha_creacion)->format('d/m/Y H:i') : 'N/A' }}</p>
                        </div>
                        @if($obra->fecha_actualizacion)
                        <div>
                            <label class="block font-medium">Actualizado</label>
                            <p>{{ \Carbon\Carbon::parse($obra->fecha_actualizacion)->format('d/m/Y H:i') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Estadísticas y Métricas -->
            @if($obra->vehiculo_id || $obra->kilometraje_inicial || $obra->kilometraje_final)
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800">Estadísticas de Operación</h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 gap-4">
                        @if($obra->kilometraje_inicial && $obra->kilometraje_final)
                            <div class="bg-blue-50 p-3 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-blue-800">Kilometraje Recorrido</span>
                                    <span class="text-lg font-bold text-blue-900">
                                        {{ number_format($obra->kilometraje_final - $obra->kilometraje_inicial) }} km
                                    </span>
                                </div>
                            </div>
                        @endif

                        @if($obra->fecha_inicio && $obra->fecha_fin && $obra->estatus === 'completado')
                            <div class="bg-green-50 p-3 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-green-800">Duración Real</span>
                                    <span class="text-lg font-bold text-green-900">
                                        {{ \Carbon\Carbon::parse($obra->fecha_inicio)->diffInDays(\Carbon\Carbon::parse($obra->fecha_fin)) }} días
                                    </span>
                                </div>
                            </div>
                        @elseif($obra->fecha_inicio)
                            <div class="bg-yellow-50 p-3 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-yellow-800">Días Transcurridos</span>
                                    <span class="text-lg font-bold text-yellow-900">
                                        {{ \Carbon\Carbon::parse($obra->fecha_inicio)->diffInDays(now()) }} días
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

        </div>

        <!-- Panel Derecho -->
        <div class="flex flex-col gap-4">

            <!-- Información de Asignación -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800">Asignación de Recursos</h3>
                </div>
                <div class="p-4">
                    @if($obra->vehiculo_id || $obra->operador_id || $obra->encargado_id)
                        <div class="space-y-4">
                            <!-- Vehículo Asignado -->
                            @if($obra->vehiculo_id && $obra->vehiculo)
                                <div class="flex items-start space-x-3 p-3 bg-blue-50 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0M15 17a2 2 0 104 0"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">Vehículo Asignado</h4>
                                        <p class="text-sm text-gray-600">{{ $obra->vehiculo->marca }} {{ $obra->vehiculo->modelo }}</p>
                                        <p class="text-sm text-gray-600">Placas: <span class="font-medium">{{ $obra->vehiculo->placas }}</span></p>
                                        @if($obra->kilometraje_inicial)
                                            <p class="text-sm text-gray-600">Km Inicial: <span class="font-medium">{{ number_format($obra->kilometraje_inicial) }} km</span></p>
                                        @endif
                                        @if($obra->kilometraje_final)
                                            <p class="text-sm text-gray-600">Km Final: <span class="font-medium">{{ number_format($obra->kilometraje_final) }} km</span></p>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Operador Asignado -->
                            @if($obra->operador_id && $obra->operador)
                                <div class="flex items-start space-x-3 p-3 bg-green-50 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">Operador Asignado</h4>
                                        <p class="text-sm text-gray-600">{{ $obra->operador->nombre_completo }}</p>
                                        <p class="text-sm text-gray-600">
                                            Estado: 
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                @if($obra->operador->estatus === 'activo') bg-green-100 text-green-800 
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($obra->operador->estatus) }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            @endif

                            <!-- Encargado Asignado -->
                            @if($obra->encargado_id && $obra->encargado)
                                <div class="flex items-start space-x-3 p-3 bg-purple-50 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">Encargado de Obra</h4>
                                        <p class="text-sm text-gray-600">{{ $obra->encargado->nombre_completo }}</p>
                                        <p class="text-sm text-gray-600">
                                            Estado: 
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                @if($obra->encargado->estatus === 'activo') bg-green-100 text-green-800 
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($obra->encargado->estatus) }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            @endif

                            <!-- Fechas de Asignación -->
                            @if($obra->fecha_asignacion || $obra->fecha_liberacion)
                                <div class="flex items-start space-x-3 p-3 bg-yellow-50 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">Fechas de Asignación</h4>
                                        @if($obra->fecha_asignacion)
                                            <p class="text-sm text-gray-600">
                                                Fecha de asignación: 
                                                <span class="font-medium">{{ \Carbon\Carbon::parse($obra->fecha_asignacion)->format('d/m/Y') }}</span>
                                            </p>
                                        @endif
                                        @if($obra->fecha_liberacion)
                                            <p class="text-sm text-gray-600">
                                                Fecha de liberación: 
                                                <span class="font-medium">{{ \Carbon\Carbon::parse($obra->fecha_liberacion)->format('d/m/Y') }}</span>
                                            </p>
                                        @else
                                            <p class="text-sm text-gray-600">
                                                Estado: 
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    Activa
                                                </span>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Observaciones -->
                            @if($obra->observaciones)
                                <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">Observaciones</h4>
                                        <p class="text-sm text-gray-600">{{ $obra->observaciones }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <h5 class="text-lg font-medium text-gray-900 mb-2">Sin asignación de recursos</h5>
                            <p class="text-gray-500 mb-4">Esta obra no tiene vehículos, operadores o encargados asignados.</p>
                            @hasPermission('editar_obras')
                            <a href="{{ route('obras.edit', $obra) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded font-medium transition-colors">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Editar y Asignar Recursos
                            </a>
                            @endhasPermission
                        </div>
                    @endif
                </div>
            </div>

            <!-- Acciones -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800">Acciones</h3>
                </div>
                <div class="p-4">
                    <div class="flex flex-wrap gap-2">
                        @hasPermission('editar_obras')
                        <a href="{{ route('obras.edit', $obra) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded text-sm font-medium transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Editar Obra
                        </a>
                        @endhasPermission
                        
                        @hasPermission('eliminar_obras')
                        @if($obra->trashed())
                            <form action="{{ route('obras.restore', $obra->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded text-sm font-medium transition-colors flex items-center" onclick="return confirm('¿Restaurar esta obra?')">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                    </svg>
                                    Restaurar
                                </button>
                            </form>
                        @else
                            <form action="{{ route('obras.destroy', $obra) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm font-medium transition-colors flex items-center" onclick="return confirm('¿Estás seguro de eliminar esta obra?')">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Eliminar
                                </button>
                            </form>
                        @endif
                        @endhasPermission
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
