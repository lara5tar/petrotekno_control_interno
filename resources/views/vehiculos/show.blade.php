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
                                {{ $vehiculo->anio ?? '2023' }}
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
                                {{ $vehiculo->n_serie ?? 'NS123456789' }}
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
                                    @if($vehiculo->obraActual)
                                        <button class="bg-blue-600 hover:bg-blue-700 text-white py-1 px-2 rounded-md transition-colors duration-200 flex items-center text-xs">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                            Cambiar Obra
                                        </button>
                                    @endif
                                </div>
                                
                                @if($vehiculo->obraActual)
                                    <div class="space-y-2">
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-sm text-gray-600">Nombre de Obra</label>
                                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                    {{ $vehiculo->obraActual->nombre_obra }}
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-600">Estatus</label>
                                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                    {{ ucfirst(str_replace('_', ' ', $vehiculo->obraActual->estatus)) }}
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-sm text-gray-600">Fecha Asignación</label>
                                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                    {{ $vehiculo->obraActual->fecha_asignacion ? $vehiculo->obraActual->fecha_asignacion->format('d/m/Y') : 'Sin fecha' }}
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-600">Avance</label>
                                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                    {{ $vehiculo->obraActual->avance ?? 0 }}%
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-sm text-gray-600">Km. Inicial</label>
                                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                    {{ $vehiculo->obraActual->kilometraje_inicial ? number_format($vehiculo->obraActual->kilometraje_inicial) : 'Sin registrar' }}
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-600">Días Activa</label>
                                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                    {{ $vehiculo->obraActual->fecha_asignacion ? $vehiculo->obraActual->fecha_asignacion->diffInDays(now()) : 0 }} días
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm text-gray-600">Responsable de Obra</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                {{ $vehiculo->obraActual->encargado->personal->nombre_completo ?? 'Sin asignar' }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <p class="text-gray-500 text-sm mb-2">Sin obra asignada</p>
                                        <p class="text-gray-400 text-xs">El vehículo está disponible para asignación</p>
                                        <button class="mt-3 bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-md transition-colors duration-200 flex items-center mx-auto text-sm">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            Asignar a Obra
                                        </button>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Sección: Operador Actual -->
                            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-center mb-4">
                                    <h5 class="text-base font-semibold text-gray-800 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                        </svg>
                                        Operador Actual
                                    </h5>
                                    <button class="bg-blue-600 hover:bg-blue-700 text-white py-1 px-2 rounded-md transition-colors duration-200 flex items-center text-xs">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Cambiar Personal
                                    </button>
                                </div>
                                
                                <div class="space-y-3">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600">Nombre Completo</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                {{ $vehiculo->operadorActual->nombre_completo ?? 'Sin asignar' }}
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600">NSS</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                {{ $vehiculo->operadorActual->nss ?? 'Sin asignar' }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600">RFC</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                MAAL850615ABC
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600">INE</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                MAALRC85061512H100
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600">Domicilio</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                Av. Revolución #123, Col. Centro, Monterrey, N.L. C.P. 64000
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 gap-3">
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
                                            @if($vehiculo->kilometrajes && $vehiculo->kilometrajes->count() > 0)
                                                @foreach($vehiculo->kilometrajes->take(5) as $kilometraje)
                                                <tr>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">{{ number_format($kilometraje->kilometraje) }}</td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ $kilometraje->fecha_captura?->format('d/m/Y') ?? 'Sin fecha' }}</td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ $kilometraje->observaciones ?? 'No especificada' }}</td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ $kilometraje->usuarioCaptura->nombre_completo ?? 'No asignado' }}</td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                        <button class="text-blue-600 hover:text-blue-900 transition-colors duration-200" title="Ver detalles">
                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5" class="px-3 py-4 text-center text-sm text-gray-500">
                                                        No hay registros de kilometraje disponibles
                                                    </td>
                                                </tr>
                                            @endif
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
                                                <th scope="col" class="px-3 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Proveedor</th>
                                                <th scope="col" class="px-3 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                                <th scope="col" class="px-3 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">15/03/2024</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">Preventivo</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">125,000 km</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">Proveedor Mecánico Central</td>
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
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">Proveedor Mecánico Central</td>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modales -->
<!-- Modal para agregar kilometraje -->
<div id="add-kilometraje-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="bg-black bg-opacity-50 absolute inset-0"></div>
    <div class="bg-white rounded-lg shadow-lg overflow-hidden w-full max-w-md z-10">
        <div class="px-4 py-3 border-b">
            <h3 class="text-lg font-semibold text-gray-800" id="modal-title">Agregar Kilometraje</h3>
        </div>
        <div class="p-4">
            <form id="add-kilometraje-form">
                @csrf
                <div class="mb-4">
                    <label for="kilometraje" class="block text-sm font-medium text-gray-700">Kilometraje</label>
                    <input type="number" name="kilometraje" id="kilometraje" required
                           class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-4">
                    <label for="fecha_captura" class="block text-sm font-medium text-gray-700">Fecha</label>
                    <input type="date" name="fecha_captura" id="fecha_captura" required
                           class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-4">
                    <label for="ubicacion" class="block text-sm font-medium text-gray-700">Ubicación</label>
                    <input type="text" name="ubicacion" id="ubicacion" required
                           class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-4">
                    <label for="observaciones" class="block text-sm font-medium text-gray-700">Observaciones</label>
                    <textarea name="observaciones" id="observaciones" rows="3"
                              class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal('add-kilometraje-modal')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors duration-200">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors duration-200">
                        Guardar Kilometraje
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para subir documentos -->
<div id="upload-document-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="bg-black bg-opacity-50 absolute inset-0"></div>
    <div class="bg-white rounded-lg shadow-lg overflow-hidden w-full max-w-md z-10">
        <div class="px-4 py-3 border-b">
            <h3 class="text-lg font-semibold text-gray-800" id="modal-title">Subir Documento</h3>
        </div>
        <div class="p-4">
            <form id="upload-document-form">
                @csrf
                <div class="mb-4">
                    <label for="documento" class="block text-sm font-medium text-gray-700">Seleccionar Documento</label>
                    <input type="file" name="documento" id="documento" accept=".pdf,.jpg,.jpeg,.png" required
                           class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-4">
                    <label for="tipo_documento" class="block text-sm font-medium text-gray-700">Tipo de Documento</label>
                    <select name="tipo_documento" id="tipo_documento" required
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Seleccione un tipo</option>
                        <option value="tarjeta_circulacion">Tarjeta de Circulación</option>
                        <option value="poliza_seguro">Póliza de Seguro</option>
                        <option value="verificacion_vehicular">Verificación Vehicular</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal('upload-document-modal')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors duration-200">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors duration-200">
                        Subir Documento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Función para mostrar el modal de agregar kilometraje
    function showAddKilometrajeModal() {
        document.getElementById('add-kilometraje-modal').classList.remove('hidden');
    }

    // Función para cerrar modales
    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    // Manejar el envío del formulario de agregar kilometraje
    document.getElementById('add-kilometraje-form').addEventListener('submit', function(e) {
        e.preventDefault();
        // Aquí puedes agregar la lógica para guardar el kilometraje, como una llamada AJAX
        // Por ahora, solo cerraremos el modal y mostraremos un mensaje
        closeModal('add-kilometraje-modal');
        alert('Kilometraje agregado exitosamente');
    });

    // Manejar el envío del formulario de subir documento
    document.getElementById('upload-document-form').addEventListener('submit', function(e) {
        e.preventDefault();
        // Aquí puedes agregar la lógica para subir el documento, como una llamada AJAX
        // Por ahora, solo cerraremos el modal y mostraremos un mensaje
        closeModal('upload-document-modal');
        alert('Documento subido exitosamente');
    });
</script>
@endsection
