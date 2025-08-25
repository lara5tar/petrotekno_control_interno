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

    <!-- Contenido Principal en Grid 50/50 -->
    <div class="flex-1 grid grid-cols-2 gap-4">
        <!-- Panel Izquierdo -->
        <div class="flex flex-col gap-4">
            <!-- Datos Generales -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800">Datos Generales</h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Nombre de la Obra</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->nombre_obra }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">ID</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ str_pad($obra->id, 6, '0', STR_PAD_LEFT) }}
                            </div>
                        </div>
                    </div>
                    
                    @if($obra->ubicacion)
                    <div class="grid grid-cols-1 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Ubicación</label>
                            <div class="bg-blue-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->ubicacion }}
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Fecha de Inicio</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->fecha_inicio ? \Carbon\Carbon::parse($obra->fecha_inicio)->format('d/m/Y') : 'No definida' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Fecha de Finalización</label>
                            <div class="bg-{{ $obra->fecha_fin && \Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($obra->fecha_fin)) ? 'red' : 'gray' }}-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->fecha_fin ? \Carbon\Carbon::parse($obra->fecha_fin)->format('d/m/Y') : 'No definida' }}
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Avance</label>
                            <div class="bg-gray-100 rounded-full h-3 mt-1">
                                <div class="bg-{{ $obra->avance >= 100 ? 'green' : ($obra->avance >= 75 ? 'blue' : ($obra->avance >= 50 ? 'yellow' : 'orange')) }}-500 h-3 rounded-full transition-all duration-500 shadow-sm" 
                                     style="width: {{ min(100, $obra->avance ?? 0) }}%"></div>
                            </div>
                            <div class="text-right text-sm font-medium text-gray-600 mt-1">
                                {{ $obra->avance ?? 0 }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estadísticas y Cronograma -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800">Cronograma y Estadísticas</h3>
                </div>
                <div class="p-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Días Transcurridos</label>
                            <div class="bg-blue-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->dias_transcurridos ?? 0 }} días
                            </div>
                        </div>
                        @if($obra->dias_restantes !== null)
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Días Restantes</label>
                            <div class="bg-{{ $obra->dias_restantes > 0 ? 'green' : 'red' }}-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->dias_restantes }} días
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    @if($obra->duracion_total)
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Duración Total</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->duracion_total }} días
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Progreso de Tiempo</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->porcentaje_tiempo_transcurrido }}%
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Observaciones -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800">Observaciones y Notas</h3>
                </div>
                <div class="p-4">
                    <div class="bg-yellow-50 border border-yellow-200 px-4 py-3 rounded text-sm">
                        {{ $obra->observaciones ?: 'No hay observaciones registradas para esta obra.' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Derecho -->
        <div class="flex flex-col gap-4">
            <!-- Estado y Resumen en fila horizontal -->
            <div class="grid grid-cols-1 gap-3">
                <!-- Estado Actual -->
                <div>
                    <div class="text-sm font-medium text-gray-600 mb-1">Estado Actual</div>
                    <div class="bg-{{ $obra->estatus === 'en_progreso' ? 'green' : ($obra->estatus === 'completada' ? 'blue' : 'orange') }}-500 text-white p-2 rounded text-center">
                        <div class="text-sm font-bold">{{ ucfirst(str_replace('_', ' ', $obra->estatus)) }}</div>
                        @if($obra->esta_atrasada)
                        <div class="text-xs bg-red-600 inline-block px-2 py-0.5 rounded mt-1">Atrasada</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pestañas de Información -->
            <div class="bg-white border border-gray-300 rounded-lg" x-data="{ activeTab: 'recursos' }">
                <div class="bg-gray-50 px-4 py-0 border-b border-gray-300">
                    <div class="flex space-x-0" role="tablist">
                        <button @click="activeTab = 'recursos'" 
                                :class="activeTab === 'recursos' ? 'border-gray-600 text-gray-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                class="px-4 py-3 text-sm font-medium border-b-2 transition-colors duration-200"
                                role="tab" 
                                aria-selected="true"
                                aria-controls="recursos-content">
                            Recursos
                        </button>
                        <button @click="activeTab = 'documentos'" 
                                :class="activeTab === 'documentos' ? 'border-gray-600 text-gray-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                class="px-4 py-3 text-sm font-medium border-b-2 transition-colors duration-200"
                                role="tab" 
                                aria-selected="false"
                                aria-controls="documentos-content">
                            Documentos
                        </button>
                        <button @click="activeTab = 'asignaciones'" 
                                :class="activeTab === 'asignaciones' ? 'border-gray-600 text-gray-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                class="px-4 py-3 text-sm font-medium border-b-2 transition-colors duration-200"
                                role="tab" 
                                aria-selected="false"
                                aria-controls="asignaciones-content">
                            Asignaciones
                        </button>
                    </div>
                </div>

                <!-- Contenido de pestañas -->
                <div class="min-h-[400px]">
                    <!-- Contenido de Recursos -->
                    <div x-show="activeTab === 'recursos'" class="tab-content p-4" role="tabpanel" aria-labelledby="recursos-tab">
                        <div class="space-y-6">
                            <!-- Sección: Encargado de la Obra -->
                            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-center mb-4">
                                    <h5 class="text-base font-semibold text-gray-800 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Encargado de la Obra
                                    </h5>
                                    
                                    <button onclick="openCambiarResponsableModal()" 
                                       class="bg-blue-600 hover:bg-blue-700 text-white py-1 px-2 rounded-md transition-colors duration-200 flex items-center text-xs"
                                       id="btn-cambiar-responsable">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                        @if($obra->encargado)
                                            Cambiar Responsable
                                        @else
                                            Asignar Responsable
                                        @endif
                                    </button>
                                </div>
                                
                                @if($obra->encargado)
                                <div class="space-y-4">
                                    <!-- Nombre completo en componente gris -->
                                    <div>
                                        <label class="block text-sm text-gray-600">Nombre Completo</label>
                                        <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                            {{ $obra->encargado->nombre_completo }}
                                        </div>
                                    </div>
                                    
                                    <!-- Información básica -->
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600">ID Personal</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                {{ $obra->encargado->id }}
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600">Categoría</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                                {{ $obra->encargado->categoria ? $obra->encargado->categoria->nombre_categoria : 'Sin categoría' }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Información de contacto -->
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600">Categoría</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                {{ $obra->encargado->categoria ? $obra->encargado->categoria->nombre_categoria : 'Sin categoría' }}
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600">Teléfono</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                {{ $obra->encargado->telefono ?? 'No registrado' }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Cuenta de usuario si existe -->
                                    @if($obra->encargado->usuario)
                                    <div>
                                        <label class="block text-sm text-gray-600">Cuenta de Usuario</label>
                                        <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm flex justify-between items-center">
                                            <span>{{ $obra->encargado->usuario->email }}</span>
                                            <span class="bg-gray-800 px-2 py-0.5 rounded-full text-xs">
                                                {{ $obra->encargado->usuario->rol ? $obra->encargado->usuario->rol->nombre_rol : 'Sin rol' }}
                                            </span>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <!-- Detalles adicionales -->
                                    <div class="grid grid-cols-3 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600">Fecha de Alta</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                {{ $obra->encargado->created_at ? $obra->encargado->created_at->format('d/m/Y') : 'No disponible' }}
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600">Estatus</label>
                                            <div class="bg-{{ $obra->encargado->estatus === 'activo' ? 'green' : 'red' }}-600 text-white px-3 py-2 rounded text-sm">
                                                {{ ucfirst($obra->encargado->estatus ?? 'No definido') }}
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600">Obras Asignadas</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                {{ $obra->encargado->obras_count ?? 'Desconocido' }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Observaciones si existen -->
                                    @if($obra->encargado->observaciones)
                                    <div>
                                        <label class="block text-sm text-gray-600">Observaciones</label>
                                        <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                            {{ $obra->encargado->observaciones }}
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                
                                @else
                                <div class="bg-red-50 border border-red-200 rounded-lg p-5 text-center">
                                    <svg class="w-12 h-12 text-red-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                    <h5 class="text-lg font-medium text-red-800 mb-2">No hay responsable asignado</h5>
                                    <p class="text-sm text-red-600">Esta obra no tiene un responsable asignado actualmente.</p>
                                </div>
                                @endif
                            </div>

                            <!-- Sección: Vehículos Asignados -->
                            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-center mb-4">
                                    <h5 class="text-base font-semibold text-gray-800 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        Vehículos Asignados
                                    </h5>
                                    
                                    @if(isset($permisos) && $permisos->contains('editar_obras'))
                                    <button onclick="openAsignarVehiculosModal()" 
                                       class="bg-blue-600 hover:bg-blue-700 text-white py-1 px-2 rounded-md transition-colors duration-200 flex items-center text-xs"
                                       id="btn-asignar-vehiculos">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Asignar Vehículos
                                    </button>
                                    @endif
                                </div>
                                
                                @if($obra->vehiculos && $obra->vehiculos->count() > 0)
                                    <div class="space-y-4">
                                        @foreach($obra->vehiculos as $vehiculo)
                                        <div class="space-y-3">
                                            <!-- Información del vehículo en componentes grises -->
                                            <div>
                                                <label class="block text-sm text-gray-600">Vehículo</label>
                                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                                    {{ $vehiculo->marca }} {{ $vehiculo->modelo }}
                                                </div>
                                            </div>
                                            
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-sm text-gray-600">Placas</label>
                                                    <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                        {{ $vehiculo->placas }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-sm text-gray-600">Año</label>
                                                    <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                        {{ $vehiculo->anio ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-sm text-gray-600">Serie</label>
                                                    <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                        {{ $vehiculo->n_serie ?? 'N/A' }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-sm text-gray-600">Kilometraje</label>
                                                    <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                        {{ $vehiculo->kilometraje_actual ? number_format($vehiculo->kilometraje_actual) : 'N/A' }} km
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            @if(isset($vehiculo->pivot) && $vehiculo->pivot->fecha_asignacion)
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-sm text-gray-600">Fecha de Asignación</label>
                                                    <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                        {{ \Carbon\Carbon::parse($vehiculo->pivot->fecha_asignacion)->format('d/m/Y') }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-sm text-gray-600">Operador</label>
                                                    <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                        {{ $vehiculo->operadorActual ? $vehiculo->operadorActual->nombre_completo : 'Sin operador asignado' }}
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            
                                            <!-- Botones de acción -->
                                            <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                                                <a href="{{ route('vehiculos.show', $vehiculo->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white py-1 px-3 rounded-md transition-colors duration-200 flex items-center text-xs">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Ver Detalles
                                                </a>
                                                
                                                @if(isset($permisos) && $permisos->contains('eliminar_asignacion_vehiculo'))
                                                <form action="{{ route('obras.desasignar_vehiculo', ['obra' => $obra->id, 'vehiculo' => $vehiculo->id]) }}" method="POST" onsubmit="return confirm('¿Estás seguro de desasignar este vehículo de la obra?')" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-100 text-red-600 hover:bg-red-200 py-1 px-3 rounded text-xs flex items-center transition-colors duration-200">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        Desasignar
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        @if(!$loop->last)
                                        <div class="border-b border-gray-300 my-4"></div>
                                        @endif
                                        @endforeach
                                    </div>
                                @elseif($obra->vehiculo)
                                    <!-- Mostrar el vehículo único asociado directamente a la obra -->
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-sm text-gray-600">Vehículo</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                                {{ $obra->vehiculo->marca }} {{ $obra->vehiculo->modelo }}
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-sm text-gray-600">Placas</label>
                                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                    {{ $obra->vehiculo->placas }}
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-600">Año</label>
                                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                    {{ $obra->vehiculo->anio ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-sm text-gray-600">Serie</label>
                                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                    {{ $obra->vehiculo->n_serie ?? 'N/A' }}
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-600">Kilometraje</label>
                                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                    {{ $obra->vehiculo->kilometraje_actual ? number_format($obra->vehiculo->kilometraje_actual) : 'N/A' }} km
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="pt-3 border-t border-gray-200">
                                            <a href="{{ route('vehiculos.show', $obra->vehiculo->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white py-1 px-3 rounded-md transition-colors duration-200 flex items-center text-xs w-fit">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                Ver Vehículo
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <!-- Mostrar mensaje cuando no hay vehículos asignados -->
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-8 text-center">
                                        <svg class="w-10 h-10 text-red-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                        </svg>
                                        <h5 class="text-lg font-medium text-red-800 mb-2">Sin vehículo asignado</h5>
                                        <p class="text-sm text-red-600 mb-4">Esta obra no tiene vehículos asignados actualmente.</p>
                                        
                                        @if(isset($permisos) && $permisos->contains('editar_obras'))
                                        <a href="{{ route('obras.edit', ['obra' => $obra->id]) }}" class="inline-block bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-md text-sm transition-colors duration-200">
                                            Asignar Vehículo
                                        </a>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            @if($obra->vehiculo_id && $obra->kilometraje_inicial)
                            <!-- Información de Kilometraje -->
                            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-center mb-4">
                                    <h5 class="text-base font-semibold text-gray-800 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                        </svg>
                                        Registro de Kilometraje
                                    </h5>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm text-gray-600">Kilometraje Inicial</label>
                                        <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                            {{ number_format($obra->kilometraje_inicial) }} km
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-600">Kilometraje Final</label>
                                        <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                            {{ $obra->kilometraje_final ? number_format($obra->kilometraje_final) . ' km' : 'En progreso' }}
                                        </div>
                                    </div>
                                </div>
                                
                                @if($obra->kilometraje_final)
                                <div class="mt-4">
                                    <label class="block text-sm text-gray-600">Kilómetros Recorridos</label>
                                    <div class="bg-blue-600 text-white px-3 py-2 rounded text-sm font-bold">
                                        {{ number_format($obra->kilometraje_final - $obra->kilometraje_inicial) }} km
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endif

                            @if($obra->combustible_inicial || $obra->combustible_final || $obra->combustible_suministrado)
                            <!-- Información de Combustible -->
                            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-center mb-4">
                                    <h5 class="text-base font-semibold text-gray-800 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Registro de Combustible
                                    </h5>
                                </div>
                                
                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm text-gray-600">Inicial</label>
                                        <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                            {{ $obra->combustible_inicial ? number_format($obra->combustible_inicial, 2) . ' L' : 'N/A' }}
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-600">Suministrado</label>
                                        <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                            {{ $obra->combustible_suministrado ? number_format($obra->combustible_suministrado, 2) . ' L' : 'N/A' }}
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-600">Final</label>
                                        <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                            {{ $obra->combustible_final ? number_format($obra->combustible_final, 2) . ' L' : 'En progreso' }}
                                        </div>
                                    </div>
                                </div>
                                
                                @if($obra->costo_combustible)
                                <div class="mt-4">
                                    <label class="block text-sm text-gray-600">Costo Total de Combustible</label>
                                    <div class="bg-green-600 text-white px-3 py-2 rounded text-sm font-bold">
                                        ${{ number_format($obra->costo_combustible, 2) }}
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Contenido de Documentos -->
                    <div x-show="activeTab === 'documentos'" class="tab-content p-4" role="tabpanel" aria-labelledby="documentos-tab">
                        <div class="space-y-6">
                            <!-- Documentos del Proyecto -->
                            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-center mb-4">
                                    <h5 class="text-base font-semibold text-gray-800 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2H5a2 2 0 00-2 2v2m0 0h14" />
                                        </svg>
                                        Documentos del Proyecto
                                    </h5>
                                    <div class="text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                        {{ $obra->getPorcentajeDocumentosCompletados() }}% completo
                                    </div>
                                </div>
                                
                                <ul class="divide-y divide-gray-200 mb-6">
                                    <!-- Contrato -->
                                    <li class="py-3 flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-3 text-{{ $obra->tieneContrato() ? 'green' : 'red' }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <div>
                                                <span class="text-sm font-medium text-gray-800">Contrato</span>
                                                @if($obra->fecha_subida_contrato)
                                                    <p class="text-xs text-gray-500">Subido: {{ \Carbon\Carbon::parse($obra->fecha_subida_contrato)->format('d/m/Y') }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            @if($obra->tieneContrato())
                                                <a href="{{ $obra->getUrlContrato() }}" target="_blank" 
                                                class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Ver
                                                </a>
                                                <a href="{{ $obra->getUrlContrato() }}" download class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                    Descargar
                                                </a>
                                            @else
                                                <span class="text-xs text-red-600 font-medium">Faltante</span>
                                            @endif
                                        </div>
                                    </li>
                                    
                                    <!-- Fianza -->
                                    <li class="py-3 flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-3 text-{{ $obra->tieneFianza() ? 'green' : 'red' }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                            </svg>
                                            <div>
                                                <span class="text-sm font-medium text-gray-800">Fianza</span>
                                                @if($obra->fecha_subida_fianza)
                                                    <p class="text-xs text-gray-500">Subido: {{ \Carbon\Carbon::parse($obra->fecha_subida_fianza)->format('d/m/Y') }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            @if($obra->tieneFianza())
                                                <a href="{{ $obra->getUrlFianza() }}" target="_blank" 
                                                class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Ver
                                                </a>
                                                <a href="{{ $obra->getUrlFianza() }}" download class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                    Descargar
                                                </a>
                                            @else
                                                <span class="text-xs text-red-600 font-medium">Faltante</span>
                                            @endif
                                        </div>
                                    </li>
                                    
                                    <!-- Acta Entrega-Recepción -->
                                    <li class="py-3 flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-3 text-{{ $obra->tieneActaEntregaRecepcion() ? 'green' : 'red' }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            <div>
                                                <span class="text-sm font-medium text-gray-800">Acta Entrega-Recepción</span>
                                                @if($obra->fecha_subida_acta)
                                                    <p class="text-xs text-gray-500">Subido: {{ \Carbon\Carbon::parse($obra->fecha_subida_acta)->format('d/m/Y') }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            @if($obra->tieneActaEntregaRecepcion())
                                                <a href="{{ $obra->getUrlActaEntregaRecepcion() }}" target="_blank" 
                                                class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Ver
                                                </a>
                                                <a href="{{ $obra->getUrlActaEntregaRecepcion() }}" download class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                    Descargar
                                                </a>
                                            @else
                                                <span class="text-xs text-red-600 font-medium">Faltante</span>
                                            @endif
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Contenido de Asignaciones -->
                    <div x-show="activeTab === 'asignaciones'" class="tab-content p-4" role="tabpanel" aria-labelledby="asignaciones-tab">
                        <div class="space-y-6">
                            <!-- Asignaciones Activas -->
                            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-center mb-4">
                                    <h5 class="text-base font-semibold text-gray-800 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Asignaciones Activas
                                    </h5>
                                    @if(isset($permisos) && $permisos->contains('crear_asignaciones'))
                                    <a href="{{ route('obras.edit', ['obra' => $obra->id]) }}" class="bg-blue-600 hover:bg-blue-700 text-white py-1 px-2 rounded-md transition-colors duration-200 flex items-center text-xs">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Nueva Asignación
                                    </a>
                                    @endif
                                </div>
                                
                                <!-- Tabla de Asignaciones -->
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehículo</th>
                                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operador</th>
                                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Asignación</th>
                                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kilometraje</th>
                                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @if($obra->vehiculo_id && $obra->operador_id)
                                            <tr>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $obra->vehiculo ? $obra->vehiculo->marca . ' ' . $obra->vehiculo->modelo : 'Sin vehículo' }}
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $obra->operador ? $obra->operador->nombre_completo : 'Sin operador' }}
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $obra->fecha_asignacion ? \Carbon\Carbon::parse($obra->fecha_asignacion)->format('d/m/Y') : 'N/A' }}
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $obra->kilometraje_inicial ? number_format($obra->kilometraje_inicial) : 'N/A' }} km
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                    <!-- Acciones en línea -->
                                                </td>
                                            </tr>
                                            @elseif($obra->asignacionesActivas && $obra->asignacionesActivas->count() > 0)
                                                @foreach($obra->asignacionesActivas as $asignacion)
                                                <tr>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        {{ $asignacion->vehiculo ? $asignacion->vehiculo->marca . ' ' . $asignacion->vehiculo->modelo : 'Sin vehículo' }}
                                                    </td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $asignacion->operador ? $asignacion->operador->nombre_completo : 'Sin operador' }}
                                                    </td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                        {{ \Carbon\Carbon::parse($asignacion->fecha_asignacion)->format('d/m/Y') }}
                                                    </td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                        {{ number_format($asignacion->kilometraje_inicial) }} km
                                                    </td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                        <a href="{{ route('obras.show', $obra->id) }}" class="text-blue-600 hover:text-blue-900 mr-2">
                                                            <svg class="h-4 w-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                        </a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5" class="px-3 py-4 text-center text-sm text-gray-500">
                                                        No hay asignaciones activas para esta obra
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Asignaciones Históricas -->
                            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-center mb-4">
                                    <h5 class="text-base font-semibold text-gray-800 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Historial de Asignaciones
                                    </h5>
                                </div>
                                
                                <!-- Tabla de Asignaciones Liberadas -->
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehículo</th>
                                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operador</th>
                                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Asignación</th>
                                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asignado Por</th>
                                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @if($obra->historialOperadores && $obra->historialOperadores->count() > 0)
                                                @foreach($obra->historialOperadores->take(10) as $historial)
                                                <tr>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        {{ $historial->vehiculo ? $historial->vehiculo->marca . ' ' . $historial->vehiculo->modelo . ' (' . $historial->vehiculo->placas . ')' : 'Sin vehículo' }}
                                                    </td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $historial->operadorNuevo ? $historial->operadorNuevo->nombre_completo : 'Sin operador' }}
                                                    </td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $historial->fecha_asignacion->format('d/m/Y H:i') }}
                                                    </td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $historial->usuarioAsigno ? $historial->usuarioAsigno->name : 'Sistema' }}
                                                    </td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            {{ $historial->tipo_movimiento_descripcion }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @elseif($obra->fecha_liberacion)
                                            <tr>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $obra->vehiculo ? $obra->vehiculo->marca . ' ' . $obra->vehiculo->modelo : 'Sin vehículo' }}
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $obra->operador ? $obra->operador->nombre_completo : 'Sin operador' }}
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $obra->fecha_asignacion ? \Carbon\Carbon::parse($obra->fecha_asignacion)->format('d/m/Y') : 'N/A' }} -
                                                    {{ \Carbon\Carbon::parse($obra->fecha_liberacion)->format('d/m/Y') }}
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                    {{ number_format($obra->kilometraje_final - $obra->kilometraje_inicial) }} km
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                    <!-- Acciones en línea -->
                                                </td>
                                            </tr>
                                            @elseif($obra->asignacionesLiberadas && $obra->asignacionesLiberadas->count() > 0)
                                                @foreach($obra->asignacionesLiberadas->take(5) as $asignacion)
                                                <tr>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        {{ $asignacion->vehiculo ? $asignacion->vehiculo->marca . ' ' . $asignacion->vehiculo->modelo : 'Sin vehículo' }}
                                                    </td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $asignacion->operador ? $asignacion->operador->nombre_completo : 'Sin operador' }}
                                                    </td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                        {{ \Carbon\Carbon::parse($asignacion->fecha_asignacion)->format('d/m/Y') }} -
                                                        {{ \Carbon\Carbon::parse($asignacion->fecha_liberacion)->format('d/m/Y') }}
                                                    </td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                        {{ number_format($asignacion->kilometraje_recorrido) }} km
                                                    </td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                        <a href="{{ route('obras.show', $obra->id) }}" class="text-blue-600 hover:text-blue-900 mr-2">
                                                            <svg class="h-4 w-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                        </a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5" class="px-3 py-4 text-center text-sm text-gray-500">
                                                        No hay historial de asignaciones para esta obra
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Botones de Acción Flotantes -->
<div class="fixed bottom-6 right-6 flex space-x-3 z-50">
    @hasPermission('actualizar_obras')
    <!-- Botón Editar -->
    <a href="{{ route('obras.edit', $obra->id) }}" 
       class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg text-sm transition-all duration-200 flex items-center space-x-2 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
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
                class="bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded-lg text-sm transition-all duration-200 flex items-center space-x-2 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
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

<!-- Modal para Cambiar Responsable de la Obra -->
<div id="cambiar-responsable-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modal-responsable-title" class="text-lg font-semibold text-gray-900">
                @if($obra->encargado)
                    Cambiar Responsable de la Obra
                @else
                    Asignar Responsable a la Obra
                @endif
            </h3>
            <button onclick="closeCambiarResponsableModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <div class="mb-4 text-sm text-gray-600">
            <p><strong>Obra:</strong> {{ $obra->nombre_obra }}</p>
            @if($obra->encargado)
                <p><strong>Responsable Actual:</strong> {{ $obra->encargado->nombre_completo }}</p>
            @else
                <p><strong>Estado:</strong> Sin responsable asignado</p>
            @endif
        </div>

        <form id="cambiar-responsable-form" method="POST" action="{{ route('obras.cambiar-encargado', $obra) }}">
            @csrf
            @method('PATCH')
            
            <!-- Selección de Nuevo Responsable -->
            <div class="mb-4">
                <label for="responsable_id" class="block text-sm font-medium text-gray-700 mb-1">
                    @if($obra->encargado)
                        Nuevo Responsable
                    @else
                        Responsable a Asignar
                    @endif
                </label>
                <select id="responsable_id" name="responsable_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Seleccionar responsable...</option>
                    @foreach($responsables ?? [] as $responsable)
                        <option value="{{ $responsable->id }}" 
                                {{ ($obra->encargado && $obra->encargado->id == $responsable->id) ? 'selected' : '' }}>
                            {{ $responsable->nombre_completo }} 
                            @if($responsable->categoria)
                                - {{ $responsable->categoria->nombre_categoria }}
                            @endif
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Selecciona el personal con categoría "Responsable de obra" para supervisión del proyecto</p>
            </div>
            
            <!-- Observaciones -->
            <div class="mb-4">
                <label for="observaciones_responsable" class="block text-sm font-medium text-gray-700 mb-1">
                    Observaciones (opcional)
                </label>
                <textarea id="observaciones_responsable" 
                         name="observaciones" 
                         rows="3" 
                         class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                         placeholder="Motivo del cambio u observaciones adicionales..."></textarea>
            </div>
            
            <!-- Botones -->
            <div class="flex justify-end space-x-3">
                <button type="button" 
                        onclick="closeCambiarResponsableModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancelar
                </button>
                <button type="submit" 
                        id="submit-responsable-btn"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    @if($obra->encargado)
                        Cambiar Responsable
                    @else
                        Asignar Responsable
                    @endif
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para Asignar Vehículos a la Obra -->
<div id="asignar-vehiculos-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modal-vehiculos-title" class="text-lg font-semibold text-gray-900">
                Asignar Vehículos a la Obra
            </h3>
            <button onclick="closeAsignarVehiculosModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <div class="mb-4 text-sm text-gray-600">
            <p><strong>Obra:</strong> {{ $obra->nombre_obra }}</p>
            <p><strong>Vehículos Actuales:</strong> {{ $obra->vehiculos ? $obra->vehiculos->count() : 0 }}</p>
        </div>

        <form id="asignar-vehiculos-form" method="POST" action="{{ route('obras.asignar-vehiculos', $obra) }}">
            @csrf
            @method('PATCH')
            
            <!-- Lista de Vehículos Disponibles -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Seleccionar Vehículos Disponibles
                </label>
                <div class="max-h-64 overflow-y-auto border border-gray-300 rounded-md p-3 bg-gray-50">
                    <div class="space-y-2" id="vehiculos-disponibles">
                        @php
                            // Separar vehículos disponibles y no disponibles
                            $vehiculosOrdenados = collect($vehiculosDisponibles ?? [])->sortBy(function($vehiculo) use ($obra) {
                                $obraActual = $vehiculo->obraActual()->first();
                                $yaAsignadoAOtraObra = $obraActual && $obraActual->id !== $obra->id;
                                // Los disponibles (false) van primero, los no disponibles (true) van al final
                                return $yaAsignadoAOtraObra ? 1 : 0;
                            });
                        @endphp
                        @php
                            // Separar vehículos disponibles y no disponibles
                            $vehiculosOrdenados = collect($vehiculosDisponibles ?? [])->sortBy(function($vehiculo) use ($obra) {
                                $obraActual = $vehiculo->obraActual()->first();
                                $yaAsignadoAOtraObra = $obraActual && $obraActual->id !== $obra->id;
                                // Los disponibles (false) van primero, los no disponibles (true) van al final
                                return $yaAsignadoAOtraObra ? 1 : 0;
                            });
                            
                            $vehiculosDisponiblesReales = $vehiculosOrdenados->filter(function($vehiculo) use ($obra) {
                                $obraActual = $vehiculo->obraActual()->first();
                                return !($obraActual && $obraActual->id !== $obra->id);
                            });
                            
                            $vehiculosNoDisponibles = $vehiculosOrdenados->filter(function($vehiculo) use ($obra) {
                                $obraActual = $vehiculo->obraActual()->first();
                                return $obraActual && $obraActual->id !== $obra->id;
                            });
                        @endphp
                        
                        @if($vehiculosDisponiblesReales->count() > 0)
                            <!-- Encabezado para vehículos disponibles -->
                            <div class="bg-green-50 border border-green-200 rounded p-2 mb-2">
                                <h6 class="text-sm font-semibold text-green-800 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Vehículos Disponibles ({{ $vehiculosDisponiblesReales->count() }})
                                </h6>
                            </div>
                            
                            @foreach($vehiculosDisponiblesReales as $vehiculo)
                                @php
                                    $obraActual = $vehiculo->obraActual()->first();
                                    $yaAsignadoAOtraObra = $obraActual && $obraActual->id !== $obra->id;
                                    $yaAsignadoAEstaObra = $obra->vehiculos && $obra->vehiculos->contains($vehiculo->id);
                                @endphp
                                <label class="flex items-center space-x-3 p-2 rounded hover:bg-white cursor-pointer">
                                    <input type="checkbox" 
                                           name="vehiculos[]" 
                                           value="{{ $vehiculo->id }}"
                                           class="form-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                           {{ $yaAsignadoAEstaObra ? 'checked' : '' }}>
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900">
                                            {{ $vehiculo->marca }} {{ $vehiculo->modelo }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Placas: {{ $vehiculo->placas }} | 
                                            Año: {{ $vehiculo->anio }} | 
                                            Kilometraje: {{ number_format($vehiculo->kilometraje_actual ?? 0) }} km
                                        </div>
                                        @if($yaAsignadoAEstaObra)
                                            <div class="text-xs text-green-600 font-medium">
                                                Asignado a esta obra
                                            </div>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        @endif
                        
                        @if($vehiculosNoDisponibles->count() > 0)
                            <!-- Separador -->
                            @if($vehiculosDisponiblesReales->count() > 0)
                                <div class="border-t border-gray-300 my-3"></div>
                            @endif
                            
                            <!-- Encabezado para vehículos no disponibles -->
                            <div class="bg-red-50 border border-red-200 rounded p-2 mb-2">
                                <h6 class="text-sm font-semibold text-red-800 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                    Vehículos No Disponibles ({{ $vehiculosNoDisponibles->count() }})
                                </h6>
                                <p class="text-xs text-red-600 mt-1">Estos vehículos están asignados a otras obras</p>
                            </div>
                            
                            @foreach($vehiculosNoDisponibles as $vehiculo)
                                @php
                                    $obraActual = $vehiculo->obraActual()->first();
                                    $yaAsignadoAOtraObra = $obraActual && $obraActual->id !== $obra->id;
                                    $yaAsignadoAEstaObra = $obra->vehiculos && $obra->vehiculos->contains($vehiculo->id);
                                @endphp
                                <label class="flex items-center space-x-3 p-2 rounded bg-gray-100 cursor-not-allowed opacity-60">
                                    <input type="checkbox" 
                                           name="vehiculos[]" 
                                           value="{{ $vehiculo->id }}"
                                           class="form-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                           {{ $yaAsignadoAEstaObra ? 'checked' : '' }}
                                           disabled>
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-500">
                                            {{ $vehiculo->marca }} {{ $vehiculo->modelo }}
                                            <span class="text-xs text-red-600 ml-2">(No disponible)</span>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Placas: {{ $vehiculo->placas }} | 
                                            Año: {{ $vehiculo->anio }} | 
                                            Kilometraje: {{ number_format($vehiculo->kilometraje_actual ?? 0) }} km
                                        </div>
                                        @if($obraActual)
                                            <div class="text-xs text-red-600 font-medium">
                                                Asignado a: {{ $obraActual->nombre_obra }}
                                            </div>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        @endif
                        
                        @if($vehiculosDisponiblesReales->count() === 0 && $vehiculosNoDisponibles->count() === 0)
                            <p class="text-sm text-gray-500 text-center py-4">No hay vehículos disponibles</p>
                        @endif
                    </div>
                </div>
                <p class="mt-1 text-xs text-gray-500">Selecciona uno o más vehículos para asignar a esta obra. Los vehículos ya asignados aparecen marcados.</p>
            </div>
            
            <!-- Observaciones -->
            <div class="mb-4">
                <label for="observaciones_vehiculos" class="block text-sm font-medium text-gray-700 mb-1">
                    Observaciones (opcional)
                </label>
                <textarea id="observaciones_vehiculos" 
                         name="observaciones" 
                         rows="3" 
                         class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                         placeholder="Motivo de la asignación u observaciones adicionales..."></textarea>
            </div>
            
            <!-- Botones -->
            <div class="flex justify-end space-x-3">
                <button type="button" 
                        onclick="closeAsignarVehiculosModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancelar
                </button>
                <button type="submit" 
                        id="submit-vehiculos-btn"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Asignar Vehículos
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function handleImageLoad() {
// ... (código existente)
    }

    function handleImageError() {
        // ... (código existente)
    }
    
    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    // Funciones para el modal de cambiar responsable
    function openCambiarResponsableModal() {
        const modal = document.getElementById('cambiar-responsable-modal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeCambiarResponsableModal() {
        const modal = document.getElementById('cambiar-responsable-modal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            
            const form = document.getElementById('cambiar-responsable-form');
            if (form) {
                form.reset();
            }
        }
    }

    // Funciones para el modal de asignar vehículos
    function openAsignarVehiculosModal() {
        const modal = document.getElementById('asignar-vehiculos-modal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeAsignarVehiculosModal() {
        const modal = document.getElementById('asignar-vehiculos-modal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            
            const form = document.getElementById('asignar-vehiculos-form');
            if (form) {
                form.reset();
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('cambiar-responsable-modal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeCambiarResponsableModal();
                }
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCambiarResponsableModal();
                closeAsignarVehiculosModal();
            }
        });

        const form = document.getElementById('cambiar-responsable-form');
        if(form) {
            form.addEventListener('submit', function(e){
                const submitBtn = document.getElementById('submit-responsable-btn');
                if(submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Guardando...';
                }
            });
        }

        // Event listeners para modal de vehículos
        const modalVehiculos = document.getElementById('asignar-vehiculos-modal');
        if (modalVehiculos) {
            modalVehiculos.addEventListener('click', function(e) {
                if (e.target === modalVehiculos) {
                    closeAsignarVehiculosModal();
                }
            });
        }

        const formVehiculos = document.getElementById('asignar-vehiculos-form');
        if(formVehiculos) {
            formVehiculos.addEventListener('submit', function(e){
                const submitBtn = document.getElementById('submit-vehiculos-btn');
                if(submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Asignando...';
                }
            });
        }
    });
</script>
@endpush
