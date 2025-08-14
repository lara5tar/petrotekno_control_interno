@extends('layouts.app')

@section('title', 'Detalle del Kilometraje - ' . $vehiculo->marca . ' ' . $vehiculo->modelo)

@section('content')
<!-- Breadcrumb -->
<x-breadcrumb :items="[
    ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
    ['label' => 'Vehículos', 'url' => route('vehiculos.index')],
    ['label' => $vehiculo->marca . ' ' . $vehiculo->modelo, 'url' => route('vehiculos.show', $vehiculo)],
    ['label' => 'Detalle del Kilometraje']
]" />

<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white border border-gray-300 rounded-lg mb-6">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-300">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-semibold text-gray-800">Detalle del Kilometraje</h1>
                        <p class="text-sm text-gray-600 mt-1">{{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->anio }})</p>
                        <p class="text-xs text-gray-500">Placas: {{ $vehiculo->placas }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-medium text-gray-600">Kilometraje Registrado</div>
                        <div class="text-3xl font-bold text-blue-600">{{ number_format($kilometraje->kilometraje) }} km</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido Principal -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Información del Kilometraje -->
            <div class="lg:col-span-2">
                <div class="bg-white border border-gray-300 rounded-lg">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-300">
                        <h3 class="font-semibold text-gray-800">Información del Registro</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Kilometraje -->
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">Kilometraje</label>
                                <div class="bg-gray-600 text-white px-4 py-3 rounded text-lg font-semibold">
                                    {{ number_format($kilometraje->kilometraje) }} km
                                </div>
                            </div>

                            <!-- Fecha de Captura -->
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">Fecha de Captura</label>
                                <div class="bg-gray-600 text-white px-4 py-3 rounded text-lg font-semibold">
                                    {{ $kilometraje->fecha_captura ? $kilometraje->fecha_captura->format('d/m/Y') : 'Sin fecha' }}
                                </div>
                            </div>

                            <!-- Usuario que Registró -->
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">Registrado por</label>
                                <div class="bg-gray-600 text-white px-4 py-3 rounded">
                                    {{ $kilometraje->usuarioCaptura?->name ?? 'Usuario no disponible' }}
                                </div>
                            </div>

                            <!-- Fecha de Registro -->
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">Fecha de Registro</label>
                                <div class="bg-gray-600 text-white px-4 py-3 rounded">
                                    {{ $kilometraje->created_at ? $kilometraje->created_at->format('d/m/Y H:i') : 'Sin fecha' }}
                                </div>
                            </div>

                            <!-- Observaciones -->
                            @if($kilometraje->observaciones)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-600 mb-2">Observaciones</label>
                                <div class="bg-gray-100 border border-gray-300 px-4 py-3 rounded">
                                    {{ $kilometraje->observaciones }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel Lateral -->
            <div class="space-y-6">
                <!-- Imagen del Odómetro -->
                @if($kilometraje->imagen)
                <div class="bg-white border border-gray-300 rounded-lg">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                        <h3 class="font-semibold text-gray-800">Imagen del Odómetro</h3>
                    </div>
                    <div class="p-4">
                        <div class="relative">
                            <img src="{{ Storage::url($kilometraje->imagen) }}" 
                                 alt="Odómetro - {{ number_format($kilometraje->kilometraje) }} km" 
                                 class="w-full h-auto rounded-lg border border-gray-200"
                                 id="odometer-image"
                                 onload="handleImageLoad()"
                                 onerror="handleImageError()">
                            
                            <!-- Overlay con información -->
                            <div class="absolute bottom-2 left-2 bg-black bg-opacity-75 text-white px-2 py-1 rounded text-xs">
                                {{ number_format($kilometraje->kilometraje) }} km
                            </div>
                        </div>
                        
                        <div class="mt-3 flex justify-center">
                            <a href="{{ Storage::url($kilometraje->imagen) }}" 
                               download="kilometraje_{{ $vehiculo->placas }}_{{ $kilometraje->kilometraje }}.jpg"
                               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm flex items-center transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Descargar Imagen
                            </a>
                        </div>
                    </div>
                </div>
                @else
                <!-- Sin imagen -->
                <div class="bg-white border border-gray-300 rounded-lg">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                        <h3 class="font-semibold text-gray-800">Imagen del Odómetro</h3>
                    </div>
                    <div class="p-6 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-sm text-gray-500">No se registró imagen</p>
                        <p class="text-xs text-gray-400">Este registro no incluye fotografía del odómetro</p>
                    </div>
                </div>
                @endif

                <!-- Acciones -->
                <div class="bg-white border border-gray-300 rounded-lg">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                        <h3 class="font-semibold text-gray-800">Acciones</h3>
                    </div>
                    <div class="p-4 space-y-3">
                        @hasPermission('editar_kilometrajes')
                        <a href="{{ route('vehiculos.kilometrajes.edit', [$vehiculo->id, $kilometraje->id]) }}" 
                           class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded text-sm flex items-center justify-center transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Editar Registro
                        </a>
                        @endhasPermission

                        @hasPermission('eliminar_kilometrajes')
                        <button onclick="confirmDelete()" 
                                class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded text-sm flex items-center justify-center transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Eliminar Registro
                        </button>
                        @endhasPermission

                        <a href="{{ route('vehiculos.show', $vehiculo) }}" 
                           class="w-full bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded text-sm flex items-center justify-center transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Volver al Vehículo
                        </a>

                        <a href="{{ route('vehiculos.kilometrajes.create', $vehiculo) }}" 
                           class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded text-sm flex items-center justify-center transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Registrar Nuevo Kilometraje
                        </a>
                    </div>
                </div>

                <!-- Información del Vehículo -->
                <div class="bg-white border border-gray-300 rounded-lg">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                        <h3 class="font-semibold text-gray-800">Información del Vehículo</h3>
                    </div>
                    <div class="p-4 space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Marca:</span>
                            <span class="text-sm font-medium">{{ $vehiculo->marca }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Modelo:</span>
                            <span class="text-sm font-medium">{{ $vehiculo->modelo }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Año:</span>
                            <span class="text-sm font-medium">{{ $vehiculo->anio }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Placas:</span>
                            <span class="text-sm font-medium">{{ $vehiculo->placas }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Kilometraje Actual:</span>
                            <span class="text-sm font-medium text-blue-600">{{ number_format($vehiculo->kilometraje_actual) }} km</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación de eliminación -->
<div id="delete-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="bg-black bg-opacity-50 absolute inset-0"></div>
    <div class="bg-white rounded-lg shadow-lg overflow-hidden w-full max-w-md z-10">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Confirmar Eliminación</h3>
        </div>
        <div class="p-6">
            <div class="flex items-center mb-4">
                <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
                <div>
                    <p class="text-sm text-gray-700">¿Está seguro de que desea eliminar este registro de kilometraje?</p>
                    <p class="text-xs text-gray-500 mt-1">Esta acción no se puede deshacer.</p>
                </div>
            </div>
            
            <div class="bg-gray-50 border border-gray-200 rounded p-3 mb-4">
                <p class="text-sm"><strong>Kilometraje:</strong> {{ number_format($kilometraje->kilometraje) }} km</p>
                <p class="text-sm"><strong>Fecha:</strong> {{ $kilometraje->fecha_captura ? $kilometraje->fecha_captura->format('d/m/Y') : 'Sin fecha' }}</p>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeDeleteModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200">
                    Cancelar
                </button>
                <form action="{{ route('vehiculos.kilometrajes.destroy', [$vehiculo->id, $kilometraje->id]) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200">
                        Eliminar Definitivamente
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function confirmDelete() {
        document.getElementById('delete-modal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('delete-modal').classList.add('hidden');
    }

    function handleImageLoad() {
        console.log('Imagen del odómetro cargada correctamente');
    }

    function handleImageError() {
        const image = document.getElementById('odometer-image');
        if (image) {
            image.style.display = 'none';
            // Mostrar mensaje de error
            const errorDiv = document.createElement('div');
            errorDiv.className = 'bg-red-50 border border-red-200 rounded p-4 text-center';
            errorDiv.innerHTML = `
                <svg class="w-8 h-8 text-red-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
                <p class="text-sm text-red-600">Error al cargar la imagen</p>
                <p class="text-xs text-red-500">No se pudo encontrar la imagen del odómetro</p>
            `;
            image.parentNode.insertBefore(errorDiv, image);
        }
    }

    // Cerrar modal al hacer clic fuera de él
    document.getElementById('delete-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
</script>
@endsection