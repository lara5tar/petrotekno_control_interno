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
        <p class="text-sm text-gray-500 mt-1">Última actualización: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
    
    <!-- Tarjetas de resumen con diseño consistente -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-medium text-gray-600 uppercase tracking-wide">Activos</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ $estadisticas['total_vehiculos'] }}</p>
                </div>
                <div class="bg-gray-100 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-600" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-3">Total de activos registrados</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-medium text-gray-600 uppercase tracking-wide">Disponibles</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ $estadisticas['vehiculos_disponibles'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-3">Activos disponibles</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-medium text-gray-600 uppercase tracking-wide">Asignados</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ $estadisticas['vehiculos_asignados'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm3 1h6v4H7V5zm6 6H7v2h6v-2z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-3">Activos asignados</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-medium text-gray-600 uppercase tracking-wide">Personal</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ $estadisticas['total_personal'] }}</p>
                </div>
                <div class="bg-gray-100 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-3">Personal activo</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-medium text-gray-600 uppercase tracking-wide">Obras</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ $estadisticas['obras_activas'] }}</p>
                </div>
                <div class="bg-gray-100 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM7 5h10v2H7V5zm0 4h10v2H7V9zm0 4h10v2H7v-2z" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-3">Obras en progreso</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-medium text-gray-600 uppercase tracking-wide">Programados</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ $estadisticas['mantenimientos_programados'] }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-3">Mantenimientos programados</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-medium text-gray-600 uppercase tracking-wide">Completados</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ $estadisticas['mantenimientos_completados'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-3">Mantenimientos completados</p>
        </div>
    </div>

    
    <!-- Actividad reciente -->
    <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Actividad Reciente
            </h3>
            <p class="text-sm text-gray-500 mt-1">Últimas acciones realizadas en el sistema</p>
        </div>
        
        <div class="p-6">
            @forelse($actividadReciente as $loop => $actividad)
            <div class="flex items-start space-x-4 py-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                <!-- Icono de actividad -->
                <div class="flex-shrink-0">
                    @if(str_contains($actividad['descripcion'], 'Mantenimiento'))
                        <div class="bg-green-100 p-2 rounded-full">
                            <svg class="w-5 h-5 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                        </div>
                    @else
                        <div class="bg-blue-100 p-2 rounded-full">
                            <svg class="w-5 h-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    @endif
                </div>
                
                <!-- Contenido de la actividad -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-medium text-gray-900 truncate">
                            {{ $actividad['descripcion'] }}
                        </h4>
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ str_contains($actividad['descripcion'], 'Mantenimiento') ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ str_contains($actividad['descripcion'], 'Mantenimiento') ? 'Mantenimiento' : 'Kilometraje' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="mt-1 flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 mr-1 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                        </svg>
                        {{ $actividad['vehiculo'] }}
                    </div>
                    
                    <div class="mt-2 flex items-center justify-between">
                        <div class="flex items-center text-xs text-gray-500">
                            <svg class="w-3 h-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $actividad['fecha']->diffForHumans() }}
                        </div>
                        <div class="flex items-center text-xs text-gray-500">
                            <svg class="w-3 h-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            {{ $actividad['usuario'] }}
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <div class="bg-gray-100 p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-sm font-medium text-gray-900 mb-1">No hay actividad reciente</h3>
                <p class="text-sm text-gray-500">Las actividades del sistema aparecerán aquí cuando se realicen acciones.</p>
            </div>
            @endforelse
        </div>
    </div>
@endsection
