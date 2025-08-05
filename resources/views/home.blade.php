@extends('layouts.app')

@section('title', 'Dashboard')

@section('header', 'Dashboard')

@section('content')
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true]
    ]" />
    
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Bienvenido, {{ Auth::user()->name ?? 'Usuario' }}</h2>
        <p class="text-gray-600">Panel de control del sistema de Control Interno de Petrotekno</p>
    </div>
    
    <!-- Tarjetas de resumen -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-petroyellow">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-700">Vehículos</h3>
                    <p class="text-3xl font-bold text-gray-800">24</p>
                </div>
                <div class="bg-petroyellow bg-opacity-20 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-petroyellow" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                        <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H11a1 1 0 001-1v-1h3.05a2.5 2.5 0 014.9 0H20a1 1 0 001-1V8a1 1 0 00-.293-.707l-2-2A1 1 0 0018 5h-3.05a2.5 2.5 0 01-4.9 0H7a1 1 0 00-1 1v1.05a2.5 2.5 0 010 4.9V11a1 1 0 00-1-1H4a1 1 0 00-1 1v5a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H11a1 1 0 001-1v-1h3.05a2.5 2.5 0 014.9 0H20a1 1 0 001-1V8a1 1 0 00-.293-.707l-2-2A1 1 0 0018 5h-3.05a2.5 2.5 0 01-4.9 0H7a1 1 0 00-1 1z" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-600 mt-4">Total de vehículos registrados</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-purple-500">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-700">Kilometrajes</h3>
                    <p class="text-3xl font-bold text-gray-800">156</p>
                </div>
                <div class="bg-purple-500 bg-opacity-20 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-500" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1L13.5 2.5L16.17 5.17L10.5 10.84C10.1 11.24 10.1 11.86 10.5 12.25L11.75 13.5C12.14 13.89 12.76 13.89 13.16 13.5L18.83 7.83L21.5 10.5L23 9H21ZM12.5 8C11.1 8 10 9.1 10 10.5V11C10 12.1 10.9 13 12 13H13C14.1 13 15 12.1 15 11V10.5C15 9.1 13.9 8 12.5 8Z" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-600 mt-4">Registros de kilometraje</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-green-500">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-700">Operativos</h3>
                    <p class="text-3xl font-bold text-gray-800">18</p>
                </div>
                <div class="bg-green-500 bg-opacity-20 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-600 mt-4">Vehículos en operación</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-red-500">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-700">En mantenimiento</h3>
                    <p class="text-3xl font-bold text-gray-800">6</p>
                </div>
                <div class="bg-red-500 bg-opacity-20 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-600 mt-4">Vehículos en mantenimiento</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-blue-500">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-700">Conductores</h3>
                    <p class="text-3xl font-bold text-gray-800">15</p>
                </div>
                <div class="bg-blue-500 bg-opacity-20 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-600 mt-4">Personal autorizado</p>
        </div>
    </div>

    <!-- Accesos Rápidos a Módulos -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Accesos Rápidos</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <!-- Vehículos -->
            <a href="{{ route('vehiculos.index') }}" class="group flex flex-col items-center p-4 bg-gray-50 hover:bg-petroyellow hover:bg-opacity-20 rounded-lg transition duration-200">
                <div class="bg-petroyellow bg-opacity-20 group-hover:bg-petroyellow group-hover:bg-opacity-30 p-3 rounded-full mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-petroyellow" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                        <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H11a1 1 0 001-1v-1h3.05a2.5 2.5 0 014.9 0H20a1 1 0 001-1V8a1 1 0 00-.293-.707l-2-2A1 1 0 0018 5h-3.05a2.5 2.5 0 01-4.9 0H7a1 1 0 00-1 1v1.05a2.5 2.5 0 010 4.9V11a1 1 0 00-1-1H4a1 1 0 00-1 1v5a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H11a1 1 0 001-1v-1h3.05a2.5 2.5 0 014.9 0H20a1 1 0 001-1V8a1 1 0 00-.293-.707l-2-2A1 1 0 0018 5h-3.05a2.5 2.5 0 01-4.9 0H7a1 1 0 00-1 1z" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-gray-800 text-center">Vehículos</span>
            </a>

            <!-- Kilometrajes -->
            <a href="{{ route('kilometrajes.index') }}" class="group flex flex-col items-center p-4 bg-gray-50 hover:bg-purple-50 rounded-lg transition duration-200">
                <div class="bg-purple-500 bg-opacity-20 group-hover:bg-purple-500 group-hover:bg-opacity-30 p-3 rounded-full mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-500" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1L13.5 2.5L16.17 5.17L10.5 10.84C10.1 11.24 10.1 11.86 10.5 12.25L11.75 13.5C12.14 13.89 12.76 13.89 13.16 13.5L18.83 7.83L21.5 10.5L23 9H21ZM12.5 8C11.1 8 10 9.1 10 10.5V11C10 12.1 10.9 13 12 13H13C14.1 13 15 12.1 15 11V10.5C15 9.1 13.9 8 12.5 8Z" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-gray-800 text-center">Kilometrajes</span>
            </a>

            <!-- Personal -->
            <a href="{{ route('personal.index') }}" class="group flex flex-col items-center p-4 bg-gray-50 hover:bg-blue-50 rounded-lg transition duration-200">
                <div class="bg-blue-500 bg-opacity-20 group-hover:bg-blue-500 group-hover:bg-opacity-30 p-3 rounded-full mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-gray-800 text-center">Personal</span>
            </a>

            <!-- Obras -->
            <a href="{{ route('obras.index') }}" class="group flex flex-col items-center p-4 bg-gray-50 hover:bg-green-50 rounded-lg transition duration-200">
                <div class="bg-green-500 bg-opacity-20 group-hover:bg-green-500 group-hover:bg-opacity-30 p-3 rounded-full mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM7 5h10v2H7V5zm0 4h10v2H7V9zm0 4h10v2H7v-2z" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-gray-800 text-center">Obras</span>
            </a>

            <!-- Alertas de Mantenimiento -->
            <a href="{{ route('kilometrajes.alertas') }}" class="group flex flex-col items-center p-4 bg-gray-50 hover:bg-red-50 rounded-lg transition duration-200">
                <div class="bg-red-500 bg-opacity-20 group-hover:bg-red-500 group-hover:bg-opacity-30 p-3 rounded-full mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-gray-800 text-center">Alertas</span>
            </a>

            <!-- Reportes -->
            <a href="#" class="group flex flex-col items-center p-4 bg-gray-50 hover:bg-indigo-50 rounded-lg transition duration-200">
                <div class="bg-indigo-500 bg-opacity-20 group-hover:bg-indigo-500 group-hover:bg-opacity-30 p-3 rounded-full mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm2 10a1 1 0 10-2 0v3a1 1 0 102 0v-3zm2-3a1 1 0 011 1v5a1 1 0 11-2 0v-5a1 1 0 011-1zm4-1a1 1 0 10-2 0v7a1 1 0 102 0V8z" clip-rule="evenodd" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-gray-800 text-center">Reportes</span>
            </a>
        </div>
    </div>
    
    <!-- Actividad reciente y próximos mantenimientos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Actividad reciente -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Actividad Reciente</h3>
            <div class="space-y-4">
                <div class="border-l-4 border-petroyellow pl-4 py-2">
                    <p class="font-medium text-gray-800">Mantenimiento completado</p>
                    <p class="text-gray-600">Vehículo Toyota Hilux - KJS-456</p>
                    <p class="text-sm text-gray-500">Hace 2 horas - Juan Pérez</p>
                </div>
                <div class="border-l-4 border-petroyellow pl-4 py-2">
                    <p class="font-medium text-gray-800">Combustible recargado</p>
                    <p class="text-gray-600">Vehículo Ford Ranger - MNO-789</p>
                    <p class="text-sm text-gray-500">Hace 5 horas - Carlos Rodríguez</p>
                </div>
                <div class="border-l-4 border-petroyellow pl-4 py-2">
                    <p class="font-medium text-gray-800">Inspección realizada</p>
                    <p class="text-gray-600">Vehículo Mitsubishi L200 - ABC-123</p>
                    <p class="text-sm text-gray-500">Ayer - María López</p>
                </div>
                <div class="border-l-4 border-petroyellow pl-4 py-2">
                    <p class="font-medium text-gray-800">Reporte de incidente</p>
                    <p class="text-gray-600">Vehículo Chevrolet D-Max - XYZ-789</p>
                    <p class="text-sm text-gray-500">Hace 2 días - Pedro Gómez</p>
                </div>
            </div>
            <div class="mt-4 text-right">
                <a href="#" class="inline-block px-4 py-2 bg-petroyellow hover:bg-yellow-500 text-gray-700 font-medium rounded-md transition duration-200">Ver todas las actividades →</a>
            </div>
        </div>
        
        <!-- Próximos mantenimientos -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Próximos Mantenimientos</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="py-3 text-left text-sm font-semibold text-gray-600">Vehículo</th>
                            <th class="py-3 text-left text-sm font-semibold text-gray-600">Tipo</th>
                            <th class="py-3 text-left text-sm font-semibold text-gray-600">Fecha</th>
                            <th class="py-3 text-left text-sm font-semibold text-gray-600">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-200">
                            <td class="py-3 text-sm text-gray-800">Toyota Hilux KJS-456</td>
                            <td class="py-3 text-sm text-gray-800">Cambio de aceite</td>
                            <td class="py-3 text-sm text-gray-800">30/06/2023</td>
                            <td class="py-3 text-sm">
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Programado</span>
                            </td>
                        </tr>
                        <tr class="border-b border-gray-200">
                            <td class="py-3 text-sm text-gray-800">Ford Ranger MNO-789</td>
                            <td class="py-3 text-sm text-gray-800">Revisión general</td>
                            <td class="py-3 text-sm text-gray-800">02/07/2023</td>
                            <td class="py-3 text-sm">
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Programado</span>
                            </td>
                        </tr>
                        <tr class="border-b border-gray-200">
                            <td class="py-3 text-sm text-gray-800">Mitsubishi L200 ABC-123</td>
                            <td class="py-3 text-sm text-gray-800">Cambio de frenos</td>
                            <td class="py-3 text-sm text-gray-800">05/07/2023</td>
                            <td class="py-3 text-sm">
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Confirmado</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-3 text-sm text-gray-800">Chevrolet D-Max XYZ-789</td>
                            <td class="py-3 text-sm text-gray-800">Alineación y balanceo</td>
                            <td class="py-3 text-sm text-gray-800">10/07/2023</td>
                            <td class="py-3 text-sm">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Pendiente</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 text-right">
                <a href="#" class="inline-block px-4 py-2 bg-petroyellow hover:bg-yellow-500 text-gray-700 font-medium rounded-md transition duration-200">Ver todos los mantenimientos →</a>
            </div>
        </div>
    </div>
@endsection
