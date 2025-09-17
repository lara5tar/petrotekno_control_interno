@extends('layouts.app')

@section('title', 'Reportes')

@section('header', 'Sistema de Reportes')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Reportes']
    ]" />

    <!-- Encabezado -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900">Reportes del Sistema</h2>
        <p class="text-gray-600 mt-2">Genere reportes detallados del sistema de control interno</p>
        <p class="text-sm text-gray-500 mt-1">Última actualización: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <!-- SECCIÓN 1: REPORTES PRINCIPALES -->
    <div class="mb-10">
        <div class="mb-4 flex items-center">
            <div class="bg-gray-100 p-2 rounded-lg mr-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 001 1h6a1 1 0 001-1V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-800">Reportes Principales</h3>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="divide-y divide-gray-200">
                
                <!-- Inventario de Activos -->
                <div class="p-6 hover:bg-gray-50 transition duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center flex-1">
                            <div class="bg-gray-100 p-3 rounded-lg mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-900">Inventario General</h4>
                                <p class="text-sm text-gray-500 mt-1">Reporte completo del inventario vehicular con último kilometraje registrado</p>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        Filtros por estatus
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        Exportable
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('reportes.inventario-vehiculos') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                </svg>
                                Ver Reporte
                            </a>
                            <a href="{{ route('reportes.inventario-vehiculos', ['formato' => 'pdf']) }}" class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded-md transition duration-200 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                Descargar PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 2: REPORTES POR ESTADO DE ACTIVOS -->
    <div class="mb-10">
        <div class="mb-4 flex items-center">
            <div class="bg-gray-100 p-2 rounded-lg mr-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-800">Reportes por Estado de Activos</h3>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6">
                <p class="text-gray-600 mb-6">Acceso directo a reportes específicos por estado de los activos</p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                    
                    {{-- Activos Disponibles --}}
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 hover:bg-gray-100 transition duration-200">
                        <div class="flex items-center mb-3">
                            <div class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900">Disponibles</h4>
                                <p class="text-xs text-gray-500">Listos para asignar</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <a href="{{ route('reportes.vehiculos-disponibles') }}" class="w-full bg-white hover:bg-gray-50 text-gray-700 font-medium py-2 px-3 rounded text-sm transition duration-200 flex items-center justify-center border">
                                Ver
                            </a>
                            <a href="{{ route('reportes.vehiculos-disponibles', ['formato' => 'pdf']) }}" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-3 rounded text-sm transition duration-200 flex items-center justify-center">
                                PDF
                            </a>
                        </div>
                    </div>

                    {{-- Activos Asignados --}}
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 hover:bg-gray-100 transition duration-200">
                        <div class="flex items-center mb-3">
                            <div class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900">Asignados</h4>
                                <p class="text-xs text-gray-500">En uso en obras</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <a href="{{ route('reportes.vehiculos-asignados') }}" class="w-full bg-white hover:bg-gray-50 text-gray-700 font-medium py-2 px-3 rounded text-sm transition duration-200 flex items-center justify-center border">
                                Ver
                            </a>
                            <a href="{{ route('reportes.vehiculos-asignados', ['formato' => 'pdf']) }}" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-3 rounded text-sm transition duration-200 flex items-center justify-center">
                                PDF
                            </a>
                        </div>
                    </div>

                    {{-- Activos en Mantenimiento --}}
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 hover:bg-gray-100 transition duration-200">
                        <div class="flex items-center mb-3">
                            <div class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900">Mantenimiento</h4>
                                <p class="text-xs text-gray-500">En taller</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <a href="{{ route('reportes.vehiculos-mantenimiento') }}" class="w-full bg-white hover:bg-gray-50 text-gray-700 font-medium py-2 px-3 rounded text-sm transition duration-200 flex items-center justify-center border">
                                Ver
                            </a>
                            <a href="{{ route('reportes.vehiculos-mantenimiento', ['formato' => 'pdf']) }}" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-3 rounded text-sm transition duration-200 flex items-center justify-center">
                                PDF
                            </a>
                        </div>
                    </div>

                    {{-- Activos Fuera de Servicio --}}
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 hover:bg-gray-100 transition duration-200">
                        <div class="flex items-center mb-3">
                            <div class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900">Fuera de Servicio</h4>
                                <p class="text-xs text-gray-500">No operativo</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <a href="{{ route('reportes.vehiculos-fuera-servicio') }}" class="w-full bg-white hover:bg-gray-50 text-gray-700 font-medium py-2 px-3 rounded text-sm transition duration-200 flex items-center justify-center border">
                                Ver
                            </a>
                            <a href="{{ route('reportes.vehiculos-fuera-servicio', ['formato' => 'pdf']) }}" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-3 rounded text-sm transition duration-200 flex items-center justify-center">
                                PDF
                            </a>
                        </div>
                    </div>

                    {{-- Activos Dados de Baja --}}
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 hover:bg-gray-100 transition duration-200">
                        <div class="flex items-center mb-3">
                            <div class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900">Baja</h4>
                                <p class="text-xs text-gray-500">Dados de baja</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <a href="{{ route('reportes.vehiculos-baja') }}" class="w-full bg-white hover:bg-gray-50 text-gray-700 font-medium py-2 px-3 rounded text-sm transition duration-200 flex items-center justify-center border">
                                Ver
                            </a>
                            <a href="{{ route('reportes.vehiculos-baja', ['formato' => 'pdf']) }}" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-3 rounded text-sm transition duration-200 flex items-center justify-center">
                                PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 3: OTROS REPORTES -->
    <div class="mb-10">
        <div class="mb-4 flex items-center">
            <div class="bg-gray-100 p-2 rounded-lg mr-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-800">Otros Reportes</h3>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6">
                <p class="text-gray-600 mb-6">Reportes adicionales del sistema (próximamente)</p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    {{-- Kilometrajes --}}
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 opacity-75">
                        <div class="flex items-center mb-3">
                            <div class="bg-gray-200 p-3 rounded-lg mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-900">Reporte de Kilometrajes</h4>
                                <p class="text-sm text-gray-500 mt-1">Análisis detallado de kilometrajes por activo, obras y períodos específicos</p>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 mt-2">
                                    Próximamente
                                </span>
                            </div>
                        </div>
                        <button disabled class="w-full bg-gray-300 text-gray-500 font-medium py-2 px-4 rounded-md cursor-not-allowed">
                            En desarrollo
                        </button>
                    </div>

                    {{-- Mantenimientos --}}
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 opacity-75">
                        <div class="flex items-center mb-3">
                            <div class="bg-gray-200 p-3 rounded-lg mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-900">Reporte de Mantenimientos</h4>
                                <p class="text-sm text-gray-500 mt-1">Historial de mantenimientos, costos, proveedores y análisis preventivo vs correctivo</p>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 mt-2">
                                    Próximamente
                                </span>
                            </div>
                        </div>
                        <button disabled class="w-full bg-gray-300 text-gray-500 font-medium py-2 px-4 rounded-md cursor-not-allowed">
                            En desarrollo
                        </button>
                    </div>

                    {{-- Obras --}}
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 opacity-75">
                        <div class="flex items-center mb-3">
                            <div class="bg-gray-200 p-3 rounded-lg mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-900">Reporte de Obras</h4>
                                <p class="text-sm text-gray-500 mt-1">Estado de obras, asignación de recursos, avances y análisis de productividad</p>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 mt-2">
                                    Próximamente
                                </span>
                            </div>
                        </div>
                        <button disabled class="w-full bg-gray-300 text-gray-500 font-medium py-2 px-4 rounded-md cursor-not-allowed">
                            En desarrollo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- INFORMACIÓN ADICIONAL -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <div class="mb-4 flex items-center">
                <div class="bg-gray-100 p-2 rounded-lg mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Información de Reportes</h3>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-base font-semibold text-gray-900 mb-3">Formatos de Exportación</h4>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li><span class="font-medium text-gray-900">Excel/CSV:</span> Para análisis avanzado en hojas de cálculo</li>
                        <li><span class="font-medium text-gray-900">PDF:</span> Para impresión y presentaciones formales</li>
                        <li><span class="font-medium text-gray-900">HTML:</span> Para visualización interactiva en pantalla</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-base font-semibold text-gray-900 mb-3">Filtros Disponibles</h4>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li><span class="font-medium text-gray-900">Estatus:</span> Filtre por estado de activos, obras, etc.</li>
                        <li><span class="font-medium text-gray-900">Fechas:</span> Seleccione rangos de tiempo específicos</li>
                        <li><span class="font-medium text-gray-900">Categorías:</span> Agrupe por marca, tipo, departamento, etc.</li>
                    </ul>
                </div>
            </div>
            
            <div class="mt-6">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-gray-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h5 class="font-medium text-gray-900 mb-1">Consejo</h5>
                            <p class="text-sm text-gray-600">
                                Los reportes se actualizan en tiempo real. Para obtener los datos más recientes, 
                                asegúrese de que toda la información esté actualizada en el sistema antes de generar el reporte.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
