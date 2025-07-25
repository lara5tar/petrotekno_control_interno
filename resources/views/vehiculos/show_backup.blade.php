@extends('layouts.app')

@section('title', 'Detalles del Vehículo')

@section('header', 'Detalles del Vehículo')

@section('content')
<div class="p-6">
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Vehículos', 'url' => route('vehiculos.index')],
        ['label' => 'Detalles del Vehículo']
    ]" />

    {{-- Mensaje de éxito --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
            <strong class="font-bold">¡Éxito!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Encabezado con acciones -->
    <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
        <div class="flex justify-between items-center">
            <div class="flex-1 mr-8">
                <h2 class="text-xl font-bold text-gray-800 mb-2">
                    Gestionar Vehículos / {{ $vehiculo->marca ?? 'Toyota' }} {{ $vehiculo->modelo ?? 'Hilux' }} ({{ $vehiculo->placas ?? 'ABC-123' }})
                </h2>
                <p class="text-sm text-gray-600">
                    Control de Inventario de Vehículos v1.0
                </p>
            </div>
            <div class="flex gap-3 ml-4">
                <a href="{{ route('vehiculos.edit', $vehiculo->id ?? 1) }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded flex items-center transition duration-200 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                    Editar
                </a>
                <button onclick="window.print()" 
                        class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded flex items-center transition duration-200 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
                    </svg>
                    Imprimir
                </button>
                <a href="{{ route('vehiculos.index') }}" 
                   class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 px-4 rounded flex items-center transition duration-200 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Regresar
                </a>
            </div>
        </div>
    </div>        <!-- Contenido Principal - Sin Scroll, Ocupa toda la pantalla -->
    <div class="h-screen flex flex-col p-4 gap-4">
        <!-- Grid principal 50/50 -->
        <div class="flex-1 grid grid-cols-2 gap-4">
            <!-- Panel Izquierdo - Datos Generales y Fotografía -->
            <div class="flex flex-col gap-4">
                <!-- Datos Generales - Compacto -->
                <div class="bg-white border border-gray-300 rounded-lg flex-1">
                    <div class="bg-gray-50 px-3 py-2 border-b border-gray-300">
                        <h3 class="font-medium text-gray-800 text-sm flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" clip-rule="evenodd" />
                            </svg>
                            Datos Generales
                        </h3>
                    </div>
                    <div class="p-3 space-y-3 overflow-y-auto">
                        <!-- Marca, Año, Modelo en una fila -->
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Marca</label>
                                <div class="bg-blue-500 text-white px-2 py-1 rounded text-xs font-medium text-center">
                                    {{ $vehiculo->marca ?? 'Nissan' }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Año</label>
                                <div class="bg-blue-500 text-white px-2 py-1 rounded text-xs font-medium text-center">
                                    {{ $vehiculo->anio ?? '2018' }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Modelo</label>
                                <div class="bg-blue-500 text-white px-2 py-1 rounded text-xs font-medium text-center">
                                    {{ $vehiculo->modelo ?? 'NP300' }}
                                </div>
                            </div>
                        </div>
                        
                        <!-- VIN y Placas -->
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">VIN</label>
                                <div class="bg-gray-600 text-white px-2 py-1 rounded text-xs font-medium break-all">
                                    {{ $vehiculo->n_serie ?? '3N6RTD45561CJDDJF' }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Placas</label>
                                <div class="bg-blue-500 text-white px-2 py-1 rounded text-xs font-medium text-center">
                                    {{ $vehiculo->placas ?? 'XSG-323A' }}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Póliza y Derecho Vehicular -->
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Póliza de Seguro</label>
                                <div class="flex items-center space-x-1">
                                    <div class="bg-yellow-500 text-white px-2 py-1 rounded text-xs font-medium flex-1 text-center">
                                        R400057579
                                    </div>
                                    <button class="bg-yellow-600 hover:bg-yellow-700 text-white p-1 rounded text-xs transition duration-200" title="Ver">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Derecho Vehicular</label>
                                <div class="flex items-center space-x-1">
                                    <div class="bg-yellow-500 text-white px-2 py-1 rounded text-xs font-medium flex-1 text-center">
                                        2025
                                    </div>
                                    <button class="bg-yellow-600 hover:bg-yellow-700 text-white p-1 rounded text-xs transition duration-200" title="Ver">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fotografía - Compacta -->
                <div class="bg-white border border-gray-300 rounded-lg h-48">
                    <div class="bg-gray-50 px-3 py-2 border-b border-gray-300">
                        <h3 class="font-medium text-gray-800 text-sm flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                            </svg>
                            Fotografía
                        </h3>
                    </div>
                    <div class="p-3 h-full flex flex-col justify-center">
                        <div class="flex justify-center mb-2">
                            <div class="relative">
                                <img src="/images/vehiculo-ejemplo.jpg" 
                                     alt="Vehículo {{ $vehiculo->marca ?? 'Nissan' }} {{ $vehiculo->modelo ?? 'NP300' }}" 
                                     class="w-24 h-16 rounded shadow object-cover"
                                     onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjYwIiB2aWV3Qm94PSIwIDAgMTAwIDYwIiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjYwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik01MCAyNVY0NU00MCAzNUg2MCIgc3Ryb2tlPSIjOUI5Q0E0IiBzdHJva2Utd2lkdGg9IjEiLz4KPHRleHQgeD0iNTAiIHk9IjUwIiBmb250LWZhbWlseT0iQXJpYWwsIHNhbnMtc2VyaWYiIGZvbnQtc2l6ZT0iOCIgZmlsbD0iIzlCOUNBNCIgdGV4dC1hbmNob3I9Im1pZGRsZSI+U2luIGZvdG88L3RleHQ+Cjwvc3ZnPgo='">
                                <div class="absolute bottom-0 left-0 bg-black bg-opacity-75 text-white px-1 py-0.5 rounded-br text-xs">
                                    15/07/2025
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-center space-x-1">
                            <button class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs">Actualizar</button>
                            <button class="bg-gray-500 hover:bg-gray-600 text-white px-2 py-1 rounded text-xs">Descargar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel Derecho - Kilometraje, Estado y Pestañas -->
            <div class="flex flex-col gap-4">
                <!-- Kilometraje y Estado en fila horizontal -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- Último Kilometraje -->
                    <div class="bg-white border border-gray-300 rounded-lg">
                        <div class="bg-gray-50 px-3 py-2 border-b border-gray-300">
                            <h3 class="font-medium text-gray-800 text-sm">Último Kilometraje</h3>
                        </div>
                        <div class="p-3">
                            <div class="bg-gray-800 text-white p-3 rounded text-center">
                                <div class="text-xl font-bold">{{ $vehiculo->kilometraje_actual ?? '125,165' }}</div>
                                <div class="text-xs text-gray-300">Kilómetros</div>
                            </div>
                        </div>
                    </div>

                    <!-- Estatus Actual -->
                    <div class="bg-white border border-gray-300 rounded-lg">
                        <div class="bg-gray-50 px-3 py-2 border-b border-gray-300">
                            <h3 class="font-medium text-gray-800 text-sm">Estatus Actual</h3>
                        </div>
                        <div class="p-3">
                            <div class="bg-orange-500 text-white p-3 rounded text-center">
                                <div class="text-sm font-bold">{{ $vehiculo->estatus->nombre ?? 'En Obra' }}</div>
                                <button class="mt-2 text-xs bg-white bg-opacity-20 px-2 py-1 rounded hover:bg-opacity-30 transition duration-200">
                                    Cambiar Obra
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pestañas de Información - Ocupa el espacio restante -->
                <div class="bg-white border border-gray-300 rounded-lg flex-1 flex flex-col" x-data="{ activeTab: 'operacion' }">
                    <div class="border-b border-gray-200">
                        <nav class="flex">
                            <button @click="activeTab = 'operacion'" 
                                    :class="activeTab === 'operacion' ? 'border-blue-500 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                    class="w-1/3 py-2 px-2 text-center border-b-2 font-medium text-xs">
                                Operación
                            </button>
                            <button @click="activeTab = 'documentos'" 
                                    :class="activeTab === 'documentos' ? 'border-blue-500 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                    class="w-1/3 py-2 px-2 text-center border-b-2 font-medium text-xs">
                                Documentos
                            </button>
                            <button @click="activeTab = 'mantenimientos'" 
                                    :class="activeTab === 'mantenimientos' ? 'border-blue-500 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                    class="w-1/3 py-2 px-2 text-center border-b-2 font-medium text-xs">
                                Mantenimientos
                            </button>
                        </nav>
                    </div>

                    <!-- Contenido de pestañas con scroll interno si es necesario -->
                    <div class="flex-1 overflow-hidden">
                        <!-- Contenido de Operación -->
                <div x-show="activeTab === 'operacion'" class="p-3">
                    <div class="space-y-4">
                        <!-- Sección: Obra Actual -->
                        <div class="bg-blue-50 border border-blue-200 rounded p-3">
                            <h5 class="text-sm font-semibold text-blue-900 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                Obra Actual
                            </h5>
                            
                            <div class="space-y-2">
                                <!-- Nombre de Obra -->
                                <div class="bg-white p-2 rounded border">
                                    <div class="text-xs font-medium text-gray-600">Nombre de Obra</div>
                                    <div class="font-semibold text-sm text-gray-900">Libramiento Monterrey</div>
                                </div>
                                
                                <!-- Ubicación -->
                                <div class="bg-white p-2 rounded border">
                                    <div class="text-xs font-medium text-gray-600">Lugar de las Operaciones</div>
                                    <div class="font-semibold text-sm text-gray-900">Monterrey, N.L.</div>
                                </div>
                                
                                <!-- Fechas -->
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="bg-white p-2 rounded border">
                                        <div class="text-xs font-medium text-gray-600">Inicio de Obra</div>
                                        <div class="font-semibold text-xs text-gray-900">1/02/2025</div>
                                    </div>
                                    <div class="bg-white p-2 rounded border">
                                        <div class="text-xs font-medium text-gray-600">Km. Final</div>
                                        <div class="font-semibold text-xs text-gray-900">200</div>
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
                            
                            <div class="space-y-2">
                                <!-- Operador Actual -->
                                <div class="bg-white p-2 rounded border">
                                    <div class="flex justify-between items-center mb-1">
                                        <div class="text-xs font-medium text-gray-600">Operador Actual</div>
                                        <button class="text-xs bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded transition duration-200">Cambiar</button>
                                    </div>
                                    <div class="font-semibold text-sm text-gray-900">Marco Alfredo</div>
                                </div>
                                
                                <!-- Supervisor -->
                                <div class="bg-white p-2 rounded border">
                                    <div class="text-xs font-medium text-gray-600">Nombre del Supervisor</div>
                                    <div class="font-semibold text-sm text-gray-900">Tonko de las Mercedes</div>
                                </div>
                                
                                <!-- Responsable -->
                                <div class="bg-white p-2 rounded border">
                                    <div class="text-xs font-medium text-gray-600">Responsable</div>
                                    <div class="font-semibold text-sm text-gray-900">Delgado Reyes</div>
                                </div>
                                
                                <!-- Información de contacto -->
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="bg-white p-2 rounded border">
                                        <div class="text-xs font-medium text-gray-600">Cert. Electrónica</div>
                                        <div class="font-semibold text-xs text-gray-900">5588</div>
                                    </div>
                                    <div class="bg-white p-2 rounded border">
                                        <div class="text-xs font-medium text-gray-600">Licencia</div>
                                        <div class="font-semibold text-xs break-all text-gray-900">DERM620912384</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contenido de Documentos -->
                <div x-show="activeTab === 'documentos'" class="p-4">
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-700 mb-3">Documentos del Vehículo</h4>
                        <div class="space-y-4">
                            <!-- Lista de documentos -->
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
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
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
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
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
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4 hover:bg-gray-50 transition-colors duration-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">Tenencia 2025</p>
                                            <p class="text-sm text-gray-500">Pagada</p>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                ✓ Al corriente
                                            </span>
                                            <button class="text-blue-600 hover:text-blue-900 transition-colors duration-200" 
                                                    title="Ver documento"
                                                    onclick="viewDocument('tenencia_2025.pdf')">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <button class="w-full bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-md transition-colors duration-200 flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Agregar Documento
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contenido de Mantenimientos -->
                <div x-show="activeTab === 'mantenimientos'" class="p-3">
                    <div class="space-y-2">
                        <div class="p-2 border border-gray-200 rounded">
                            <div class="font-medium text-sm">Cambio de Aceite</div>
                            <div class="text-xs text-gray-600">Próximo: 130,000 km</div>
                            <div class="text-xs text-gray-500 mt-1">Faltante: 4,835 km</div>
                        </div>
                        
                        <div class="p-2 border border-gray-200 rounded">
                            <div class="font-medium text-sm">Revisión General</div>
                            <div class="text-xs text-gray-600">Última: 15/06/2025</div>
                            <div class="text-xs text-gray-500 mt-1">Estado: Completado</div>
                        </div>
                        
                        <button class="w-full mt-3 bg-blue-500 text-white py-2 px-3 rounded text-xs hover:bg-blue-600 transition duration-200">
                            Ver Historial Completo
                        </button>
                    </div>
                </div>
            </div>

            <!-- Historial de Kilometrajes -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-800">Kilometrajes</h3>
                    <button class="bg-blue-500 text-white px-2 py-1 rounded text-xs hover:bg-blue-600 transition duration-200">
                        Capturar Nuevo
                    </button>
                </div>
                <div class="p-3">
                    <div class="space-y-1 max-h-48 overflow-y-auto">
                        <div class="grid grid-cols-4 gap-2 text-xs font-medium text-gray-600 pb-1 border-b">
                            <div>Km</div>
                            <div>Fecha</div>
                            <div>Obra</div>
                            <div>Usuario</div>
                        </div>
                        
                        <!-- Registros de kilometraje -->
                        <div class="grid grid-cols-4 gap-2 text-xs py-1">
                            <div class="font-medium">125,165</div>
                            <div>02/06</div>
                            <div>Lib. Monterrey</div>
                            <div>M. Delgado</div>
                        </div>
                        
                        <div class="grid grid-cols-4 gap-2 text-xs py-1 bg-gray-50">
                            <div class="font-medium">124,356</div>
                            <div>29/05</div>
                            <div>Lib. Monterrey</div>
                            <div>M. Delgado</div>
                        </div>
                        
                        <div class="grid grid-cols-4 gap-2 text-xs py-1">
                            <div class="font-medium">122,908</div>
                            <div>28/05</div>
                            <div>Lib. Monterrey</div>
                            <div>M. Delgado</div>
                        </div>
                        
                        <div class="grid grid-cols-4 gap-2 text-xs py-1 bg-gray-50">
                            <div class="font-medium">120,574</div>
                            <div>28/05</div>
                            <div>Lib. Monterrey</div>
                            <div>D. López</div>
                        </div>
                        
                        <div class="grid grid-cols-4 gap-2 text-xs py-1">
                            <div class="font-medium">118,117</div>
                            <div>15/04</div>
                            <div>Carretera ABC</div>
                            <div>J. Pérez</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Función para visualizar documentos
function viewDocument(fileName) {
    // Crear URL temporal o abrir en nueva ventana
    // En un entorno real, esto sería la URL del documento almacenado
    const documentUrl = `/storage/vehiculos/documentos/${fileName}`;
    
    // Mostrar modal o abrir en nueva pestaña
    if (fileName.toLowerCase().endsWith('.pdf')) {
        // Para PDFs, abrir en nueva pestaña
        window.open(documentUrl, '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');
    } else {
        // Para imágenes u otros archivos, mostrar modal
        showDocumentModal(documentUrl, fileName);
    }
}

// Función para mostrar modal de documento
function showDocumentModal(url, fileName) {
    // Crear modal dinámicamente
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
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Descargar
                </button>
                <button onclick="closeDocumentModal()" 
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded text-sm transition duration-200">
                    Cerrar
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Cerrar modal al hacer clic fuera de él
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeDocumentModal();
        }
    });
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
    link.target = '_blank';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Cerrar modal con tecla Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDocumentModal();
    }
});
</script>
@endpush

@push('scripts')
<script src="//unpkg.com/alpinejs" defer></script>
<script>
    // Función para imprimir la página
    function imprimirDetalles() {
        window.print();
    }
    
    // Función para actualizar foto (simulada)
    function actualizarFoto() {
        alert('Funcionalidad de actualizar foto (simulación frontend)');
    }
    
    // Función para cambiar operador (simulada)
    function cambiarOperador() {
        alert('Funcionalidad de cambiar operador (simulación frontend)');
    }
    
    // Función para cambiar obra (simulada)
    function cambiarObra() {
        alert('Funcionalidad de cambiar obra (simulación frontend)');
    }
    
    // Función para capturar nuevo kilometraje (simulada)
    function capturarKilometraje() {
        alert('Funcionalidad de capturar kilometraje (simulación frontend)');
    }
</script>
@endpush