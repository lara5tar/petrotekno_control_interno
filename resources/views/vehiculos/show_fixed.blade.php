@extends('layouts.app')

@section('title', 'Detalles del Vehículo')

@section('content')
<div class="h-[calc(100vh-120px)] flex flex-col gap-4">
    <!-- Header -->
    <div class="bg-white border border-gray-300 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-lg font-bold text-gray-900">
                    {{ $vehiculo->marca ?? 'Nissan' }} {{ $vehiculo->modelo ?? 'NP300' }}
                </h1>
                <p class="text-sm text-gray-600">Placas: <span class="font-medium">{{ $vehiculo->placas ?? 'NL-ABC-1234' }}</span></p>
            </div>
            
            <div class="flex space-x-2">
                <button onclick="imprimirDetalles()" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded text-sm transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-1a2 2 0 00-2-2H9a2 2 0 00-2 2v1a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Imprimir
                </button>
                
                <a href="{{ route('vehiculos.edit', $vehiculo->id ?? 1) }}" 
                   class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded text-sm transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Editar
                </a>
                
                <a href="{{ route('vehiculos.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded text-sm transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </div>

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
                            <label class="block text-sm font-medium text-gray-600">Marca</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $vehiculo->marca ?? 'Nissan' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Modelo</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $vehiculo->modelo ?? 'NP300' }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Año</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $vehiculo->año ?? '2023' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Color</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $vehiculo->color ?? 'Blanco' }}
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Placas</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $vehiculo->placas ?? 'NL-ABC-1234' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Serie</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $vehiculo->serie ?? 'NS123456789' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fotografía del Vehículo -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800">Fotografía del Vehículo</h3>
                </div>
                <div class="p-4">
                    <div class="relative">
                        <div class="h-48 bg-gray-100 rounded overflow-hidden mb-3">
                            <img src="{{ $vehiculo->imagen ?? '/images/placeholder-vehicle.jpg' }}" 
                                 alt="Vehículo {{ $vehiculo->marca ?? 'Nissan' }} {{ $vehiculo->modelo ?? 'NP300' }}" 
                                 class="w-full h-full object-cover"
                                 onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDMwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIzMDAiIGhlaWdodD0iMjAwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0xNTAgODBWMTIwTTEzMCAxMDBIMTcwIiBzdHJva2U9IiM5QjlDQTQiIHN0cm9rZS13aWR0aD0iMiIvPgo8dGV4dCB4PSIxNTAiIHk9IjE0MCIgZm9udC1mYW1pbHk9IkFyaWFsLCBzYW5zLXNlcmlmIiBmb250LXNpemU9IjEyIiBmaWxsPSIjOUI5Q0E0IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5TaW4gZm90bzwvdGV4dD4KPC9zdmc+Cg=='">
                            <div class="absolute bottom-2 left-2 bg-black bg-opacity-75 text-white px-2 py-1 rounded text-sm">
                                15/07/2025
                            </div>
                        </div>
                        <div class="flex justify-center space-x-2">
                            <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded text-sm">Actualizar</button>
                            <button class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded text-sm">Descargar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Derecho -->
        <div class="flex flex-col gap-4">
            <!-- Kilometraje y Estado en fila horizontal -->
            <div class="grid grid-cols-2 gap-4">
                <!-- Último Kilometraje -->
                <div class="bg-white border border-gray-300 rounded-lg">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                        <h3 class="font-semibold text-gray-800">Último Kilometraje</h3>
                    </div>
                    <div class="p-4">
                        <div class="bg-gray-800 text-white p-4 rounded text-center">
                            <div class="text-2xl font-bold">{{ $vehiculo->kilometraje_actual ?? '125,165' }}</div>
                            <div class="text-sm text-gray-300">Kilómetros</div>
                        </div>
                    </div>
                </div>

                <!-- Estatus Actual -->
                <div class="bg-white border border-gray-300 rounded-lg">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                        <h3 class="font-semibold text-gray-800">Estatus Actual</h3>
                    </div>
                    <div class="p-4">
                        <div class="bg-orange-500 text-white p-4 rounded text-center">
                            <div class="text-sm font-bold">{{ $vehiculo->estatus->nombre ?? 'En Obra' }}</div>
                            <button class="mt-2 text-sm bg-white bg-opacity-20 px-3 py-1 rounded hover:bg-opacity-30">
                                Cambiar Obra
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pestañas de Información - Flexible para ocupar espacio restante -->
            <div class="bg-white border border-gray-300 rounded-lg flex-1 flex flex-col" x-data="{ activeTab: 'operacion' }">
                <div class="bg-gray-50 px-4 py-0 border-b border-gray-300">
                    <nav class="flex">
                        <button @click="activeTab = 'operacion'" 
                                :class="activeTab === 'operacion' ? 'border-blue-500 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                class="px-4 py-3 text-sm font-medium border-b-2 transition-colors duration-200">
                            Operación
                        </button>
                        <button @click="activeTab = 'documentos'" 
                                :class="activeTab === 'documentos' ? 'border-blue-500 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                class="px-4 py-3 text-sm font-medium border-b-2 transition-colors duration-200">
                            Documentos
                        </button>
                        <button @click="activeTab = 'mantenimientos'" 
                                :class="activeTab === 'mantenimientos' ? 'border-blue-500 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                class="px-4 py-3 text-sm font-medium border-b-2 transition-colors duration-200">
                            Mantenimientos
                        </button>
                    </nav>
                </div>

                <!-- Contenido de pestañas con scroll interno -->
                <div class="flex-1 overflow-hidden">
                    <!-- Contenido de Operación -->
                    <div x-show="activeTab === 'operacion'" class="p-4 h-full overflow-y-auto">
                        <div class="space-y-4">
                            <!-- Sección: Obra Actual -->
                            <div class="bg-blue-50 border border-blue-200 rounded p-3">
                                <h5 class="text-sm font-semibold text-blue-900 mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    Obra Actual
                                </h5>
                                
                                <div class="space-y-3">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs text-gray-600">Nombre de Obra</label>
                                            <div class="bg-gray-600 text-white px-2 py-1 rounded text-sm">
                                                Libramiento Monterrey
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600">Ubicación</label>
                                            <div class="bg-gray-600 text-white px-2 py-1 rounded text-sm">
                                                Monterrey, N.L.
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs text-gray-600">Fecha Inicio</label>
                                            <div class="bg-gray-600 text-white px-2 py-1 rounded text-sm">
                                                1/02/2025
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600">Km. Final</label>
                                            <div class="bg-gray-600 text-white px-2 py-1 rounded text-sm">
                                                200
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Sección: Personal Asignado -->
                            <div class="bg-green-50 border border-green-200 rounded p-3">
                                <h5 class="text-sm font-semibold text-green-900 mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                    </svg>
                                    Personal Asignado
                                </h5>
                                
                                <div class="space-y-3">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs text-gray-600">Operador Actual</label>
                                            <div class="flex items-center space-x-2">
                                                <div class="bg-gray-600 text-white px-2 py-1 rounded text-sm flex-1">
                                                    Marco Alfredo
                                                </div>
                                                <button class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs">
                                                    Cambiar
                                                </button>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600">Supervisor</label>
                                            <div class="bg-gray-600 text-white px-2 py-1 rounded text-sm">
                                                Tonko de las Mercedes
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs text-gray-600">Responsable</label>
                                            <div class="bg-gray-600 text-white px-2 py-1 rounded text-sm">
                                                Delgado Reyes
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600">Cert. Electrónica</label>
                                            <div class="bg-gray-600 text-white px-2 py-1 rounded text-sm">
                                                5588
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-xs text-gray-600">Licencia de Manejo</label>
                                        <div class="bg-gray-600 text-white px-2 py-1 rounded text-sm">
                                            DERM620912384
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contenido de Documentos -->
                    <div x-show="activeTab === 'documentos'" class="p-4 h-full overflow-y-auto">
                        <div class="space-y-4">
                            <h4 class="font-medium text-gray-700">Documentos del Vehículo</h4>
                            <div class="border border-gray-200 rounded-lg divide-y divide-gray-200">
                                <div class="p-4 hover:bg-gray-50 transition-colors duration-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">Tarjeta de Circulación</p>
                                            <p class="text-sm text-gray-500">Vigente hasta: 15/08/2025</p>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                ✓ Vigente
                                            </span>
                                            <button class="text-blue-600 hover:text-blue-900 transition-colors duration-200" 
                                                    title="Ver documento"
                                                    onclick="viewDocument('tarjeta_circulacion.pdf')">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 616 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4 hover:bg-gray-50 transition-colors duration-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">Póliza de Seguro</p>
                                            <p class="text-sm text-gray-500">Vigente hasta: 20/12/2025</p>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                ✓ Vigente
                                            </span>
                                            <button class="text-blue-600 hover:text-blue-900 transition-colors duration-200" 
                                                    title="Ver documento"
                                                    onclick="viewDocument('poliza_seguro.pdf')">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 616 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4 hover:bg-gray-50 transition-colors duration-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">Verificación Vehicular</p>
                                            <p class="text-sm text-gray-500">Vence: 30/08/2025</p>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                ⚠ Próximo a vencer
                                            </span>
                                            <button class="text-blue-600 hover:text-blue-900 transition-colors duration-200" 
                                                    title="Ver documento"
                                                    onclick="viewDocument('verificacion_vehicular.pdf')">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 616 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="pt-4 border-t border-gray-200">
                                <button class="w-full bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-md transition-colors duration-200 flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Agregar Documento
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Contenido de Mantenimientos -->
                    <div x-show="activeTab === 'mantenimientos'" class="p-4 h-full overflow-y-auto">
                        <div class="space-y-3">
                            <div class="p-3 border border-gray-200 rounded">
                                <div class="font-medium text-sm">Cambio de Aceite</div>
                                <div class="text-sm text-gray-600">Próximo: 130,000 km</div>
                                <div class="text-sm text-gray-500 mt-1">Faltante: 4,835 km</div>
                            </div>
                            
                            <div class="p-3 border border-gray-200 rounded">
                                <div class="font-medium text-sm">Revisión General</div>
                                <div class="text-sm text-gray-600">Última: 15/06/2025</div>
                                <div class="text-sm text-gray-500 mt-1">Estado: Completado</div>
                            </div>
                            
                            <button class="w-full mt-4 bg-blue-500 text-white py-2 px-4 rounded text-sm hover:bg-blue-600">
                                Ver Historial Completo
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="//unpkg.com/alpinejs" defer></script>
<script>
// Función para visualizar documentos
function viewDocument(fileName) {
    const documentUrl = `/storage/vehiculos/documentos/${fileName}`;
    
    if (fileName.toLowerCase().endsWith('.pdf')) {
        window.open(documentUrl, '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');
    } else {
        showDocumentModal(documentUrl, fileName);
    }
}

// Función para mostrar modal de documento
function showDocumentModal(url, fileName) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
    modal.id = 'documentModal';
    
    modal.innerHTML = `
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">${fileName}</h3>
                <button onclick="closeDocumentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="mb-4">
                <div class="flex justify-center bg-gray-100 p-4 rounded">
                    <div class="text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-600">Vista previa del documento</p>
                        <p class="text-sm text-gray-500">${fileName}</p>
                    </div>
                </div>
            </div>
            <div class="flex justify-end space-x-3">
                <button onclick="downloadDocument('${url}', '${fileName}')" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm">
                    Descargar
                </button>
                <button onclick="closeDocumentModal()" 
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded text-sm">
                    Cerrar
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

// Función para cerrar modal
function closeDocumentModal() {
    const modal = document.getElementById('documentModal');
    if (modal) {
        modal.remove();
    }
}

// Función para descargar documento
function downloadDocument(url, fileName) {
    const link = document.createElement('a');
    link.href = url;
    link.download = fileName;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Función para imprimir
function imprimirDetalles() {
    window.print();
}

// Cerrar modal con tecla Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDocumentModal();
    }
});
</script>
@endpush
