@extends('layouts.app')

@section('title', 'Configuración')

@section('header', 'Configuración del Sistema')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Configuración']
    ]" />

    <!-- Encabezado -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900">Configuración del Sistema</h2>
        <p class="text-gray-600 mt-2">Gestiona todos los aspectos y configuraciones del sistema</p>
        <p class="text-sm text-gray-500 mt-1">Última actualización: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <!-- SECCIÓN 1: GESTIÓN DE PERSONAL -->
    @if(auth()->user()->hasPermission('ver_personal'))
    <div class="mb-10">
        <div class="mb-4">
            <h3 class="text-xl font-bold text-gray-800 mb-2">👥 Gestión de Personal</h3>
            <p class="text-gray-600">Administra categorías operativas y roles de usuario del sistema</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="divide-y divide-gray-200">
                
                <!-- Categorías de Personal -->
                <div class="p-6 hover:bg-gray-50 transition duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center flex-1">
                            <div class="bg-blue-100 p-3 rounded-lg mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-900">Categorías de Personal</h4>
                                <p class="text-gray-600">Clasificaciones operativas del personal de campo y administrativo</p>
                                <div class="flex items-center mt-2 space-x-4">
                                    <span class="text-sm text-gray-500">1 categorías</span>
                                    <span class="text-sm text-gray-500">18 personal registrado</span>
                                </div>
                            </div>
                        </div>
                        <div class="ml-6">
                            <a href="{{ route('personal.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 inline-flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                                </svg>
                                Gestionar Personal
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Roles de Usuario -->
                @if(auth()->user()->hasPermission('ver_roles'))
                <div class="p-6 hover:bg-gray-50 transition duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center flex-1">
                            <div class="bg-green-100 p-3 rounded-lg mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-4 4-4-4 4-4 .257-.257A6 6 0 1118 8zm-6-2a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-900">Roles de Usuario</h4>
                                <p class="text-gray-600">Permisos y control de acceso a las funcionalidades del sistema</p>
                                <div class="flex items-center mt-2 space-x-4">
                                    <span class="text-sm text-gray-500">1 roles definidos</span>
                                    <span class="text-sm text-gray-500">1 usuarios asignados</span>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Activo</span>
                                </div>
                            </div>
                        </div>
                        <div class="ml-6 flex space-x-3">
                            <a href="{{ route('roles.index') }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 inline-flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                                </svg>
                                Gestionar Roles
                            </a>
                            @if(auth()->user()->hasPermission('crear_roles'))
                            <a href="{{ route('roles.create') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 inline-flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                                </svg>
                                Crear Rol
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Nota informativa -->
        <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-start">
                <div class="bg-yellow-100 p-2 rounded-lg mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="text-sm">
                    <p class="font-medium text-yellow-800 mb-1">Diferenciación Importante</p>
                    <p class="text-yellow-700">
                        Las <strong>categorías de personal</strong> clasifican operativamente a los trabajadores (técnicos, supervisores, etc.), 
                        mientras que los <strong>roles de usuario</strong> controlan el acceso a las funcionalidades del sistema.
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- SECCIÓN 2: CONFIGURACIÓN DEL SISTEMA -->
    <div class="mb-10">
        <div class="mb-4">
            <h3 class="text-xl font-bold text-gray-800 mb-2">⚙️ Configuración del Sistema</h3>
            <p class="text-gray-600">Ajustes globales y parámetros de funcionamiento</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center flex-1">
                        <div class="bg-purple-100 p-3 rounded-lg mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-semibold text-gray-900">Configuración General</h4>
                            <p class="text-gray-600">Administra los parámetros globales y configuraciones centrales</p>
                            <div class="flex items-center mt-2 space-x-6">
                                <span class="text-sm text-gray-500">
                                    Versión <span class="font-medium text-gray-700">{{ config('app.version', '1.0.0') }}</span>
                                </span>
                                <span class="text-sm text-gray-500">
                                    Entorno <span class="font-medium text-gray-700 capitalize">{{ config('app.env') }}</span>
                                </span>
                                <div class="flex items-center">
                                    <div class="h-2 w-2 bg-green-500 rounded-full mr-1"></div>
                                    <span class="text-xs text-gray-600">Sistema Operativo</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ml-6">
                        <button class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 inline-flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                            </svg>
                            Configurar Sistema
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 3: SEGURIDAD Y RESPALDOS -->
    <div class="mb-10">
        <div class="mb-4">
            <h3 class="text-xl font-bold text-gray-800 mb-2">🔒 Seguridad y Respaldos</h3>
            <p class="text-gray-600">Gestión de copias de seguridad y políticas de seguridad</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center flex-1">
                        <div class="bg-orange-100 p-3 rounded-lg mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-600" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-semibold text-gray-900">Respaldos del Sistema</h4>
                            <p class="text-gray-600">Gestiona las copias de seguridad automáticas y manuales</p>
                            <div class="flex items-center mt-2 space-x-6">
                                <span class="text-sm text-gray-500">
                                    Último respaldo <span class="font-medium text-gray-700">hace 2 horas</span>
                                </span>
                                <span class="text-sm text-gray-500">
                                    Frecuencia <span class="font-medium text-gray-700">diaria</span>
                                </span>
                                <div class="flex items-center">
                                    <div class="h-2 w-2 bg-green-500 rounded-full mr-1"></div>
                                    <span class="text-xs text-gray-600">Respaldos Activos</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ml-6">
                        <button class="bg-orange-600 hover:bg-orange-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 inline-flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                            Gestionar Respaldos
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 4: MONITOREO Y LOGS -->
    <div class="mb-10">
        <div class="mb-4">
            <h3 class="text-xl font-bold text-gray-800 mb-2">📊 Monitoreo y Registros</h3>
            <p class="text-gray-600">Supervisa la actividad del sistema y revisa los logs</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center flex-1">
                        <div class="bg-red-100 p-3 rounded-lg mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-semibold text-gray-900">Logs del Sistema</h4>
                            <p class="text-gray-600">Revisa eventos, errores y actividad del sistema</p>
                            <div class="flex items-center mt-2 space-x-6">
                                <span class="text-sm text-gray-500">
                                    Eventos hoy <span class="font-medium text-gray-700">127</span>
                                </span>
                                <span class="text-sm text-gray-500">
                                    Errores <span class="font-medium text-red-600">3</span>
                                </span>
                                <span class="text-sm text-gray-500">
                                    Advertencias <span class="font-medium text-orange-600">8</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="ml-6">
                        <button class="bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 inline-flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                            </svg>
                            Ver Logs del Sistema
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
                                <h4 class="text-lg font-semibold text-gray-800">Categorías de Personal</h4>
                                <p class="text-sm text-gray-600">Clasificaciones operativas del personal de campo y administrativo</p>
                                <div class="flex items-center mt-2 space-x-4">
                                    <span class="text-sm text-gray-500">
                                        <span class="font-medium text-gray-700">{{ \App\Models\CategoriaPersonal::count() }}</span> categorías
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        <span class="font-medium text-gray-700">{{ \App\Models\Personal::count() }}</span> personal registrado
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="ml-6">
                            <a href="{{ route('categorias-personal.index') }}" 
                               class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 inline-flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                                </svg>
                                Gestionar
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Roles de Usuario -->
                @if(auth()->user()->hasPermission('ver_roles'))
                <div class="p-6 hover:bg-gray-50 transition duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center flex-1">
                            <div class="bg-gray-200 p-2 rounded-lg mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <h4 class="text-lg font-semibold text-gray-800">Roles de Usuario</h4>
                                    <div class="flex items-center ml-3">
                                        <div class="h-2 w-2 bg-green-500 rounded-full mr-1"></div>
                                        <span class="text-xs text-gray-600">Activo</span>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600">Permisos y control de acceso a las funcionalidades del sistema</p>
                                <div class="flex items-center mt-2 space-x-4">
                                    <span class="text-sm text-gray-500">
                                        <span class="font-medium text-gray-700">{{ \App\Models\Role::count() }}</span> roles definidos
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        <span class="font-medium text-gray-700">{{ \App\Models\User::whereNotNull('rol_id')->count() }}</span> usuarios asignados
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="ml-6 flex space-x-2">
                            <a href="{{ route('admin.roles.index') }}" 
                               class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 inline-flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                                </svg>
                                Gestionar
                            </a>
                            @if(auth()->user()->hasPermission('crear_roles'))
                            <a href="{{ route('admin.roles.create') }}" 
                               class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-3 rounded-lg transition duration-200 inline-flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                                </svg>
                                Crear
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @else
                <!-- Sin permisos para roles -->
                <div class="p-6 bg-gray-50 opacity-75">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center flex-1">
                            <div class="bg-gray-300 p-2 rounded-lg mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <h4 class="text-lg font-semibold text-gray-600">Roles de Usuario</h4>
                                    <div class="flex items-center ml-3">
                                        <div class="h-2 w-2 bg-red-500 rounded-full mr-1"></div>
                                        <span class="text-xs text-gray-500">Restringido</span>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-500">No tienes permisos para gestionar roles del sistema</p>
                                <div class="mt-2">
                                    <span class="text-sm text-gray-400">Contacta al administrador para obtener acceso</span>
                                </div>
                            </div>
                        </div>
                        <div class="ml-6">
                            <button disabled class="bg-gray-400 text-gray-600 font-medium py-2 px-4 rounded-lg cursor-not-allowed inline-flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                                Bloqueado
                            </button>
                        </div>
                    </div>
                </div>
                @endif
            </div>
                            </a>
                            
                            @if(auth()->user()->hasPermission('crear_roles'))
                            <a href="{{ route('admin.roles.create') }}" 
                               class="w-full bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200 inline-flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                                </svg>
                                Crear Nuevo Rol
                            </a>
                            @endif
                        </div>
                    </div>
                    @else
                    <!-- Sin permisos para roles -->
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-6 opacity-75">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="bg-gray-300 p-2 rounded-lg mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-600">Roles de Usuario</h4>
                                    <p class="text-sm text-gray-500">Acceso restringido</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="h-2 w-2 bg-red-500 rounded-full mr-2"></div>
                                <span class="text-xs text-gray-500">Restringido</span>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-lg p-4 mb-4 border border-gray-200">
                            <div class="text-center">
                                <div class="text-sm text-gray-500">No tienes permisos para gestionar roles</div>
                            </div>
                        </div>
                        
                        <button disabled class="w-full bg-gray-400 text-gray-600 font-medium py-3 px-4 rounded-lg cursor-not-allowed inline-flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                            Acceso Restringido
                        </button>
                    </div>
                    @endif
                </div>

                <!-- Nota informativa -->
                <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="bg-yellow-100 p-1 rounded-lg mr-3 mt-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="text-sm">
                            <p class="font-medium text-yellow-800 mb-1">Diferenciación Importante</p>
                            <p class="text-yellow-700">
                                Las <strong>categorías de personal</strong> clasifican operativamente a los trabajadores (técnicos, supervisores, etc.), 
                                mientras que los <strong>roles de usuario</strong> controlan el acceso a las funcionalidades del sistema.
                            </p>
                        </div>
                    </div>
                </div>
        </div>
    </div>

    <!-- Configuración General del Sistema -->
    <div class="mb-8">
        <div class="bg-white rounded-lg shadow-md border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Configuración General</h3>
                <p class="text-sm text-gray-600">Ajustes globales del sistema y parámetros de funcionamiento</p>
            </div>
            
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center flex-1">
                        <div class="bg-gray-100 p-3 rounded-lg mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-xl font-semibold text-gray-800">Configuración del Sistema</h4>
                            <p class="text-gray-600 mb-3">Administra los parámetros globales y configuraciones centrales</p>
                            <div class="flex items-center space-x-6">
                                <span class="text-sm text-gray-500">
                                    Versión <span class="font-medium text-gray-700">{{ config('app.version', '1.0.0') }}</span>
                                </span>
                                <span class="text-sm text-gray-500">
                                    Entorno <span class="font-medium text-gray-700 capitalize">{{ config('app.env') }}</span>
                                </span>
                                <div class="flex items-center">
                                    <div class="h-2 w-2 bg-green-500 rounded-full mr-1"></div>
                                    <span class="text-xs text-gray-600">Sistema Operativo</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ml-6">
                        <button class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 inline-flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                            </svg>
                            Configurar Sistema
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gestión de Respaldos -->
    <div class="mb-8">
        <div class="bg-white rounded-lg shadow-md border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Gestión de Respaldos</h3>
                <p class="text-sm text-gray-600">Administra copias de seguridad y restauración de datos</p>
            </div>
            
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center flex-1">
                        <div class="bg-gray-100 p-3 rounded-lg mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-xl font-semibold text-gray-800">Respaldos del Sistema</h4>
                            <p class="text-gray-600 mb-3">Gestiona las copias de seguridad automáticas y manuales</p>
                            <div class="flex items-center space-x-6">
                                <span class="text-sm text-gray-500">
                                    Último respaldo <span class="font-medium text-gray-700">hace 2 horas</span>
                                </span>
                                <span class="text-sm text-gray-500">
                                    Frecuencia <span class="font-medium text-gray-700">diaria</span>
                                </span>
                                <div class="flex items-center">
                                    <div class="h-2 w-2 bg-green-500 rounded-full mr-1"></div>
                                    <span class="text-xs text-gray-600">Respaldos Activos</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ml-6">
                        <button class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 inline-flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                            Gestionar Respaldos
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monitoreo y Logs -->
    <div class="mb-8">
        <div class="bg-white rounded-lg shadow-md border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Monitoreo y Registros</h3>
                <p class="text-sm text-gray-600">Supervisa la actividad del sistema y revisa los logs</p>
            </div>
            
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center flex-1">
                        <div class="bg-gray-100 p-3 rounded-lg mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-xl font-semibold text-gray-800">Logs del Sistema</h4>
                            <p class="text-gray-600 mb-3">Revisa eventos, errores y actividad del sistema</p>
                            <div class="flex items-center space-x-6">
                                <span class="text-sm text-gray-500">
                                    Eventos hoy <span class="font-medium text-gray-700">127</span>
                                </span>
                                <span class="text-sm text-gray-500">
                                    Errores <span class="font-medium text-red-600">3</span>
                                </span>
                                <span class="text-sm text-gray-500">
                                    Advertencias <span class="font-medium text-orange-600">8</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="ml-6">
                        <button class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 inline-flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                            </svg>
                            Ver Logs del Sistema
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
