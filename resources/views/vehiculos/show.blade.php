@extends('layouts.app')

@section('title', 'Detalles del Vehículo')

@section('content')
<!-- Breadcrumb -->
<x-breadcrumb :items="[
    ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
    ['label' => 'Vehículos', 'url' => route('vehiculos.index')],
    ['label' => 'Detalle del Vehículo']
]" />

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
                @hasPermission('imprimir_vehiculos')
                <button onclick="imprimirDetalles()" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded text-sm transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-1a2 2 0 00-2-2H9a2 2 0 00-2 2v1a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Imprimir
                </button>
                @endhasPermission
                
                @hasPermission('editar_vehiculos')
                <a href="{{ route('vehiculos.edit', $vehiculo->id ?? 1) }}" 
                   class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded text-sm transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Editar
                </a>
                @endhasPermission
                
                <a href="{{ route('vehiculos.index') }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded text-sm transition duration-200 flex items-center">
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
                    <!-- Fotografía del Vehículo integrada en Datos Generales -->
                    <div class="mb-4">
                        <div class="relative">
                            <div class="bg-gray-100 rounded overflow-hidden mb-2">
                                <img src="{{ $vehiculo->imagen ?? '/images/placeholder-vehicle.jpg' }}" 
                                     alt="Vehículo {{ $vehiculo->marca ?? 'Nissan' }} {{ $vehiculo->modelo ?? 'NP300' }}" 
                                     class="w-full h-auto object-contain max-h-40"
                                     onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDMwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIzMDAiIGhlaWdodD0iMjAwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0xNTAgODBWMTIwTTEzMCAxMDBIMTcwIiBzdHJva2U9IiM5QjlDQTQiIHN0cm9rZS13aWR0aD0iMiIvPgo8dGV4dCB4PSIxNTAiIHk9IjE0MCIgZm9udC1mYW1pbHk9IkFyaWFsLCBzYW5zLXNlcmlmIiBmb250LXNpemU9IjEyIiBmaWxsPSIjOUI5Q0E0IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5TaW4gZm90bzwvdGV4dD4KPC9zdmc+Cg=='">
                                <div class="absolute bottom-1 left-1 bg-black bg-opacity-75 text-white px-1 py-0.5 rounded text-xs">
                                    15/07/2025
                                </div>
                            </div>
                            <div class="flex justify-center space-x-2">
                                <button class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs">
                                    <svg class="w-3 h-3 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Descargar
                                </button>
                            </div>
                        </div>
                    </div>
                    
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
                    
                    <div class="grid grid-cols-1 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Año</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $vehiculo->año ?? '2023' }}
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

                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Derecho Vehicular</label>
                            <div class="flex items-center space-x-2">
                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium flex-1">
                                    {{ $vehiculo->derecho_vehicular ?? 'DV-2025-001234' }}
                                </div>
                                <button onclick="viewDocument('derecho_vehicular.pdf')" 
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-2 rounded text-sm transition duration-200"
                                        title="Ver documento">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Póliza de Seguro</label>
                            <div class="flex items-center space-x-2">
                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium flex-1">
                                    {{ $vehiculo->poliza_seguro ?? 'PS-2025-567890' }}
                                </div>
                                <button onclick="viewDocument('poliza_seguro.pdf')" 
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-2 rounded text-sm transition duration-200"
                                        title="Ver documento">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



        </div>

        <!-- Panel Derecho -->
        <div class="flex flex-col gap-4">
            <!-- Kilometraje y Estado en fila horizontal -->
            <div class="grid grid-cols-2 gap-3">
                <!-- Último Kilometraje -->
                <div>
                    <div class="text-sm font-medium text-gray-600 mb-1">Último Kilometraje</div>
                    <div class="bg-gray-800 text-white p-2 rounded text-center">
                        <div class="text-sm font-bold">{{ $vehiculo->kilometraje_actual ?? '125,145' }}</div>
                    </div>
                </div>

                <!-- Estatus Actual -->
                <div>
                    <div class="text-sm font-medium text-gray-600 mb-1">Estatus Actual</div>
                    <select class="w-full text-sm bg-orange-500 text-white p-2 rounded border-none focus:ring-1 focus:ring-white">
                        <option value="disponible" selected>Disponible</option>
                        <option value="en_obra">En Obra</option>
                        <option value="mantenimiento">Mantenimiento</option>
                        <option value="fuera_servicio">Fuera de Servicio</option>
                    </select>
                </div>
            </div>

            <!-- Pestañas de Información - Flexible para ocupar espacio restante -->
            <div class="bg-white border border-gray-300 rounded-lg flex-1 flex flex-col" x-data="{ activeTab: 'operacion' }">
                <div class="bg-gray-50 px-3 py-1 border-b border-gray-300">
                    <nav class="flex">
                        <button @click="activeTab = 'operacion'" 
                                :class="activeTab === 'operacion' ? 'border-gray-500 text-gray-700 bg-gray-100' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                class="px-3 py-2 text-sm font-medium border-b-2 transition-colors duration-200">
                            Operación
                        </button>
                        <button @click="activeTab = 'kilometraje'" 
                                :class="activeTab === 'kilometraje' ? 'border-gray-500 text-gray-700 bg-gray-100' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                class="px-3 py-2 text-sm font-medium border-b-2 transition-colors duration-200">
                            Kilometraje
                        </button>
                        <button @click="activeTab = 'documentos'" 
                                :class="activeTab === 'documentos' ? 'border-gray-500 text-gray-700 bg-gray-100' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                class="px-3 py-2 text-sm font-medium border-b-2 transition-colors duration-200">
                            Documentos
                        </button>
                        <button @click="activeTab = 'mantenimientos'" 
                                :class="activeTab === 'mantenimientos' ? 'border-gray-500 text-gray-700 bg-gray-100' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                class="px-3 py-2 text-sm font-medium border-b-2 transition-colors duration-200">
                            Mantenimientos
                        </button>
                    </nav>
                </div>

                <!-- Contenido de pestañas con scroll interno -->
                <div class="flex-1 overflow-hidden">
                    <!-- Contenido de Operación -->
                    <div x-show="activeTab === 'operacion'" class="p-6 bg-gray-50">
                        <div class="space-y-6">
                            <!-- Sección: Obra Actual -->
                            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-center mb-4">
                                    <h5 class="text-base font-semibold text-gray-800 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        Obra Actual
                                    </h5>
                                    <button class="bg-blue-600 hover:bg-blue-700 text-white py-1 px-2 rounded-md transition-colors duration-200 flex items-center text-xs">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Cambiar Obra
                                    </button>
                                </div>
                                
                                <div class="space-y-2">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600">Nombre de Obra</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                Libramiento Monterrey
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600">Ubicación</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                Monterrey, N.L.
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600">Fecha Inicio</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                1/02/2025
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600">Fecha Finalización</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                15/07/2025
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600">Km. Inicial</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                124,500
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600">Km. Final</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                200
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm text-gray-600">Responsable de Obra</label>
                                        <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                            Delgado Reyes
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Sección: Personal Asignado -->
                            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-center mb-4">
                                    <h5 class="text-base font-semibold text-gray-800 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                        </svg>
                                        Personal Asignado
                                    </h5>
                                    <button class="bg-blue-600 hover:bg-blue-700 text-white py-1 px-2 rounded-md transition-colors duration-200 flex items-center text-xs">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Cambiar Personal
                                    </button>
                                </div>
                                
                                <div class="space-y-2">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600">Operador Actual</label>
                                            <div class="flex items-center space-x-2">
                                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm flex-1">
                                                    Marco Alfredo
                                                </div>
                                                <button class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-sm">
                                                    Cambiar
                                                </button>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600">Supervisor</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                Tonko de las Mercedes
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600">Cert. Electrónica</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                5588
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600">Licencia de Manejo</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                DERM620912384
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Aquí iba la sección de Kilometrajes que ahora tiene su propia pestaña -->
                        </div>
                    </div>

                    <!-- Contenido de Kilometraje -->
                    <div x-show="activeTab === 'kilometraje'" class="p-6 bg-gray-50">
                        <div class="space-y-6">
                            <!-- Kilometrajes del Vehículo -->
                            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-center mb-4">
                                    <h5 class="text-base font-semibold text-gray-800 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                        Registro de Kilometraje
                                    </h5>
                                    <button onclick="showAddKilometrajeModal()" class="bg-blue-600 hover:bg-blue-700 text-white py-1 px-2 rounded-md transition-colors duration-200 flex items-center text-xs">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Capturar Nuevo
                                    </button>
                                </div>
                                
                                <!-- Tabla de Kilometrajes -->
                                <div class="overflow-x-auto">
                                    <table id="kilometraje-table" class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-3 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Kilometraje</th>
                                                <th scope="col" class="px-3 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                                <th scope="col" class="px-3 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Ubicación</th>
                                                <th scope="col" class="px-3 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Registró</th>
                                                <th scope="col" class="px-3 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">125,145</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">28/05/2023</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">Libramiento Monterrey</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">Marco Delgado</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                    <button class="text-blue-600 hover:text-blue-900 transition-colors duration-200" title="Ver detalles">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">124,956</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">26/05/2023</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">Libramiento Monterrey</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">Marco Delgado</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                    <button class="text-blue-600 hover:text-blue-900 transition-colors duration-200" title="Ver detalles">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">122,890</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">20/05/2023</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">Libramiento Monterrey</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">Marco Delgado</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                    <button class="text-blue-600 hover:text-blue-900 transition-colors duration-200" title="Ver detalles">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">120,574</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">15/05/2023</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">Libramiento Monterrey</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">Diego López</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                    <button class="text-blue-600 hover:text-blue-900 transition-colors duration-200" title="Ver detalles">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">118,117</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">10/05/2023</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">Carretera ABC</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">José Pérez</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                    <button class="text-blue-600 hover:text-blue-900 transition-colors duration-200" title="Ver detalles">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contenido de Documentos -->
                    <div x-show="activeTab === 'documentos'" class="p-6 bg-gray-50">
                        <div class="space-y-6">
                            <!-- Documentos del Vehículo -->
                            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-center mb-4">
                                    <h5 class="text-base font-semibold text-gray-800 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Documentos del Vehículo
                                    </h5>
                                    <button onclick="showUploadDocumentModal()" class="bg-blue-600 hover:bg-blue-700 text-white py-1 px-2 rounded-md transition-colors duration-200 flex items-center text-xs">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Subir Documento
                                    </button>
                                </div>
                                
                                <!-- Documentos Obligatorios -->
                                <h6 class="text-sm font-medium text-gray-700 mb-2">Documentos Obligatorios</h6>
                                <ul class="divide-y divide-gray-200 mb-6">
                                    <!-- Tarjeta de Circulación -->
                                    <li class="py-2 flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <div>
                                                <span class="text-sm font-medium text-gray-800">Tarjeta de Circulación</span>
                                                <p class="text-xs text-gray-500">Vence: 15/01/2025</p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button onclick="viewDocument('tarjeta')" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                Ver
                                            </button>
                                            <button onclick="downloadDocument('tarjeta')" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                                Descargar
                                            </button>
                                        </div>
                                    </li>
                                    
                                    <!-- Póliza de Seguro -->
                                    <li class="py-2 flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <div>
                                                <span class="text-sm font-medium text-gray-800">Póliza de Seguro</span>
                                                <p class="text-xs text-gray-500">Vence: 30/06/2024</p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button onclick="viewDocument('poliza')" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                Ver
                                            </button>
                                            <button onclick="downloadDocument('poliza')" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                                Descargar
                                            </button>
                                        </div>
                                    </li>
                                    
                                    <!-- Verificación Vehicular -->
                                    <li class="py-2 flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <div>
                                                <span class="text-sm font-medium text-gray-800">Verificación Vehicular</span>
                                                <p class="text-xs text-gray-500">Vence: 10/06/2024</p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button onclick="viewDocument('verificacion')" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                Ver
                                            </button>
                                            <button onclick="downloadDocument('verificacion')" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                                Descargar
                                            </button>
                                        </div>
                                    </li>
                                </ul>
                                
                                <!-- Documentos Adicionales -->
                                <h6 class="text-sm font-medium text-gray-700 mb-2">Documentos Adicionales</h6>
                                <div id="documentos-adicionales">
                                    <!-- Si no hay documentos adicionales -->
                                    <div class="text-center py-4 text-gray-500 text-sm italic" id="no-documentos" x-show="true">
                                        No hay documentos adicionales registrados
                                    </div>
                                    
                                    <!-- Lista de documentos adicionales (se llenará dinámicamente) -->
                                    <ul class="divide-y divide-gray-200" id="lista-documentos-adicionales">
                                        <!-- Los documentos adicionales se agregarán aquí dinámicamente -->
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contenido de Mantenimientos -->
                    <div x-show="activeTab === 'mantenimientos'" class="p-6 bg-gray-50">
                        <div class="space-y-6">
                            <!-- Mantenimientos del Vehículo -->
                            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-center mb-4">
                                    <h5 class="text-base font-semibold text-gray-800 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Mantenimientos Recientes
                                    </h5>
                                    <button class="bg-blue-600 hover:bg-blue-700 text-white py-1 px-2 rounded-md transition-colors duration-200 flex items-center text-xs">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Registrar Mantenimiento
                                    </button>
                                </div>
                                
                                <!-- Tabla de Mantenimientos -->
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-3 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                                <th scope="col" class="px-3 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                                <th scope="col" class="px-3 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Kilometraje</th>
                                                <th scope="col" class="px-3 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Taller</th>
                                                <th scope="col" class="px-3 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                                <th scope="col" class="px-3 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">15/03/2024</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">Preventivo</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">125,000 km</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">Taller Mecánico Central</td>
                                                <td class="px-3 py-2 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Completado
                                                    </span>
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                    <button class="text-blue-600 hover:text-blue-900 transition-colors duration-200" title="Ver detalles">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">10/01/2024</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">Correctivo</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">120,500 km</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">Servicio Oficial Toyota</td>
                                                <td class="px-3 py-2 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Completado
                                                    </span>
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                    <button class="text-blue-600 hover:text-blue-900 transition-colors duration-200" title="Ver detalles">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">05/12/2023</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">Preventivo</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">115,000 km</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">Taller Mecánico Central</td>
                                                <td class="px-3 py-2 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Completado
                                                    </span>
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                    <button class="text-blue-600 hover:text-blue-900 transition-colors duration-200" title="Ver detalles">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Botones de acción -->
                                <div class="flex justify-end items-center pt-4 border-t border-gray-200 mt-4">
                                    <button class="bg-blue-600 hover:bg-blue-700 text-white py-1 px-2 rounded-md transition-colors duration-200 flex items-center text-xs">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                        </svg>
                                        Ver Historial Completo
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal para subir documentos -->
<div id="uploadDocumentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Subir Documento</h3>
            <button onclick="closeUploadDocumentModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="uploadDocumentForm" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="document_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre del Documento</label>
                <input type="text" id="document_name" name="document_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="document_file" class="block text-sm font-medium text-gray-700 mb-1">Archivo</label>
                <input type="file" id="document_file" name="document_file" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="expiration_date" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Vencimiento (opcional)</label>
                <input type="date" id="expiration_date" name="expiration_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeUploadDocumentModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded text-sm">
                    Cancelar
                </button>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm">
                    Subir
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para capturar kilometraje -->
<div id="addKilometrajeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Registrar Nuevo Kilometraje</h3>
            <button onclick="closeAddKilometrajeModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="addKilometrajeForm">
            <div class="mb-4">
                <label for="kilometraje" class="block text-sm font-medium text-gray-700 mb-1">Kilometraje</label>
                <input type="number" id="kilometraje" name="kilometraje" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="fecha" class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                <input type="date" id="fecha" name="fecha" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="ubicacion" class="block text-sm font-medium text-gray-700 mb-1">Ubicación</label>
                <input type="text" id="ubicacion" name="ubicacion" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeAddKilometrajeModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded text-sm">
                    Cancelar
                </button>
                <button type="button" onclick="submitKilometraje()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm">
                    Guardar
                </button>
            </div>
        </form>
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

// Funciones para el modal de subir documentos
function showUploadDocumentModal() {
    const modal = document.getElementById('uploadDocumentModal');
    modal.classList.remove('hidden');
}

function closeUploadDocumentModal() {
    const modal = document.getElementById('uploadDocumentModal');
    modal.classList.add('hidden');
    document.getElementById('uploadDocumentForm').reset();
}

// Manejar el envío del formulario de documentos
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('uploadDocumentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Aquí iría la lógica para enviar el documento al servidor
        const formData = new FormData(this);
        formData.append('vehicle_id', '{{ $vehiculo->id }}'); // Agregar el ID del vehículo
        
        // Simulación de subida exitosa (en producción, aquí iría una llamada AJAX)
        console.log('Subiendo documento:', formData.get('document_name'));
        
        // Después de subir, agregar el documento a la lista
        addDocumentToList({
            name: formData.get('document_name'),
            expiration_date: formData.get('expiration_date') ? formatDate(formData.get('expiration_date')) : 'Sin fecha de vencimiento'
        });
        
        closeUploadDocumentModal();
    });
});

// Función para agregar un documento a la lista
function addDocumentToList(document) {
    // Ocultar el mensaje de "No hay documentos"
    const noDocumentos = document.getElementById('no-documentos');
    if (noDocumentos) {
        noDocumentos.style.display = 'none';
    }
    
    // Crear el elemento de lista para el nuevo documento
    const li = document.createElement('li');
    li.className = 'py-2 flex items-center justify-between';
    li.innerHTML = `
        <div class="flex items-center">
            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <div>
                <span class="text-sm font-medium text-gray-800">${document.name}</span>
                <p class="text-xs text-gray-500">Vence: ${document.expiration_date}</p>
            </div>
        </div>
        <div class="flex space-x-2">
            <button onclick="viewDocument('custom')" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Ver
            </button>
            <button onclick="downloadDocument('custom')" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Descargar
            </button>
        </div>
    `;
    
    // Agregar el nuevo documento a la lista
    document.getElementById('lista-documentos-adicionales').appendChild(li);
}

// Función para formatear fecha (YYYY-MM-DD a DD/MM/YYYY)
function formatDate(dateString) {
    const parts = dateString.split('-');
    return `${parts[2]}/${parts[1]}/${parts[0]}`;
}

// Función para imprimir
function imprimirDetalles() {
    window.print();
}

// Funciones para el modal de kilometraje
function showAddKilometrajeModal() {
    const modal = document.getElementById('addKilometrajeModal');
    modal.classList.remove('hidden');
}

function closeAddKilometrajeModal() {
    const modal = document.getElementById('addKilometrajeModal');
    modal.classList.add('hidden');
    document.getElementById('addKilometrajeForm').reset();
}

function submitKilometraje() {
    // Obtener los valores del formulario
    const kilometraje = document.getElementById('kilometraje').value;
    const fecha = document.getElementById('fecha').value;
    const ubicacion = document.getElementById('ubicacion').value;
    
    // Validar que los campos no estén vacíos
    if (!kilometraje || !fecha || !ubicacion) {
        alert('Por favor complete todos los campos');
        return;
    }
    
    // Aquí iría la lógica para enviar los datos al servidor
    console.log('Enviando datos de kilometraje:', { kilometraje, fecha, ubicacion });
    
    // Simulamos una respuesta exitosa
    setTimeout(() => {
        // Agregar el nuevo kilometraje a la tabla (simulado)
        const tbody = document.querySelector('#kilometraje-table tbody');
        if (tbody) {
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">${kilometraje}</td>
                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">${formatDate(fecha)}</td>
                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">${ubicacion}</td>
                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">Usuario Actual</td>
                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                    <button class="text-blue-600 hover:text-blue-900 transition-colors duration-200" title="Ver detalles">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </td>
            `;
            tbody.insertBefore(newRow, tbody.firstChild);
        }
        
        // Limpiar el formulario
        document.getElementById('kilometraje').value = '';
        document.getElementById('fecha').value = '';
        document.getElementById('ubicacion').value = '';
        
        // Cerrar el modal
        closeAddKilometrajeModal();
        
        // Mostrar mensaje de éxito
        alert('Kilometraje registrado con éxito');
    }, 500);
}

// Cerrar modal con tecla Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDocumentModal();
        closeUploadDocumentModal();
        closeAddKilometrajeModal();
    }
});
</script>
@endpush
