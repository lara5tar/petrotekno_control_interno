@extends('layouts.app')

@section('title', 'Alertas')

@section('header', 'Alertas')

@section('content')
<div x-data="{ 
    filtroTipo: 'todos', 
    filtroEstado: 'todos',
    filtroVehiculo: '',
    limpiarFiltros() {
        this.filtroTipo = 'todos';
        this.filtroEstado = 'todos';
        this.filtroVehiculo = '';
    }
}">
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Alertas']
    ]" />

    <!-- Encabezado -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Alertas</h2>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-gray-100 rounded-md flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Alertas</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $estadisticas['total'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-md flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Vencidas</p>
                    <p class="text-2xl font-semibold text-red-600">{{ $estadisticas['vencidas'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Próximas</p>
                    <p class="text-2xl font-semibold text-yellow-600">{{ $estadisticas['proximas'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Mantenimiento</p>
                    <p class="text-2xl font-semibold text-blue-600">{{ $estadisticas['mantenimiento'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-md flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Documentos</p>
                    <p class="text-2xl font-semibold text-purple-600">{{ $estadisticas['documentos'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel de filtros -->
    <div class="bg-white rounded-lg shadow-md mb-6 p-6">
        <!-- Filtros adicionales -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end mb-6">            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                <select x-model="filtroEstado" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-petroyellow focus:ring-petroyellow">
                    <option value="todos">Todos los estados</option>
                    <option value="Vencido">Vencidas</option>
                    <option value="Próximo">Próximas</option>
                    <option value="Próximo a Vencer">Próximas a vencer</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Vehículo</label>
                <input type="text" x-model="filtroVehiculo" 
                       placeholder="Buscar por placa o nombre..." 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-petroyellow focus:ring-petroyellow">
            </div>
            
            <div>
                <button @click="limpiarFiltros()" 
                        class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md w-full transition duration-200">
                    Limpiar filtros
                </button>
            </div>
        </div>

        <!-- Filtros rápidos con chips -->
        <div class="mb-0">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-medium text-gray-700">Filtros Rápidos</h3>
                <div class="text-xs text-gray-500">
                    <span x-show="filtroTipo === 'todos'">Mostrando todas las alertas</span>
                    <span x-show="filtroTipo === 'mantenimiento'">Mostrando solo mantenimientos</span>
                    <span x-show="filtroTipo === 'documentos'">Mostrando solo documentos</span>
                </div>
            </div>
            
            <!-- Versión desktop -->
            <div class="hidden sm:flex flex-wrap gap-2" role="tablist" aria-label="Filtros de tipo de alerta">
                <!-- Chip Todos -->
                <button @click="filtroTipo = 'todos'" 
                        :class="filtroTipo === 'todos' ? 'bg-petroyellow text-white border-petroyellow shadow-md' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                        :aria-pressed="filtroTipo === 'todos'"
                        aria-label="Mostrar todas las alertas"
                        role="tab"
                        class="filter-chip inline-flex items-center px-4 py-2 border rounded-full text-sm font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-petroyellow focus:ring-offset-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                    </svg>
                    Todos
                    <span :class="filtroTipo === 'todos' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-600'" class="chip-counter ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold rounded-full" aria-label="Total de alertas">
                        {{ $estadisticas['total'] }}
                    </span>
                </button>

                <!-- Chip Mantenimientos -->
                <button @click="filtroTipo = 'mantenimiento'" 
                        :class="filtroTipo === 'mantenimiento' ? 'bg-red-500 text-white border-red-500 shadow-md' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                        :aria-pressed="filtroTipo === 'mantenimiento'"
                        aria-label="Mostrar solo alertas de mantenimiento"
                        role="tab"
                        class="filter-chip inline-flex items-center px-4 py-2 border rounded-full text-sm font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                    </svg>
                    Mantenimientos
                    <span :class="filtroTipo === 'mantenimiento' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-600'" class="chip-counter ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold rounded-full" aria-label="Alertas de mantenimiento">
                        {{ $estadisticas['mantenimiento'] }}
                    </span>
                </button>

                <!-- Chip Documentos -->
                <button @click="filtroTipo = 'documentos'" 
                        :class="filtroTipo === 'documentos' ? 'bg-purple-500 text-white border-purple-500 shadow-md' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                        :aria-pressed="filtroTipo === 'documentos'"
                        aria-label="Mostrar solo alertas de documentos"
                        role="tab"
                        class="filter-chip inline-flex items-center px-4 py-2 border rounded-full text-sm font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                    </svg>
                    Documentos
                    <span :class="filtroTipo === 'documentos' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-600'" class="chip-counter ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold rounded-full" aria-label="Alertas de documentos">
                        {{ $estadisticas['documentos'] }}
                    </span>
                </button>
            </div>

            <!-- Versión móvil más compacta -->
            <div class="sm:hidden grid grid-cols-3 gap-2">
                <!-- Chip Todos Móvil -->
                <button @click="filtroTipo = 'todos'" 
                        :class="filtroTipo === 'todos' ? 'bg-petroyellow text-white border-petroyellow shadow-md' : 'bg-white text-gray-700 border-gray-300'"
                        class="filter-chip flex flex-col items-center px-3 py-2 border rounded-lg text-xs font-medium transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mb-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                    </svg>
                    <span>Todos</span>
                    <span :class="filtroTipo === 'todos' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-600'" class="chip-counter mt-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold rounded-full">
                        {{ $estadisticas['total'] }}
                    </span>
                </button>

                <!-- Chip Mantenimientos Móvil -->
                <button @click="filtroTipo = 'mantenimiento'" 
                        :class="filtroTipo === 'mantenimiento' ? 'bg-red-500 text-white border-red-500 shadow-md' : 'bg-white text-gray-700 border-gray-300'"
                        class="filter-chip flex flex-col items-center px-3 py-2 border rounded-lg text-xs font-medium transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mb-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                    </svg>
                    <span>Mantenim.</span>
                    <span :class="filtroTipo === 'mantenimiento' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-600'" class="chip-counter mt-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold rounded-full">
                        {{ $estadisticas['mantenimiento'] }}
                    </span>
                </button>

                <!-- Chip Documentos Móvil -->
                <button @click="filtroTipo = 'documentos'" 
                        :class="filtroTipo === 'documentos' ? 'bg-purple-500 text-white border-purple-500 shadow-md' : 'bg-white text-gray-700 border-gray-300'"
                        class="filter-chip flex flex-col items-center px-3 py-2 border rounded-lg text-xs font-medium transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mb-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                    </svg>
                    <span>Documentos</span>
                    <span :class="filtroTipo === 'documentos' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-600'" class="chip-counter mt-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold rounded-full">
                        {{ $estadisticas['documentos'] }}
                    </span>
                </button>
            </div>
        </div>
    </div>
    <!-- Lista de alertas -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">
                Alertas activas
                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                    {{ count($alertasUnificadas) }}
                </span>
            </h3>
        </div>
        
        @if(count($alertasUnificadas) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 table-fixed">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="w-36 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tipo
                            </th>
                            <th scope="col" class="w-48 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Vehículo
                            </th>
                            <th scope="col" class="w-80 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Descripción
                            </th>
                            <th scope="col" class="w-32 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Vencimiento
                            </th>
                            <th scope="col" class="w-32 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th scope="col" class="w-36 px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($alertasUnificadas as $alerta)
                        <tr class="hover:bg-gray-50" 
                            x-show="(filtroTipo === 'todos' || filtroTipo === '{{ $alerta['tipo'] }}' || (filtroTipo === 'documentos' && '{{ $alerta['tipo'] }}' === 'documento')) && (filtroEstado === 'todos' || filtroEstado === '{{ $alerta['estado'] }}')"
                            x-transition>
                            <!-- Tipo de alerta -->
                            <td class="w-36 px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($alerta['tipo'] === 'mantenimiento')
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">Mantenimiento</span>
                                    @else
                                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-purple-600" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">Documentos</span>
                                    @endif
                                </div>
                            </td>
                            
                            <!-- Vehículo -->
                            <td class="w-48 px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $alerta['vehiculo_info']['marca'] }} {{ $alerta['vehiculo_info']['modelo'] }}</div>
                                <div class="text-sm text-gray-500">{{ $alerta['vehiculo_info']['placas'] }}</div>
                            </td>
                            
                            <!-- Descripción -->
                            <td class="w-80 px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $alerta['descripcion'] }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    @if($alerta['dias_restantes'] !== null)
                                        {{ $alerta['dias_restantes'] >= 0 ? $alerta['dias_restantes'] . ' días restantes' : abs($alerta['dias_restantes']) . ' días vencido' }}
                                    @endif
                                    @if($alerta['kilometros_restantes'] !== null)
                                        {{ $alerta['kilometros_restantes'] >= 0 ? ' • ' . number_format($alerta['kilometros_restantes']) . ' km restantes' : ' • ' . number_format(abs($alerta['kilometros_restantes'])) . ' km excedidos' }}
                                    @endif
                                </div>
                            </td>
                            
                            <!-- Fecha -->
                            <td class="w-32 px-6 py-4 whitespace-nowrap">
                                @if($alerta['fecha_limite'])
                                    <div class="text-sm text-gray-900">{{ $alerta['fecha_limite']->format('d/m/Y') }}</div>
                                @else
                                    <span class="text-sm text-gray-500">-</span>
                                @endif
                            </td>
                            
                            <!-- Estado -->
                            <td class="w-32 px-6 py-4 whitespace-nowrap">
                                @php
                                    $estadoClass = '';
                                    switch($alerta['estado']) {
                                        case 'Vencido':
                                            $estadoClass = 'bg-red-100 text-red-800';
                                            break;
                                        case 'Próximo':
                                        case 'Próximo a Vencer':
                                            $estadoClass = 'bg-yellow-100 text-yellow-800';
                                            break;
                                        default:
                                            $estadoClass = 'bg-gray-100 text-gray-800';
                                    }
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $estadoClass }}">
                                    {{ $alerta['estado'] }}
                                </span>
                            </td>
                            
                            <!-- Acciones -->
                            <td class="w-36 px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('vehiculos.show', $alerta['vehiculo_id']) }}" 
                                       class="text-petroyellow hover:text-petroyellow/80 font-medium">
                                        Ver vehículo
                                    </a>
                                    @if($alerta['tipo'] === 'mantenimiento')
                                        <a href="{{ route('mantenimientos.create', ['vehiculo_id' => $alerta['vehiculo_id']]) }}" 
                                           class="bg-petroyellow hover:bg-petroyellow/90 text-white px-3 py-1.5 rounded-md text-xs font-medium">
                                            Programar
                                        </a>
                                    @else
                                        <a href="{{ route('vehiculos.edit', $alerta['vehiculo_id']) }}" 
                                           class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1.5 rounded-md text-xs font-medium">
                                            Actualizar
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">¡Excelente! No hay alertas pendientes</h3>
                <p class="mt-1 text-sm text-gray-500">Todos los vehículos están al día con sus mantenimientos y documentos.</p>
            </div>
        @endif
    </div>
</div>

@endsection

@push('styles')
<style>
    /* Animaciones y mejoras para los chips de filtros */
    .filter-chip {
        transition: all 0.2s ease-in-out;
        transform: translateY(0);
    }
    
    .filter-chip:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .filter-chip:active {
        transform: translateY(0);
        transition: all 0.1s ease-in-out;
    }
    
    /* Animación del contador en los chips */
    .chip-counter {
        transition: all 0.3s ease-in-out;
    }
    
    /* Responsive design para los chips */
    @media (max-width: 640px) {
        .filter-chip {
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
        }
        
        .chip-counter {
            margin-left: 0.375rem;
            padding: 0.125rem 0.375rem;
        }
    }
</style>
@endpush
