@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Panel de Configuración</h1>
            <p class="text-gray-600 mt-2">Gestiona la configuración del sistema</p>
        </div>
        <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-500">Usuario: {{ auth()->user()->nombre }}</span>
            <div class="h-8 w-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-medium">
                {{ strtoupper(substr(auth()->user()->nombre, 0, 1)) }}
            </div>
        </div>
    </div>

    <!-- Grid principal de configuración -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- Gestión de Personal y Roles -->
        @can('gestionar_personal')
        <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 col-span-1 md:col-span-2">
            <div class="p-6">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-purple-500 text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 515.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 919.288 0M15 7a3 3 0 11-6 0 3 3 0 616 0zm6 3a2 2 0 11-4 0 2 2 0 414 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5">
                        <h3 class="text-lg font-medium text-gray-900">Gestión de Personal y Roles</h3>
                        <p class="text-sm text-gray-500">Puestos operativos y roles de acceso al sistema</p>
                    </div>
                </div>
                
                <!-- DOS BOTONES PRINCIPALES SEPARADOS -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- BOTÓN 1: GESTIONAR PUESTOS DE PERSONAL -->
                    <div class="bg-gradient-to-br from-purple-500 to-purple-700 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <div class="text-center">
                            <div class="mb-4">
                                <svg class="w-12 h-12 mx-auto text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-2">PUESTOS DE PERSONAL</h3>
                            <p class="text-purple-100 text-sm mb-4">Gestiona las clasificaciones operativas del personal</p>
                            <div class="bg-white/20 rounded-lg p-3 mb-4">
                                <div class="text-sm">
                                    <div class="flex justify-between">
                                        <span>Total puestos:</span>
                                        <span class="font-bold">{{ \App\Models\CategoriaPersonal::count() }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Personal asignado:</span>
                                        <span class="font-bold">{{ \App\Models\Personal::count() }}</span>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('categorias-personal.index') }}" class="w-full bg-white text-purple-700 py-3 px-6 rounded-lg font-bold hover:bg-purple-50 transition-colors duration-200 inline-block">
                                🏷️ GESTIONAR PUESTOS
                            </a>
                        </div>
                    </div>

                    <!-- BOTÓN 2: GESTIONAR ROLES DE USUARIO -->
                    @can('ver_roles')
                    <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <div class="text-center">
                            <div class="mb-4">
                                <svg class="w-12 h-12 mx-auto text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 515.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 919.288 0M15 7a3 3 0 11-6 0 3 3 0 616 0zm6 3a2 2 0 11-4 0 2 2 0 414 0zM7 10a2 2 0 11-4 0 2 2 0 414 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-2">ROLES DE USUARIO</h3>
                            <p class="text-blue-100 text-sm mb-4">Gestiona el control de acceso al sistema</p>
                            <div class="bg-white/20 rounded-lg p-3 mb-4">
                                <div class="text-sm">
                                    <div class="flex justify-between">
                                        <span>Total roles:</span>
                                        <span class="font-bold">{{ \App\Models\Role::count() }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Usuarios con rol:</span>
                                        <span class="font-bold">{{ \App\Models\User::whereNotNull('rol_id')->count() }}</span>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('admin.roles.index') }}" class="w-full bg-white text-blue-700 py-3 px-6 rounded-lg font-bold hover:bg-blue-50 transition-colors duration-200 inline-block">
                                🎯 GESTIONAR ROLES
                            </a>
                            @can('crear_roles')
                            <div class="mt-3">
                                <a href="{{ route('admin.roles.create') }}" class="text-blue-100 hover:text-white text-sm font-medium underline">
                                    + Crear nuevo rol
                                </a>
                            </div>
                            @endcan
                        </div>
                    </div>
                    @else
                    <!-- Si no tiene permisos para ver roles -->
                    <div class="bg-gray-300 rounded-xl p-6 text-gray-600 shadow-lg">
                        <div class="text-center">
                            <div class="mb-4">
                                <svg class="w-12 h-12 mx-auto text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-2">ROLES DE USUARIO</h3>
                            <p class="text-gray-500 text-sm mb-4">No tienes permisos para gestionar roles</p>
                            <div class="bg-gray-200 rounded-lg p-3 mb-4">
                                <div class="text-sm text-gray-600">
                                    Contacta al administrador para obtener acceso
                                </div>
                            </div>
                            <button disabled class="w-full bg-gray-400 text-gray-600 py-3 px-6 rounded-lg font-bold cursor-not-allowed">
                                🔒 ACCESO RESTRINGIDO
                            </button>
                        </div>
                    </div>
                    @endcan
                </div>

                <!-- Información Adicional -->
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <div class="text-xs text-gray-600 bg-yellow-50 border border-yellow-200 rounded p-2">
                        <div class="flex items-start">
                            <svg class="w-4 h-4 text-yellow-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <strong>Diferencia importante:</strong> Los <em>puestos</em> clasifican el personal operativamente, mientras que los <em>roles</em> controlan el acceso al sistema.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endcan

        <!-- Configuración General -->
        <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-gray-500 text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5">
                        <h3 class="text-lg font-medium text-gray-900">Configuración General</h3>
                        <p class="text-sm text-gray-500">Ajustes generales del sistema</p>
                    </div>
                </div>
                <div class="mt-6">
                    <div class="text-sm text-gray-600 space-y-2">
                        <div class="flex justify-between">
                            <span>Versión:</span>
                            <span class="font-medium">{{ config('app.version', '1.0.0') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Entorno:</span>
                            <span class="font-medium">{{ config('app.env') }}</span>
                        </div>
                    </div>
                </div>
                <div class="mt-6">
                    <button class="w-full bg-gray-600 hover:bg-gray-700 text-white text-center py-2 px-4 rounded-lg font-medium transition-colors duration-200">
                        Configurar
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- Información del Sistema -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-blue-400 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            <div class="text-sm text-blue-700">
                <h3 class="font-medium text-blue-900 mb-2">Panel de Configuración del Sistema</h3>
                <p>Desde este panel puedes gestionar todos los aspectos de configuración del sistema de control interno de Petrotekno. Utiliza las opciones de menú para acceder a las diferentes secciones de administración.</p>
                <p class="mt-2"><strong>Última actualización:</strong> {{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
