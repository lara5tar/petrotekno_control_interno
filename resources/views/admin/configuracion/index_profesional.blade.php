@extends('layouts.app')

@section('content')
<!-- Fondo con gradiente sutil -->
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <!-- Header profesional -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="h-12 w-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Configuración del Sistema</h1>
                        <p class="text-sm text-gray-600 mt-1">Centro de administración y gestión de configuraciones</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->nombre_completo ?? auth()->user()->nombre }}</p>
                        <p class="text-xs text-gray-500">Administrador del Sistema</p>
                    </div>
                    <div class="h-10 w-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-semibold shadow-lg">
                        {{ strtoupper(substr(auth()->user()->nombre_completo ?? auth()->user()->nombre ?? 'A', 0, 1)) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        
        <!-- Sección de acceso rápido -->
        <div class="mb-8">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Acceso Rápido
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Gestiona los aspectos principales del sistema</p>
                </div>
                
                <!-- Gestión de Personal y Roles -->
                @if(auth()->user()->hasPermission('ver_personal'))
                <div class="p-6">
                    <div class="mb-6">
                        <div class="flex items-center space-x-3 mb-2">
                            <div class="h-8 w-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="h-4 w-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 515.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 919.288 0M15 7a3 3 0 11-6 0 3 3 0 616 0zm6 3a2 2 0 11-4 0 2 2 0 414 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Gestión de Personal y Roles</h3>
                                <p class="text-sm text-gray-600">Administra categorías operativas y permisos de acceso</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- DOS TARJETAS PRINCIPALES -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- CATEGORÍAS DE PERSONAL -->
                        <div class="group">
                            <div class="bg-gradient-to-br from-purple-500 via-purple-600 to-purple-700 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border border-purple-200">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="h-12 w-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                    </div>
                                    <div class="flex -space-x-1">
                                        <div class="h-6 w-6 bg-white/30 rounded-full border-2 border-white/50"></div>
                                        <div class="h-6 w-6 bg-white/40 rounded-full border-2 border-white/50"></div>
                                        <div class="h-6 w-6 bg-white/50 rounded-full border-2 border-white/50"></div>
                                    </div>
                                </div>
                                
                                <h3 class="text-xl font-bold mb-2">Categorías de Personal</h3>
                                <p class="text-purple-100 text-sm mb-6 leading-relaxed">
                                    Gestiona las clasificaciones operativas del personal de campo y administrativo
                                </p>
                                
                                <!-- Estadísticas -->
                                <div class="bg-white/10 backdrop-blur rounded-xl p-4 mb-6 border border-white/20">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="text-center">
                                            <div class="text-2xl font-bold text-white">{{ \App\Models\CategoriaPersonal::count() }}</div>
                                            <div class="text-xs text-purple-200 uppercase tracking-wider">Categorías</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-2xl font-bold text-white">{{ \App\Models\Personal::count() }}</div>
                                            <div class="text-xs text-purple-200 uppercase tracking-wider">Personal</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <a href="{{ route('categorias-personal.index') }}" class="w-full bg-white/90 hover:bg-white text-purple-700 py-3 px-6 rounded-xl font-semibold transition-all duration-200 inline-flex items-center justify-center space-x-2 group-hover:shadow-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span>Gestionar Categorías</span>
                                </a>
                            </div>
                        </div>

                        <!-- ROLES DE USUARIO -->
                        @if(auth()->user()->hasPermission('ver_roles'))
                        <div class="group">
                            <div class="bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-700 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border border-blue-200">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="h-12 w-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <div class="h-2 w-2 bg-green-400 rounded-full animate-pulse"></div>
                                        <span class="text-xs text-blue-200">Activo</span>
                                    </div>
                                </div>
                                
                                <h3 class="text-xl font-bold mb-2">Roles de Usuario</h3>
                                <p class="text-blue-100 text-sm mb-6 leading-relaxed">
                                    Configura permisos y controla el acceso a las funcionalidades del sistema
                                </p>
                                
                                <!-- Estadísticas -->
                                <div class="bg-white/10 backdrop-blur rounded-xl p-4 mb-6 border border-white/20">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="text-center">
                                            <div class="text-2xl font-bold text-white">{{ \App\Models\Role::count() }}</div>
                                            <div class="text-xs text-blue-200 uppercase tracking-wider">Roles</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-2xl font-bold text-white">{{ \App\Models\User::whereNotNull('rol_id')->count() }}</div>
                                            <div class="text-xs text-blue-200 uppercase tracking-wider">Usuarios</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="space-y-3">
                                    <a href="{{ route('admin.roles.index') }}" class="w-full bg-white/90 hover:bg-white text-blue-700 py-3 px-6 rounded-xl font-semibold transition-all duration-200 inline-flex items-center justify-center space-x-2 group-hover:shadow-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                                        </svg>
                                        <span>Gestionar Roles</span>
                                    </a>
                                    
                                    @if(auth()->user()->hasPermission('crear_roles'))
                                    <a href="{{ route('admin.roles.create') }}" class="w-full bg-white/20 hover:bg-white/30 text-white py-2 px-6 rounded-xl text-sm font-medium transition-all duration-200 inline-flex items-center justify-center space-x-2 border border-white/30">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        <span>Crear Nuevo Rol</span>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @else
                        <!-- Sin permisos para roles -->
                        <div class="group">
                            <div class="bg-gradient-to-br from-gray-400 to-gray-500 rounded-2xl p-6 text-white shadow-xl border border-gray-300 opacity-75">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="h-12 w-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <div class="h-2 w-2 bg-red-400 rounded-full"></div>
                                        <span class="text-xs text-gray-200">Restringido</span>
                                    </div>
                                </div>
                                
                                <h3 class="text-xl font-bold mb-2">Roles de Usuario</h3>
                                <p class="text-gray-200 text-sm mb-6 leading-relaxed">
                                    No tienes permisos para gestionar roles del sistema
                                </p>
                                
                                <div class="bg-white/10 backdrop-blur rounded-xl p-4 mb-6 border border-white/20">
                                    <div class="text-center">
                                        <div class="text-sm text-gray-200">Contacta al administrador para obtener acceso</div>
                                    </div>
                                </div>
                                
                                <button disabled class="w-full bg-gray-600 text-gray-300 py-3 px-6 rounded-xl font-semibold cursor-not-allowed inline-flex items-center justify-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    <span>Acceso Restringido</span>
                                </button>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Nota informativa profesional -->
                    <div class="mt-8 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-xl p-4">
                        <div class="flex items-start space-x-3">
                            <div class="h-6 w-6 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="text-sm">
                                <p class="font-medium text-amber-900 mb-1">Diferenciación Importante</p>
                                <p class="text-amber-800 leading-relaxed">
                                    Las <strong>categorías de personal</strong> se utilizan para clasificar operativamente a los trabajadores (técnicos, supervisores, etc.), mientras que los <strong>roles de usuario</strong> determinan qué funcionalidades pueden acceder en el sistema.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Grid de configuraciones adicionales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- Configuración General -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="h-10 w-10 bg-slate-100 rounded-xl flex items-center justify-center">
                            <svg class="h-5 w-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Configuración General</h3>
                            <p class="text-sm text-gray-600">Ajustes globales del sistema</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Versión</span>
                            <span class="text-sm font-medium text-gray-900">{{ config('app.version', '1.0.0') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Entorno</span>
                            <span class="text-sm font-medium text-gray-900 capitalize">{{ config('app.env') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-sm text-gray-600">Estado</span>
                            <span class="text-sm font-medium text-green-600 flex items-center">
                                <div class="h-2 w-2 bg-green-500 rounded-full mr-2"></div>
                                Operativo
                            </span>
                        </div>
                    </div>
                    
                    <button class="w-full bg-slate-600 hover:bg-slate-700 text-white py-2.5 px-4 rounded-xl font-medium transition-colors duration-200 flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                        </svg>
                        <span>Configurar</span>
                    </button>
                </div>
            </div>

            <!-- Respaldos -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="h-10 w-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                            <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Respaldos</h3>
                            <p class="text-sm text-gray-600">Gestiona copias de seguridad</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Último respaldo</span>
                            <span class="text-sm font-medium text-gray-900">Hace 2 horas</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Frecuencia</span>
                            <span class="text-sm font-medium text-gray-900">Diaria</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-sm text-gray-600">Estado</span>
                            <span class="text-sm font-medium text-green-600 flex items-center">
                                <div class="h-2 w-2 bg-green-500 rounded-full mr-2"></div>
                                Activo
                            </span>
                        </div>
                    </div>
                    
                    <button class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2.5 px-4 rounded-xl font-medium transition-colors duration-200 flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        <span>Gestionar Respaldos</span>
                    </button>
                </div>
            </div>

            <!-- Logs del Sistema -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="h-10 w-10 bg-amber-100 rounded-xl flex items-center justify-center">
                            <svg class="h-5 w-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Logs del Sistema</h3>
                            <p class="text-sm text-gray-600">Monitoreo y registros</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Eventos hoy</span>
                            <span class="text-sm font-medium text-gray-900">127</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Errores</span>
                            <span class="text-sm font-medium text-red-600">3</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-sm text-gray-600">Advertencias</span>
                            <span class="text-sm font-medium text-orange-600">8</span>
                        </div>
                    </div>
                    
                    <button class="w-full bg-amber-600 hover:bg-amber-700 text-white py-2.5 px-4 rounded-xl font-medium transition-colors duration-200 flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <span>Ver Logs</span>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Footer informativo -->
        <div class="mt-12 bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-100">
                <div class="flex items-center space-x-3">
                    <div class="h-8 w-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Centro de Control Petrotekno</h3>
                </div>
            </div>
            <div class="p-6">
                <p class="text-gray-700 leading-relaxed mb-4">
                    Desde este panel de configuración puedes gestionar todos los aspectos administrativos del sistema de control interno de Petrotekno. 
                    Las opciones están organizadas por módulos para facilitar la navegación y el mantenimiento del sistema.
                </p>
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <span>Última actualización: {{ now()->format('d/m/Y H:i') }}</span>
                    <span class="flex items-center space-x-2">
                        <div class="h-2 w-2 bg-green-500 rounded-full"></div>
                        <span>Sistema operativo</span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
