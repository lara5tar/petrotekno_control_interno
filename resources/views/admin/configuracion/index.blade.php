@extends('layouts.app')

@section('title', 'Configuración del Sistema')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Configuración del Sistema</h1>
        <p class="text-gray-600 mt-2">Administra las configuraciones principales del sistema de control interno</p>
    </div>

    <!-- Botones de Acceso Rápido -->
    <div class="mb-8 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-200">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Acceso Rápido</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            @can('ver_roles')
            <a href="{{ route('admin.roles.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg text-center">
                <div class="flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Gestión de Roles</span>
                </div>
                <div class="text-xs text-blue-100 mt-1">{{ \App\Models\Role::count() }} roles</div>
            </a>
            @endcan

            @can('crear_roles')
            <a href="{{ route('admin.roles.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg text-center">
                <div class="flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>Crear Rol</span>
                </div>
                <div class="text-xs text-green-100 mt-1">Nuevo rol</div>
            </a>
            @endcan

            @can('ver_usuarios')
            <a href="#" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg text-center">
                <div class="flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m3 5.197H9m3 0a4 4 0 01-4-4m0 0V9a2 2 0 012-2h2a2 2 0 012 2v6a2 2 0 01-2 2H9z"></path>
                    </svg>
                    <span>Usuarios</span>
                </div>
                <div class="text-xs text-purple-100 mt-1">{{ \App\Models\User::count() }} usuarios</div>
            </a>
            @endcan

            @can('ver_permisos')
            <button onclick="showPermissionsModal()" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg text-center w-full">
                <div class="flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <span>Permisos</span>
                </div>
                <div class="text-xs text-yellow-100 mt-1">{{ \App\Models\Permission::count() }} permisos</div>
            </button>
            @endcan

        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <!-- Gestión de Roles y Permisos -->
        @can('ver_roles')
        <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 border-l-4 border-blue-500">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5">
                        <h3 class="text-lg font-medium text-gray-900">Gestión de Roles y Permisos</h3>
                        <p class="text-sm text-gray-500">Control de acceso y permisos del sistema</p>
                    </div>
                </div>
                
                <!-- Estadísticas Detalladas -->
                <div class="mt-6">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="text-center p-3 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ \App\Models\Role::count() }}</div>
                            <div class="text-gray-600 font-medium">Roles del Sistema</div>
                            <div class="text-xs text-gray-500 mt-1">Roles de acceso</div>
                        </div>
                        <div class="text-center p-3 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ \App\Models\Permission::count() }}</div>
                            <div class="text-gray-600 font-medium">Permisos</div>
                            <div class="text-xs text-gray-500 mt-1">Acciones disponibles</div>
                        </div>
                    </div>
                </div>

                <!-- Roles Destacados -->
                <div class="mt-4">
                    <div class="grid grid-cols-1 gap-2 text-xs">
                        @php
                            $rolesConUsuarios = \App\Models\Role::withCount('usuarios')->take(3)->get();
                        @endphp
                        @foreach($rolesConUsuarios as $rol)
                            <div class="flex justify-between items-center py-1 px-2 bg-gray-50 rounded">
                                <span class="font-medium text-gray-700">{{ $rol->nombre_rol }}</span>
                                <span class="text-gray-500">{{ $rol->usuarios_count }} usuarios</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Acciones Rápidas -->
                <div class="mt-6 space-y-2">
                    <a href="{{ route('admin.roles.index') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg font-medium transition-colors duration-200 inline-block">
                        <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Gestionar Roles
                    </a>
                    
                    @can('crear_roles')
                    <a href="{{ route('admin.roles.create') }}" class="w-full bg-green-100 hover:bg-green-200 text-green-700 text-center py-2 px-4 rounded-lg font-medium transition-colors duration-200 inline-block">
                        <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Crear Nuevo Rol
                    </a>
                    @endcan
                </div>
            </div>
        </div>
        @endcan

        <!-- Usuarios por Rol -->
        @can('ver_usuarios')
        <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 border-l-4 border-indigo-500">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5">
                        <h3 class="text-lg font-medium text-gray-900">Distribución de Usuarios</h3>
                        <p class="text-sm text-gray-500">Usuarios activos por rol del sistema</p>
                    </div>
                </div>
                
                <!-- Distribución por Roles -->
                <div class="mt-4">
                    @php
                        $rolesDistribution = \App\Models\Role::withCount('usuarios')->get();
                        $totalUsuarios = \App\Models\User::count();
                    @endphp
                    
                    @if($rolesDistribution->count() > 0)
                        <div class="space-y-3">
                            @foreach($rolesDistribution as $rol)
                                @php
                                    $porcentaje = $totalUsuarios > 0 ? round(($rol->usuarios_count / $totalUsuarios) * 100, 1) : 0;
                                @endphp
                                <div class="flex items-center justify-between cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors" 
                                     onclick="showRoleQuickInfo({{ $rol->id }}, '{{ $rol->nombre_rol }}')">
                                    <div class="flex-1">
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="font-medium text-gray-700">{{ $rol->nombre_rol }}</span>
                                            <span class="text-gray-500">{{ $rol->usuarios_count }} ({{ $porcentaje }}%)</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: {{ $porcentaje }}%"></div>
                                        </div>
                                    </div>
                                    <div class="ml-2">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="text-sm text-gray-600 text-center">
                                <strong>Total: {{ $totalUsuarios }} usuarios</strong> distribuidos en {{ $rolesDistribution->count() }} roles
                            </div>
                        </div>
                    @else
                        <div class="text-center text-gray-500 text-sm py-4">
                            No hay roles configurados
                        </div>
                    @endif
                </div>

                <!-- Acciones Rápidas -->
                <div class="mt-6 grid grid-cols-2 gap-2">
                    <a href="{{ route('admin.roles.index') }}" class="bg-indigo-100 hover:bg-indigo-200 text-indigo-700 text-center py-2 px-3 rounded-lg text-sm font-medium transition-colors duration-200">
                        Ver Roles
                    </a>
                    @can('asignar_roles')
                    <button onclick="window.location.href='#'" class="bg-green-100 hover:bg-green-200 text-green-700 text-center py-2 px-3 rounded-lg text-sm font-medium transition-colors duration-200">
                        Asignar Roles
                    </button>
                    @endcan
                </div>
            </div>
        </div>
        @endcan

        <!-- Gestión de Usuarios -->
        @can('ver_usuarios')
        <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-green-500 text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m3 5.197H9m3 0a4 4 0 01-4-4m0 0V9a2 2 0 012-2h2a2 2 0 012 2v6a2 2 0 01-2 2H9z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5">
                        <h3 class="text-lg font-medium text-gray-900">Gestión de Usuarios</h3>
                        <p class="text-sm text-gray-500">Administra usuarios del sistema</p>
                    </div>
                </div>
                <div class="mt-6">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ \App\Models\User::count() }}</div>
                            <div class="text-gray-500">Usuarios</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ \App\Models\User::whereNull('deleted_at')->count() }}</div>
                            <div class="text-gray-500">Activos</div>
                        </div>
                    </div>
                </div>
                <div class="mt-6">
                    <a href="#" class="w-full bg-green-600 hover:bg-green-700 text-white text-center py-2 px-4 rounded-lg font-medium transition-colors duration-200 inline-block">
                        Gestionar Usuarios
                    </a>
                </div>
            </div>
        </div>
        @endcan

        <!-- Resumen de Permisos por Módulo -->
        @can('ver_permisos')
        <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 border-l-4 border-yellow-500">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-yellow-500 text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5">
                        <h3 class="text-lg font-medium text-gray-900">Permisos por Módulo</h3>
                        <p class="text-sm text-gray-500">Distribución de permisos del sistema</p>
                    </div>
                </div>
                
                <!-- Permisos por Módulo -->
                <div class="mt-4">
                    @php
                        $permisosPorModulo = \App\Models\Permission::all()->groupBy(function($permiso) {
                            // Extraer el módulo del nombre del permiso
                            $partes = explode('_', $permiso->nombre_permiso);
                            return count($partes) > 1 ? implode('_', array_slice($partes, 1)) : 'General';
                        });
                    @endphp
                    
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        @foreach($permisosPorModulo->take(6) as $modulo => $permisos)
                            <div class="bg-yellow-50 p-3 rounded-lg">
                                <div class="font-semibold text-yellow-800 capitalize">
                                    {{ str_replace('_', ' ', $modulo) }}
                                </div>
                                <div class="text-yellow-600 mt-1">
                                    {{ $permisos->count() }} permisos
                                </div>
                                <div class="text-yellow-500 text-xs mt-1">
                                    @foreach($permisos->take(2) as $permiso)
                                        <div>• {{ ucfirst(explode('_', $permiso->nombre_permiso)[0]) }}</div>
                                    @endforeach
                                    @if($permisos->count() > 2)
                                        <div class="text-yellow-400">+{{ $permisos->count() - 2 }} más</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($permisosPorModulo->count() > 6)
                        <div class="mt-3 text-center text-xs text-gray-500">
                            Y {{ $permisosPorModulo->count() - 6 }} módulos más...
                        </div>
                    @endif
                </div>

                <!-- Estadísticas de Permisos -->
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="grid grid-cols-3 gap-4 text-center text-sm">
                        <div>
                            <div class="text-lg font-bold text-green-600">{{ \App\Models\Permission::count() }}</div>
                            <div class="text-gray-500 text-xs">Total Permisos</div>
                        </div>
                        <div>
                            <div class="text-lg font-bold text-blue-600">{{ $permisosPorModulo->count() }}</div>
                            <div class="text-gray-500 text-xs">Módulos</div>
                        </div>
                        <div>
                            @php
                                $permisosAsignados = \App\Models\RolePermission::distinct('permiso_id')->count();
                            @endphp
                            <div class="text-lg font-bold text-purple-600">{{ $permisosAsignados }}</div>
                            <div class="text-gray-500 text-xs">Asignados</div>
                        </div>
                    </div>
                </div>

                <!-- Acciones Rápidas -->
                <div class="mt-4">
                    <a href="{{ route('admin.roles.index') }}" class="w-full bg-yellow-100 hover:bg-yellow-200 text-yellow-700 text-center py-2 px-4 rounded-lg text-sm font-medium transition-colors duration-200 inline-block">
                        <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Gestionar Permisos
                    </a>
                </div>
            </div>
        </div>
        @endcan

        <!-- Categorías de Personal -->
        @can('ver_personal')
        <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-purple-500 text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5">
                        <h3 class="text-lg font-medium text-gray-900">Categorías de Personal</h3>
                        <p class="text-sm text-gray-500">Gestiona categorías y clasificaciones</p>
                    </div>
                </div>
                <div class="mt-6">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ \App\Models\CategoriaPersonal::count() }}</div>
                            <div class="text-gray-500">Categorías</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-indigo-600">{{ \App\Models\Personal::count() }}</div>
                            <div class="text-gray-500">Personal</div>
                        </div>
                    </div>
                </div>
                <div class="mt-6">
                    <a href="{{ route('categorias-personal.index') }}" class="w-full bg-purple-600 hover:bg-purple-700 text-white text-center py-2 px-4 rounded-lg font-medium transition-colors duration-200 inline-block">
                        Gestionar Categorías
                    </a>
                </div>
            </div>
        </div>
        @endcan

        <!-- Logs del Sistema -->
        @can('ver_logs')
        <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-yellow-500 text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5">
                        <h3 class="text-lg font-medium text-gray-900">Logs de Auditoría</h3>
                        <p class="text-sm text-gray-500">Revisa registros de actividad</p>
                    </div>
                </div>
                <div class="mt-6">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-600">{{ \App\Models\LogAccion::count() }}</div>
                            <div class="text-gray-500">Registros</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-600">{{ \App\Models\LogAccion::whereDate('created_at', today())->count() }}</div>
                            <div class="text-gray-500">Hoy</div>
                        </div>
                    </div>
                </div>
                <div class="mt-6">
                    <a href="#" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white text-center py-2 px-4 rounded-lg font-medium transition-colors duration-200 inline-block">
                        Ver Logs
                    </a>
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

        <!-- Respaldos y Mantenimiento -->
        <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-red-500 text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5">
                        <h3 class="text-lg font-medium text-gray-900">Respaldos</h3>
                        <p class="text-sm text-gray-500">Gestiona respaldos del sistema</p>
                    </div>
                </div>
                <div class="mt-6">
                    <div class="text-sm text-gray-600 space-y-2">
                        <div class="flex justify-between">
                            <span>Último respaldo:</span>
                            <span class="font-medium">Ayer</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Estado:</span>
                            <span class="font-medium text-green-600">Activo</span>
                        </div>
                    </div>
                </div>
                <div class="mt-6">
                    <button class="w-full bg-red-600 hover:bg-red-700 text-white text-center py-2 px-4 rounded-lg font-medium transition-colors duration-200">
                        Gestionar Respaldos
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

<!-- Modal para información rápida de roles -->
<div id="roleQuickInfoModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3">
            <h3 class="text-lg font-bold text-gray-900" id="modalRoleTitle">Información del Rol</h3>
            <button onclick="closeRoleQuickInfo()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div id="modalRoleContent" class="mt-4">
            <div class="animate-pulse">
                <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                <div class="h-4 bg-gray-200 rounded w-1/2 mb-2"></div>
                <div class="h-4 bg-gray-200 rounded w-5/6"></div>
            </div>
        </div>
        
        <div class="flex justify-end pt-4 space-x-2">
            <button onclick="closeRoleQuickInfo()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                Cerrar
            </button>
            <a id="manageRoleLink" href="#" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Gestionar Rol
            </a>
        </div>
    </div>
</div>

<!-- Modal para listado de permisos -->
<div id="permissionsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-lg font-bold text-gray-900">Permisos del Sistema</h3>
            <button onclick="closePermissionsModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="mt-4 max-h-96 overflow-y-auto">
            @php
                $todosLosPermisos = \App\Models\Permission::all()->groupBy(function($permiso) {
                    $partes = explode('_', $permiso->nombre_permiso);
                    return count($partes) > 1 ? implode('_', array_slice($partes, 1)) : 'General';
                });
            @endphp
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($todosLosPermisos as $modulo => $permisos)
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-800 mb-3 capitalize border-b border-gray-200 pb-2">
                        {{ str_replace('_', ' ', $modulo) }}
                        <span class="text-sm text-gray-500 font-normal">({{ $permisos->count() }})</span>
                    </h4>
                    <div class="space-y-1">
                        @foreach($permisos as $permiso)
                        <div class="flex items-center text-sm">
                            <div class="w-2 h-2 bg-blue-400 rounded-full mr-2"></div>
                            <span class="text-gray-700">{{ ucfirst(str_replace('_', ' ', $permiso->nombre_permiso)) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="mt-6 pt-4 border-t border-gray-200 text-center">
                <div class="text-sm text-gray-600">
                    <strong>Total: {{ \App\Models\Permission::count() }} permisos</strong> distribuidos en {{ $todosLosPermisos->count() }} módulos
                </div>
            </div>
        </div>
        
        <div class="flex justify-between pt-4 border-t">
            <button onclick="closePermissionsModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                Cerrar
            </button>
            @can('ver_roles')
            <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Gestionar en Roles
            </a>
            @endcan
        </div>
    </div>
</div>

<!-- Script para interactividad de las tarjetas -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animar las barras de progreso de distribución de usuarios
    const progressBars = document.querySelectorAll('.bg-indigo-600');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 300);
    });

    // Efecto hover mejorado para las tarjetas
    const cards = document.querySelectorAll('.bg-white.rounded-lg.shadow-lg');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.transition = 'all 0.3s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Tooltip para información adicional
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'absolute z-50 px-2 py-1 text-xs text-white bg-gray-900 rounded shadow-lg';
            tooltip.textContent = this.getAttribute('data-tooltip');
            tooltip.style.bottom = '100%';
            tooltip.style.left = '50%';
            tooltip.style.transform = 'translateX(-50%)';
            tooltip.style.marginBottom = '5px';
            
            this.style.position = 'relative';
            this.appendChild(tooltip);
        });
        
        element.addEventListener('mouseleave', function() {
            const tooltip = this.querySelector('.absolute.z-50');
            if (tooltip) {
                tooltip.remove();
            }
        });
    });

    // Actualización automática de estadísticas cada 30 segundos
    setInterval(function() {
        // Solo actualizar si el usuario está en la página y la página está visible
        if (!document.hidden) {
            const timestamp = document.querySelector('strong:contains("Última actualización:")');
            if (timestamp && timestamp.parentElement) {
                timestamp.parentElement.innerHTML = timestamp.parentElement.innerHTML.replace(
                    /\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}/,
                    new Date().toLocaleString('es-ES', {
                        day: '2-digit',
                        month: '2-digit', 
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    })
                );
            }
        }
    }, 30000);

    // Confirmación para acciones críticas
    const criticalButtons = document.querySelectorAll('button[onclick*="delete"], a[href*="delete"]');
    criticalButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('¿Estás seguro de que deseas realizar esta acción?')) {
                e.preventDefault();
                return false;
            }
        });
    });
});

// Función para mostrar información rápida del rol
function showRoleQuickInfo(roleId, roleName) {
    const modal = document.getElementById('roleQuickInfoModal');
    const title = document.getElementById('modalRoleTitle');
    const content = document.getElementById('modalRoleContent');
    const manageLink = document.getElementById('manageRoleLink');
    
    // Mostrar modal y loading
    title.textContent = `Información del Rol: ${roleName}`;
    manageLink.href = `{{ route('admin.roles.index') }}/${roleId}`;
    modal.classList.remove('hidden');
    
    // Mostrar loading
    content.innerHTML = `
        <div class="animate-pulse">
            <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
            <div class="h-4 bg-gray-200 rounded w-1/2 mb-2"></div>
            <div class="h-4 bg-gray-200 rounded w-5/6 mb-4"></div>
            <div class="grid grid-cols-2 gap-4">
                <div class="h-20 bg-gray-200 rounded"></div>
                <div class="h-20 bg-gray-200 rounded"></div>
            </div>
        </div>
    `;
    
    // Cargar datos del rol
    fetch(`{{ route('admin.roles.index') }}/${roleId}/quick-info`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayRoleInfo(data.data);
            } else {
                content.innerHTML = `
                    <div class="text-red-600 text-center py-4">
                        <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p>Error al cargar la información del rol</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            content.innerHTML = `
                <div class="text-red-600 text-center py-4">
                    <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p>Error de conexión</p>
                </div>
            `;
        });
}

// Función para mostrar la información del rol en el modal
function displayRoleInfo(data) {
    const content = document.getElementById('modalRoleContent');
    
    const usuariosHtml = data.usuarios.length > 0 
        ? data.usuarios.map(user => `
            <div class="flex justify-between items-center py-2 px-3 bg-gray-50 rounded">
                <div>
                    <div class="font-medium text-sm">${user.name}</div>
                    <div class="text-xs text-gray-500">${user.email}</div>
                </div>
                <div class="text-xs text-gray-400">${user.last_login}</div>
            </div>
        `).join('')
        : '<div class="text-gray-500 text-center py-4">No hay usuarios asignados</div>';
    
    const modulosHtml = data.modulos_permisos.length > 0
        ? data.modulos_permisos.map(modulo => `
            <div class="bg-blue-50 p-3 rounded">
                <div class="font-medium text-blue-800">${modulo.modulo}</div>
                <div class="text-sm text-blue-600">${modulo.count} permisos</div>
            </div>
        `).join('')
        : '<div class="text-gray-500 text-center py-4">No hay permisos asignados</div>';
    
    content.innerHTML = `
        <div class="space-y-6">
            <!-- Información básica -->
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">${data.role.usuarios_count}</div>
                    <div class="text-sm text-gray-600">Usuarios</div>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">${data.role.permisos_count}</div>
                    <div class="text-sm text-gray-600">Permisos</div>
                </div>
            </div>
            
            <!-- Descripción -->
            ${data.role.descripcion ? `
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">Descripción</h4>
                    <p class="text-sm text-gray-600">${data.role.descripcion}</p>
                </div>
            ` : ''}
            
            <!-- Usuarios asignados -->
            <div>
                <h4 class="font-medium text-gray-900 mb-3">Usuarios asignados (${data.role.usuarios_count})</h4>
                <div class="max-h-40 overflow-y-auto space-y-2">
                    ${usuariosHtml}
                </div>
            </div>
            
            <!-- Módulos de permisos -->
            <div>
                <h4 class="font-medium text-gray-900 mb-3">Módulos con permisos (${data.modulos_permisos.length})</h4>
                <div class="grid grid-cols-2 gap-2 max-h-40 overflow-y-auto">
                    ${modulosHtml}
                </div>
            </div>
            
            <!-- Información adicional -->
            <div class="text-xs text-gray-500 pt-2 border-t">
                Rol creado el ${data.role.created_at}
            </div>
        </div>
    `;
}

// Función para cerrar el modal
function closeRoleQuickInfo() {
    const modal = document.getElementById('roleQuickInfoModal');
    modal.classList.add('hidden');
}

// Función para mostrar el modal de permisos
function showPermissionsModal() {
    const modal = document.getElementById('permissionsModal');
    modal.classList.remove('hidden');
}

// Función para cerrar el modal de permisos
function closePermissionsModal() {
    const modal = document.getElementById('permissionsModal');
    modal.classList.add('hidden');
}

// Cerrar modal al hacer clic fuera de él
document.addEventListener('click', function(e) {
    const roleModal = document.getElementById('roleQuickInfoModal');
    const permissionsModal = document.getElementById('permissionsModal');
    
    if (e.target === roleModal) {
        closeRoleQuickInfo();
    }
    
    if (e.target === permissionsModal) {
        closePermissionsModal();
    }
});
</script>
@endsection
