@extends('layouts.app')

@section('title', 'Reportes')

@section('header', 'Sistema de Reportes')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

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

    <!-- SECCIÓN: REPORTES DE VEHÍCULOS -->
    <div class="mb-10">
        <div class="mb-4 flex items-center">
            <div class="bg-gray-100 p-2 rounded-lg mr-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-800">Reportes de Vehículos</h3>
                <p class="text-sm text-gray-500 mt-1">Inventario general y reportes por estado de vehículos</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="divide-y divide-gray-200">
                
                <!-- Inventario General de Vehículos -->
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
                            <a href="{{ route('reportes.inventario-vehiculos', ['formato' => 'excel']) }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                Descargar Excel
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



                <!-- Vehículos Disponibles -->
                <div class="p-6 hover:bg-gray-50 transition duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center flex-1">
                            <div class="bg-gray-100 p-3 rounded-lg mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-900">Vehículos Disponibles</h4>
                                <p class="text-sm text-gray-500 mt-1">Listado de vehículos listos para asignación a obras</p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('reportes.vehiculos-disponibles', ['formato' => 'excel']) }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                Descargar Excel
                            </a>
                            <a href="{{ route('reportes.vehiculos-disponibles', ['formato' => 'pdf']) }}" class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded-md transition duration-200 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                Descargar PDF
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Vehículos Asignados -->
                <div class="p-6 hover:bg-gray-50 transition duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center flex-1">
                            <div class="bg-gray-100 p-3 rounded-lg mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-900">Vehículos Asignados</h4>
                                <p class="text-sm text-gray-500 mt-1">Vehículos actualmente en uso en obras específicas</p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('reportes.vehiculos-asignados', ['formato' => 'excel']) }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                Descargar Excel
                            </a>
                            <a href="{{ route('reportes.vehiculos-asignados', ['formato' => 'pdf']) }}" class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded-md transition duration-200 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                Descargar PDF
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Vehículos en Mantenimiento -->
                <div class="p-6 hover:bg-gray-50 transition duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center flex-1">
                            <div class="bg-gray-100 p-3 rounded-lg mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-900">Vehículos en Mantenimiento</h4>
                                <p class="text-sm text-gray-500 mt-1">Vehículos actualmente en talleres para reparación o mantenimiento</p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('reportes.vehiculos-mantenimiento', ['formato' => 'excel']) }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                Descargar Excel
                            </a>
                            <a href="{{ route('reportes.vehiculos-mantenimiento', ['formato' => 'pdf']) }}" class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded-md transition duration-200 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                Descargar PDF
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Vehículos Fuera de Servicio -->
                <div class="p-6 hover:bg-gray-50 transition duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center flex-1">
                            <div class="bg-gray-100 p-3 rounded-lg mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-900">Vehículos Fuera de Servicio</h4>
                                <p class="text-sm text-gray-500 mt-1">Vehículos temporalmente no operativos por diversas causas</p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('reportes.vehiculos-fuera-servicio', ['formato' => 'excel']) }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                Descargar Excel
                            </a>
                            <a href="{{ route('reportes.vehiculos-fuera-servicio', ['formato' => 'pdf']) }}" class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded-md transition duration-200 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                Descargar PDF
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Vehículos Dados de Baja -->
                <div class="p-6 hover:bg-gray-50 transition duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center flex-1">
                            <div class="bg-gray-100 p-3 rounded-lg mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-900">Vehículos Dados de Baja</h4>
                                <p class="text-sm text-gray-500 mt-1">Historial de vehículos retirados definitivamente del inventario</p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('reportes.vehiculos-baja', ['formato' => 'excel']) }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                Descargar Excel
                            </a>
                            <a href="{{ route('reportes.vehiculos-baja', ['formato' => 'pdf']) }}" class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded-md transition duration-200 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                Descargar PDF
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Reporte de Kilometrajes -->
                <div class="p-6 hover:bg-gray-50 transition duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center flex-1">
                            <div class="bg-gray-100 p-3 rounded-lg mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00-2-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-900">Reporte de Kilometrajes</h4>
                                <p class="text-sm text-gray-500 mt-1">Análisis detallado de kilometrajes por vehículo, obras y períodos específicos con alertas de mantenimiento</p>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        Análisis detallado
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        Alertas de mantenimiento
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        Filtros por fecha/obra
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('reportes.kilometrajes', ['formato' => 'excel']) }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                Descargar Excel
                            </a>
                            <a href="{{ route('reportes.kilometrajes', ['formato' => 'pdf']) }}" class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded-md transition duration-200 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                Descargar PDF
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Reporte de Mantenimientos Pendientes -->
                <div class="p-6 hover:bg-gray-50 transition duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center flex-1">
                            <div class="bg-gray-100 p-3 rounded-lg mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-900">Mantenimientos Pendientes</h4>
                                <p class="text-sm text-gray-500 mt-1">Listado de mantenimientos que aún no han sido completados, con alertas de tiempo y costos estimados</p>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        Correctivos y Preventivos
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        Alertas de tiempo
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        Filtros avanzados
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('reportes.mantenimientos-pendientes', ['formato' => 'excel']) }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                Descargar Excel
                            </a>
                            <a href="{{ route('reportes.mantenimientos-pendientes', ['formato' => 'pdf']) }}" class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded-md transition duration-200 flex items-center">
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

    <!-- SECCIÓN: HISTORIAL -->
    <div class="mb-10">
        <div class="mb-4 flex items-center">
            <div class="bg-gray-100 p-2 rounded-lg mr-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-800">Historial</h3>
                <p class="text-sm text-gray-500 mt-1">Reportes históricos y seguimiento temporal de operaciones</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="divide-y divide-gray-200">
                
                <!-- Historial de Obras por Vehículo -->
                <div class="p-6 hover:bg-gray-50 transition duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center flex-1">
                            <div class="bg-gray-100 p-3 rounded-lg mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-900">Historial de Obras por Vehículo</h4>
                                <p class="text-sm text-gray-500 mt-1">Reporte individual del historial completo de asignaciones por vehículo específico, incluyendo fechas, kilometrajes y estados</p>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        Por vehículo individual
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        Filtros por fecha/estado
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        PDF específico
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <!-- Dropdown para Excel por Vehículo -->
                            <div class="relative inline-block text-left">
                                <button type="button" id="excel-dropdown-button-obras" 
                                        class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 flex items-center focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                    Descargar Excel
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div id="excel-dropdown-menu-obras" class="hidden absolute right-0 mt-2 w-72 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                    <div class="py-1" role="menu">
                                        <div class="px-4 py-3 border-b border-gray-200">
                                            <h4 class="text-sm font-medium text-gray-900">Seleccionar Vehículo para Excel</h4>
                                            <p class="text-xs text-gray-500 mt-1">Genere un Excel individual con el historial completo de obras de un vehículo específico</p>
                                        </div>
                                        
                                        <div class="px-4 py-3">
                                            <label for="vehiculo-excel-select-obras" class="block text-xs font-medium text-gray-700 mb-2">
                                                Vehículo:
                                            </label>
                                            <select id="vehiculo-excel-select-obras" 
                                                    class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                                <option value="">Seleccionar vehículo...</option>
                                                @foreach($vehiculosDisponibles as $vehiculo)
                                                    <option value="{{ $vehiculo->id }}">{{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->anio }}) - {{ $vehiculo->placas }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 flex justify-end space-x-2">
                                            <button type="button" onclick="cerrarDropdownExcelObras()" 
                                                    class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500">
                                                Cancelar
                                            </button>
                                            <button type="button" onclick="generarExcelObrasVehiculo()" 
                                                    class="px-3 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                                Generar Excel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Dropdown para PDF por Vehículo -->
                            <div class="relative inline-block text-left">
                                <button type="button" id="pdf-dropdown-button-main" 
                                        class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded-md transition duration-200 flex items-center focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                    Descargar PDF
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div id="pdf-dropdown-menu-main" class="hidden absolute right-0 mt-2 w-72 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                    <div class="py-1" role="menu">
                                        <div class="px-4 py-3 border-b border-gray-200">
                                            <h4 class="text-sm font-medium text-gray-900">Seleccionar Vehículo para PDF</h4>
                                            <p class="text-xs text-gray-500 mt-1">Genere un PDF individual con el historial completo de un vehículo específico</p>
                                        </div>
                                        
                                        <div class="px-4 py-3">
                                            <label for="vehiculo-pdf-select-main" class="block text-xs font-medium text-gray-700 mb-2">
                                                Vehículo:
                                            </label>
                                            <select id="vehiculo-pdf-select-main" class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                                <option value="">Seleccionar vehículo...</option>
                                                @foreach($vehiculosDisponibles as $vehiculo)
                                                    <option value="{{ $vehiculo->id }}">
                                                        {{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->anio }}) - {{ $vehiculo->placas }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            
                                            <button onclick="descargarPDFVehiculoMain()" 
                                                    class="w-full mt-3 bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium text-sm py-2 px-3 rounded-md transition duration-200 flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                                Generar PDF del Vehículo
                                            </button>
                                        </div>
                                        
                                        <div class="px-4 py-2 bg-gray-50 border-t border-gray-200">
                                            <p class="text-xs text-gray-500">
                                                💡 El PDF incluirá todo el historial de obras y asignaciones del vehículo seleccionado
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historial de Obras por Operador -->
                <div class="p-6 hover:bg-gray-50 transition duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center flex-1">
                            <div class="bg-gray-100 p-3 rounded-lg mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-900">Historial de Obras por Operador</h4>
                                <p class="text-sm text-gray-500 mt-1">Seguimiento completo de asignaciones y movimientos de operadores en vehículos, incluyendo cambios y transferencias</p>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        Por operador individual
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        Filtros por fecha/obra
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        Historial completo
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <!-- Dropdown para Excel por Operador -->
                            <div class="relative inline-block text-left">
                                <button type="button" id="excel-dropdown-button-operador" 
                                        class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 flex items-center focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                    Descargar Excel
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div id="excel-dropdown-menu-operador" class="hidden absolute right-0 mt-2 w-72 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                    <div class="py-1" role="menu">
                                        <div class="px-4 py-3 border-b border-gray-200">
                                            <h4 class="text-sm font-medium text-gray-900">Seleccionar Operador para Excel</h4>
                                            <p class="text-xs text-gray-500 mt-1">Genere un Excel individual con el historial completo de obras de un operador específico</p>
                                        </div>
                                        
                                        <div class="px-4 py-3">
                                            <label for="operador-excel-select" class="block text-xs font-medium text-gray-700 mb-2">
                                                Operador:
                                            </label>
                                            <select id="operador-excel-select" 
                                                    class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                                <option value="">Seleccionar operador...</option>
                                                @foreach($operadoresDisponibles as $operador)
                                                    <option value="{{ $operador->id }}">{{ $operador->nombre_completo }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 flex justify-end space-x-2">
                                            <button type="button" onclick="cerrarDropdownExcelOperador()" 
                                                    class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500">
                                                Cancelar
                                            </button>
                                            <button type="button" onclick="generarExcelOperador()" 
                                                    class="px-3 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                                Generar Excel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Dropdown para PDF por Operador -->
                            <div class="relative inline-block text-left">
                                <button type="button" id="pdf-dropdown-button-operador" 
                                        class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded-md transition duration-200 flex items-center focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                    Descargar PDF
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div id="pdf-dropdown-menu-operador" class="hidden absolute right-0 mt-2 w-72 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                    <div class="py-1" role="menu">
                                        <div class="px-4 py-3 border-b border-gray-200">
                                            <h4 class="text-sm font-medium text-gray-900">Seleccionar Operador para PDF</h4>
                                            <p class="text-xs text-gray-500 mt-1">Genere un PDF individual con el historial completo de un operador específico</p>
                                        </div>
                                        
                                        <div class="px-4 py-3">
                                            <label for="operador-pdf-select-main" class="block text-xs font-medium text-gray-700 mb-2">
                                                Operador:
                                            </label>
                                            <select id="operador-pdf-select-main" class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                                <option value="">Seleccionar operador...</option>
                                                @foreach($operadoresDisponibles as $operador)
                                                    <option value="{{ $operador->id }}">
                                                        {{ $operador->nombre_completo }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            
                                            <button onclick="descargarPDFOperadorMain()" 
                                                    class="w-full mt-3 bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium text-sm py-2 px-3 rounded-md transition duration-200 flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                                Generar PDF del Operador
                                            </button>
                                        </div>
                                        
                                        <div class="px-4 py-2 bg-gray-50 border-t border-gray-200">
                                            <p class="text-xs text-gray-500">
                                                💡 El PDF incluirá todo el historial de movimientos y asignaciones del operador seleccionado
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historial de Mantenimientos por Vehículo -->
                <div class="p-6 hover:bg-gray-50 transition duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center flex-1">
                            <div class="bg-gray-100 p-3 rounded-lg mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-900">Historial de Mantenimientos por Vehículo</h4>
                                <p class="text-sm text-gray-500 mt-1">Reporte completo del historial de mantenimientos por vehículo específico, incluyendo fechas, tipos de mantenimiento, costos y estado</p>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        Por vehículo individual
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        Filtros por fecha/tipo
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        Historial completo
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <!-- Dropdown para Excel por Vehículo -->
                            <div class="relative inline-block text-left">
                                <button type="button" id="excel-dropdown-button-mantenimientos" 
                                        class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 flex items-center focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                    Descargar Excel
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div id="excel-dropdown-menu-mantenimientos" class="hidden absolute right-0 mt-2 w-72 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                    <div class="py-1" role="menu">
                                        <div class="px-4 py-3 border-b border-gray-200">
                                            <h4 class="text-sm font-medium text-gray-900">Seleccionar Vehículo para Excel</h4>
                                            <p class="text-xs text-gray-500 mt-1">Genere un Excel individual con el historial completo de mantenimientos de un vehículo específico</p>
                                        </div>
                                        
                                        <div class="px-4 py-3">
                                            <label for="vehiculo-excel-select-mantenimientos" class="block text-xs font-medium text-gray-700 mb-2">
                                                Vehículo:
                                            </label>
                                            <select id="vehiculo-excel-select-mantenimientos" 
                                                    class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                                <option value="">Seleccionar vehículo...</option>
                                                @foreach($vehiculosDisponibles as $vehiculo)
                                                    <option value="{{ $vehiculo->id }}">{{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->anio }}) - {{ $vehiculo->placas }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 flex justify-end space-x-2">
                                            <button type="button" onclick="cerrarDropdownExcelMantenimientos()" 
                                                    class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500">
                                                Cancelar
                                            </button>
                                            <button type="button" onclick="generarExcelMantenimientosVehiculo()" 
                                                    class="px-3 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                                Generar Excel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Dropdown para PDF por Vehículo -->
                            <div class="relative inline-block text-left">
                                <button type="button" id="pdf-dropdown-button-vehiculo" 
                                        class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded-md transition duration-200 flex items-center focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                    Descargar PDF
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div id="pdf-dropdown-menu-vehiculo" class="hidden absolute right-0 mt-2 w-72 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                    <div class="py-1" role="menu">
                                        <div class="px-4 py-3 border-b border-gray-200">
                                            <h4 class="text-sm font-medium text-gray-900">Seleccionar Vehículo para PDF</h4>
                                            <p class="text-xs text-gray-500 mt-1">Genere un PDF individual con el historial completo de mantenimientos de un vehículo específico</p>
                                        </div>
                                        <div class="px-4 py-3">
                                            <label for="vehiculo-select-dropdown" class="block text-xs font-medium text-gray-700 mb-1">Vehículo:</label>
                                            <select id="vehiculo-select-dropdown" 
                                                    class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                                <option value="">Seleccionar vehículo...</option>
                                                @foreach($vehiculosDisponibles as $vehiculo)
                                                    <option value="{{ $vehiculo->id }}">{{ $vehiculo->marca }} {{ $vehiculo->modelo }} - {{ $vehiculo->placas }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 flex justify-end space-x-2">
                                            <button type="button" onclick="cerrarDropdownVehiculo()" 
                                                    class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                                                Cancelar
                                            </button>
                                            <button type="button" onclick="generarPDFVehiculo()" 
                                                    class="px-3 py-2 text-sm font-medium text-petrodark bg-petroyellow border border-transparent rounded-md hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                                                Generar PDF
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


</div>

<script>
// Manejo del dropdown de PDF en la vista principal de reportes
document.addEventListener('DOMContentLoaded', function() {
    // Dropdown PDF principal
    const dropdownButton = document.getElementById('pdf-dropdown-button-main');
    const dropdownMenu = document.getElementById('pdf-dropdown-menu-main');
    
    if (dropdownButton && dropdownMenu) {
        dropdownButton.addEventListener('click', function() {
            dropdownMenu.classList.toggle('hidden');
        });
        
        // Cerrar dropdown al hacer clic fuera
        document.addEventListener('click', function(event) {
            if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
                dropdownMenu.classList.add('hidden');
            }
        });
    }

    // Dropdown Excel Obras
    const excelButtonObras = document.getElementById('excel-dropdown-button-obras');
    const excelMenuObras = document.getElementById('excel-dropdown-menu-obras');
    
    if (excelButtonObras && excelMenuObras) {
        excelButtonObras.addEventListener('click', function() {
            excelMenuObras.classList.toggle('hidden');
        });
        
        document.addEventListener('click', function(event) {
            if (!excelButtonObras.contains(event.target) && !excelMenuObras.contains(event.target)) {
                excelMenuObras.classList.add('hidden');
            }
        });
    }

    // Dropdown Excel Mantenimientos
    const excelButtonMantenimientos = document.getElementById('excel-dropdown-button-mantenimientos');
    const excelMenuMantenimientos = document.getElementById('excel-dropdown-menu-mantenimientos');
    
    if (excelButtonMantenimientos && excelMenuMantenimientos) {
        excelButtonMantenimientos.addEventListener('click', function() {
            excelMenuMantenimientos.classList.toggle('hidden');
        });
        
        document.addEventListener('click', function(event) {
            if (!excelButtonMantenimientos.contains(event.target) && !excelMenuMantenimientos.contains(event.target)) {
                excelMenuMantenimientos.classList.add('hidden');
            }
        });
    }

    // Dropdown Excel Operador
    const excelButtonOperador = document.getElementById('excel-dropdown-button-operador');
    const excelMenuOperador = document.getElementById('excel-dropdown-menu-operador');
    
    if (excelButtonOperador && excelMenuOperador) {
        excelButtonOperador.addEventListener('click', function() {
            excelMenuOperador.classList.toggle('hidden');
        });
        
        document.addEventListener('click', function(event) {
            if (!excelButtonOperador.contains(event.target) && !excelMenuOperador.contains(event.target)) {
                excelMenuOperador.classList.add('hidden');
            }
        });
    }
});

function descargarPDFVehiculoMain() {
    const vehiculoSelect = document.getElementById('vehiculo-pdf-select-main');
    const vehiculoId = vehiculoSelect.value;
    
    if (!vehiculoId) {
        // Mostrar alerta moderna si está disponible SweetAlert2
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Seleccionar Vehículo',
                text: 'Por favor seleccione un vehículo para generar el PDF con su historial completo.',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#f59e0b'
            });
        } else {
            alert('Por favor seleccione un vehículo para generar el PDF.');
        }
        return;
    }
    
    // Obtener nombre del vehículo para el archivo
    const vehiculoOption = vehiculoSelect.options[vehiculoSelect.selectedIndex];
    const vehiculoNombre = vehiculoOption.text;
    
    // Crear URL para la descarga del PDF
    const url = new URL('{{ route("reportes.historial-obras-vehiculo") }}', window.location.origin);
    url.searchParams.set('formato', 'pdf');
    url.searchParams.set('vehiculo_id', vehiculoId);
    
    // Cerrar dropdown
    document.getElementById('pdf-dropdown-menu-main').classList.add('hidden');
    
    // Mostrar notificación de descarga
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'Generando PDF',
            text: `Descargando historial de: ${vehiculoNombre}`,
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }
    
    // Abrir PDF en nueva ventana
    window.open(url.toString(), '_blank');
    
    // Resetear selector
    vehiculoSelect.value = '';
}

function descargarPDFOperadorMain() {
    const operadorSelect = document.getElementById('operador-pdf-select-main');
    const operadorId = operadorSelect.value;
    
    if (!operadorId) {
        // Mostrar alerta moderna si está disponible SweetAlert2
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Seleccionar Operador',
                text: 'Por favor seleccione un operador para generar el PDF con su historial completo.',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#f59e0b'
            });
        } else {
            alert('Por favor seleccione un operador para generar el PDF.');
        }
        return;
    }
    
    // Obtener nombre del operador para el archivo
    const operadorOption = operadorSelect.options[operadorSelect.selectedIndex];
    const operadorNombre = operadorOption.text;
    
    // Crear URL para la descarga del PDF
    const url = new URL('{{ route("reportes.historial-obras-operador") }}', window.location.origin);
    url.searchParams.set('formato', 'pdf');
    url.searchParams.set('operador_id', operadorId);
    
    // Cerrar dropdown
    document.getElementById('pdf-dropdown-menu-operador').classList.add('hidden');
    
    // Mostrar notificación de descarga
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'Generando PDF',
            text: `Descargando historial de: ${operadorNombre}`,
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }
    
    // Abrir PDF en nueva ventana
    window.open(url.toString(), '_blank');
    
    // Resetear selector
    operadorSelect.value = '';
}

// Manejar dropdown de operador
document.addEventListener('DOMContentLoaded', function() {
    const dropdownButtonOperador = document.getElementById('pdf-dropdown-button-operador');
    const dropdownMenuOperador = document.getElementById('pdf-dropdown-menu-operador');

    if (dropdownButtonOperador && dropdownMenuOperador) {
        dropdownButtonOperador.addEventListener('click', function() {
            dropdownMenuOperador.classList.toggle('hidden');
            // Cerrar el dropdown de vehículos si está abierto
            const dropdownMenuMain = document.getElementById('pdf-dropdown-menu-main');
            if (dropdownMenuMain) {
                dropdownMenuMain.classList.add('hidden');
            }
        });

        // Cerrar dropdown al hacer click fuera
        document.addEventListener('click', function(event) {
            if (!dropdownButtonOperador.contains(event.target) && !dropdownMenuOperador.contains(event.target)) {
                dropdownMenuOperador.classList.add('hidden');
            }
        });
    }
});

// Manejar dropdown de vehículo para mantenimientos
document.addEventListener('DOMContentLoaded', function() {
    const dropdownButtonVehiculo = document.getElementById('pdf-dropdown-button-vehiculo');
    const dropdownMenuVehiculo = document.getElementById('pdf-dropdown-menu-vehiculo');

    if (dropdownButtonVehiculo && dropdownMenuVehiculo) {
        dropdownButtonVehiculo.addEventListener('click', function() {
            dropdownMenuVehiculo.classList.toggle('hidden');
            // Cerrar otros dropdowns si están abiertos
            const dropdownMenuOperador = document.getElementById('pdf-dropdown-menu-operador');
            const dropdownMenuMain = document.getElementById('pdf-dropdown-menu-main');
            if (dropdownMenuOperador) {
                dropdownMenuOperador.classList.add('hidden');
            }
            if (dropdownMenuMain) {
                dropdownMenuMain.classList.add('hidden');
            }
        });

        // Cerrar dropdown al hacer click fuera
        document.addEventListener('click', function(event) {
            if (!dropdownButtonVehiculo.contains(event.target) && !dropdownMenuVehiculo.contains(event.target)) {
                dropdownMenuVehiculo.classList.add('hidden');
            }
        });
    }
});

// Funciones para el dropdown de vehículo
function cerrarDropdownVehiculo() {
    const dropdownMenu = document.getElementById('pdf-dropdown-menu-vehiculo');
    if (dropdownMenu) {
        dropdownMenu.classList.add('hidden');
    }
}

function generarPDFVehiculo() {
    const vehiculoSelect = document.getElementById('vehiculo-select-dropdown');
    const vehiculoId = vehiculoSelect.value;
    const vehiculoNombre = vehiculoSelect.options[vehiculoSelect.selectedIndex].text;
    
    if (!vehiculoId) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Selección requerida',
                text: 'Por favor seleccione un vehículo para generar el PDF con su historial de mantenimientos.',
                confirmButtonColor: '#f59e0b'
            });
        } else {
            alert('Por favor seleccione un vehículo para generar el PDF con su historial de mantenimientos.');
        }
        return;
    }
    
    // Cerrar dropdown
    cerrarDropdownVehiculo();
    
    // Construir URL con parámetros
    const url = new URL('{{ route("reportes.historial-mantenimientos-vehiculo") }}', window.location.origin);
    url.searchParams.append('vehiculo_id', vehiculoId);
    url.searchParams.append('formato', 'pdf');
    
    console.log('Generated PDF URL:', url.toString());
    
    // Mostrar loading si SweetAlert está disponible
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Generando PDF...',
            text: `Descargando historial de mantenimientos de: ${vehiculoNombre}`,
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Cerrar loading después de un tiempo
        setTimeout(() => {
            Swal.close();
        }, 3000);
    }
    
    // Redirigir para descargar
    console.log('Navigating to:', url.toString());
    window.location.href = url.toString();
}

// Funciones para Excel de Obras por Vehículo
function cerrarDropdownExcelObras() {
    const dropdownMenu = document.getElementById('excel-dropdown-menu-obras');
    if (dropdownMenu) {
        dropdownMenu.classList.add('hidden');
    }
}

function generarExcelObrasVehiculo() {
    const vehiculoSelect = document.getElementById('vehiculo-excel-select-obras');
    const vehiculoId = vehiculoSelect.value;
    const vehiculoNombre = vehiculoSelect.options[vehiculoSelect.selectedIndex].text;
    
    if (!vehiculoId) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Selección requerida',
                text: 'Por favor seleccione un vehículo para generar el Excel con su historial de obras.',
                confirmButtonColor: '#059669'
            });
        } else {
            alert('Por favor seleccione un vehículo para generar el Excel con su historial de obras.');
        }
        return;
    }
    
    // Cerrar dropdown
    cerrarDropdownExcelObras();
    
    // Construir URL con parámetros
    const url = new URL('{{ route("reportes.historial-obras-vehiculo") }}', window.location.origin);
    url.searchParams.append('vehiculo_id', vehiculoId);
    url.searchParams.append('formato', 'excel');
    
    // Mostrar loading si SweetAlert está disponible
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Generando Excel...',
            text: `Descargando historial de obras de: ${vehiculoNombre}`,
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Cerrar loading después de un tiempo
        setTimeout(() => {
            Swal.close();
        }, 3000);
    }
    
    // Redirigir para descargar
    window.location.href = url.toString();
}

// Funciones para Excel de Mantenimientos por Vehículo
function cerrarDropdownExcelMantenimientos() {
    const dropdownMenu = document.getElementById('excel-dropdown-menu-mantenimientos');
    if (dropdownMenu) {
        dropdownMenu.classList.add('hidden');
    }
}

function generarExcelMantenimientosVehiculo() {
    const vehiculoSelect = document.getElementById('vehiculo-excel-select-mantenimientos');
    const vehiculoId = vehiculoSelect.value;
    const vehiculoNombre = vehiculoSelect.options[vehiculoSelect.selectedIndex].text;
    
    if (!vehiculoId) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Selección requerida',
                text: 'Por favor seleccione un vehículo para generar el Excel con su historial de mantenimientos.',
                confirmButtonColor: '#059669'
            });
        } else {
            alert('Por favor seleccione un vehículo para generar el Excel con su historial de mantenimientos.');
        }
        return;
    }
    
    // Cerrar dropdown
    cerrarDropdownExcelMantenimientos();
    
    // Construir URL con parámetros
    const url = new URL('{{ route("reportes.historial-mantenimientos-vehiculo") }}', window.location.origin);
    url.searchParams.append('vehiculo_id', vehiculoId);
    url.searchParams.append('formato', 'excel');
    
    // Mostrar loading si SweetAlert está disponible
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Generando Excel...',
            text: `Descargando historial de mantenimientos de: ${vehiculoNombre}`,
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Cerrar loading después de un tiempo
        setTimeout(() => {
            Swal.close();
        }, 3000);
    }
    
    // Redirigir para descargar
    window.location.href = url.toString();
}

// Funciones para Excel de Historial de Obras por Operador
function cerrarDropdownExcelOperador() {
    const dropdownMenu = document.getElementById('excel-dropdown-menu-operador');
    if (dropdownMenu) {
        dropdownMenu.classList.add('hidden');
    }
}

function generarExcelOperador() {
    const operadorSelect = document.getElementById('operador-excel-select');
    const operadorId = operadorSelect.value;
    const operadorNombre = operadorSelect.options[operadorSelect.selectedIndex].text;
    
    if (!operadorId) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Selección requerida',
                text: 'Por favor seleccione un operador para generar el Excel con su historial de obras.',
                confirmButtonColor: '#059669'
            });
        } else {
            alert('Por favor seleccione un operador para generar el Excel con su historial de obras.');
        }
        return;
    }
    
    // Cerrar dropdown
    cerrarDropdownExcelOperador();
    
    // Construir URL con parámetros
    const url = new URL('{{ route("reportes.historial-obras-operador") }}', window.location.origin);
    url.searchParams.append('operador_id', operadorId);
    url.searchParams.append('formato', 'excel');
    
    // Mostrar loading si SweetAlert está disponible
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Generando Excel...',
            text: `Descargando historial de obras de: ${operadorNombre}`,
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Cerrar loading después de un tiempo
        setTimeout(() => {
            Swal.close();
        }, 3000);
    }
    
    // Redirigir para descargar
    window.location.href = url.toString();
}
</script>
@endsection
