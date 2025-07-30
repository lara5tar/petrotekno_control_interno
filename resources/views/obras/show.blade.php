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
        <div class="flex flex-col gap-4">
            <!-- Información General -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800">Información General</h3>
                </div>
                <div class="p-4 space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">ID:</span>
                        <span class="font-medium">#{{ $obra->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nombre:</span>
                        <span class="font-medium">{{ $obra->nombre_obra }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Estatus:</span>
                        <span class="px-2 py-1 rounded text-xs font-medium
                            @switch($obra->estatus)
                                @case('completada') bg-green-100 text-green-800 @break
                                @case('en_progreso') bg-blue-100 text-blue-800 @break
                                @case('pausada') bg-yellow-100 text-yellow-800 @break
                                @case('cancelada') bg-red-100 text-red-800 @break
                                @default bg-gray-100 text-gray-800
                            @endswitch
                        ">
                            {{ ucfirst(str_replace('_', ' ', $obra->estatus)) }}
                        </span>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Avance:</span>
                            <span class="font-medium">{{ $obra->avance ?? 0 }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="h-3 rounded-full transition-all duration-300
                                @if($obra->avance >= 100) bg-green-500
                                @elseif($obra->avance >= 75) bg-blue-500
                                @elseif($obra->avance >= 50) bg-yellow-500
                                @else bg-red-500
                                @endif
                            " style="width: {{ $obra->avance ?? 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de Fechas -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800">Fechas</h3>
                </div>
                <div class="p-4 space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Fecha Inicio:</span>
                        <span class="font-medium">
                            {{ $obra->fecha_inicio ? $obra->fecha_inicio->format('d/m/Y') : 'No definida' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Fecha Fin:</span>
                        <span class="font-medium">
                            {{ $obra->fecha_fin ? $obra->fecha_fin->format('d/m/Y') : 'No definida' }}
                        </span>
                    </div>
                    @if($obra->fecha_inicio && $obra->fecha_fin)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Duración:</span>
                        <span class="font-medium">
                            {{ $obra->fecha_inicio->diffInDays($obra->fecha_fin) }} días
                        </span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-600">Creada:</span>
                        <span class="font-medium">
                            {{ $obra->created_at ? $obra->created_at->format('d/m/Y H:i') : 'No disponible' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Actualizada:</span>
                        <span class="font-medium">
                            {{ $obra->updated_at->format('d/m/Y H:i') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Derecho -->
        <div class="flex flex-col gap-4">

            <!-- Asignaciones de la Obra -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-800">Asignaciones de la Obra</h3>
                    @hasPermission('crear_asignaciones')
                    <a href="{{ route('asignaciones.create', ['obra_id' => $obra->id]) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Nueva Asignación
                    </a>
                    @endhasPermission
                </div>
                <div class="p-4">
                    @if($obra->asignaciones->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehículo</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operador</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Asignación</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Km Inicial</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Km Final</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Encargado</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($obra->asignaciones as $asignacion)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="font-medium text-gray-900">{{ $asignacion->vehiculo->nombre_completo ?? 'N/A' }}</div>
                                                <div class="text-sm text-gray-500">{{ $asignacion->vehiculo->placas ?? 'Sin placas' }}</div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="font-medium text-gray-900">{{ $asignacion->personal->nombre_completo ?? 'N/A' }}</div>
                                                <div class="text-sm text-gray-500">{{ $asignacion->personal->categoria->nombre_categoria ?? 'Sin categoría' }}</div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                {{ $asignacion->fecha_asignacion ? \Carbon\Carbon::parse($asignacion->fecha_asignacion)->format('d/m/Y H:i') : 'N/A' }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                @if($asignacion->esta_activa)
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Activa</span>
                                                @else
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Liberada</span>
                                                    @if($asignacion->fecha_liberacion)
                                                        <div class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($asignacion->fecha_liberacion)->format('d/m/Y H:i') }}</div>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ number_format($asignacion->kilometraje_inicial ?? 0) }} km</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                @if($asignacion->kilometraje_final)
                                                    {{ number_format($asignacion->kilometraje_final) }} km
                                                @else
                                                    <span class="text-gray-400">Pendiente</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                {{ $asignacion->encargado->personal->nombre_completo ?? 'N/A' }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('asignaciones.show', $asignacion->id) }}" class="text-blue-600 hover:text-blue-900" title="Ver detalles">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </a>
                                                    @if($asignacion->esta_activa)
                                                        @hasPermission('editar_asignaciones')
                                                        <a href="{{ route('asignaciones.edit', $asignacion->id) }}" class="text-yellow-600 hover:text-yellow-900" title="Editar">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                        </a>
                                                        @endhasPermission
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <h5 class="text-lg font-medium text-gray-900 mb-2">No hay asignaciones para esta obra</h5>
                            <p class="text-gray-500 mb-4">Crea la primera asignación para comenzar a trabajar en esta obra.</p>
                            @hasPermission('crear_asignaciones')
                            <a href="{{ route('asignaciones.create', ['obra_id' => $obra->id]) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded font-medium transition-colors">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Crear Primera Asignación
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
