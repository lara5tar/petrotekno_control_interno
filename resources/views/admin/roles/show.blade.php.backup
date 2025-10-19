@extends('layouts.app')

@section('title', 'Detalles del Rol')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-lg">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.roles.index') }}" class="text-gray-600 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $role->nombre_rol }}</h1>
                        <p class="text-sm text-gray-600 mt-1">{{ $role->descripcion ?? 'Sin descripción' }}</p>
                    </div>
                </div>
                
                <div class="flex space-x-2">
                    @can('editar_roles')
                    <a href="{{ route('admin.roles.permissions', $role) }}" class="bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                        <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-3c0-.265.105-.52.293-.707l10-10a6 6 0 017.414 7.414z"></path>
                        </svg>
                        Gestionar Permisos
                    </a>
                    <a href="{{ route('admin.roles.edit', $role) }}" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                        <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Editar Rol
                    </a>
                    @endcan
                </div>
            </div>
        </div>

        <div class="p-6">
            <!-- Estadísticas del Rol -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ $role->permisos->count() }}</div>
                    <div class="text-sm text-blue-800 font-medium">Permisos Asignados</div>
                </div>
                
                <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold text-green-600">{{ $role->usuarios->count() }}</div>
                    <div class="text-sm text-green-800 font-medium">Usuarios Activos</div>
                </div>
                
                <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold text-purple-600">
                        {{ $role->permisos->groupBy(function($permission) {
                            $parts = explode('_', $permission->nombre_permiso);
                            return isset($parts[1]) ? $parts[1] : 'general';
                        })->count() }}
                    </div>
                    <div class="text-sm text-purple-800 font-medium">Módulos Cubiertos</div>
                </div>
                
                <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold text-orange-600">
                        {{ $role->created_at ? $role->created_at->diffForHumans() : 'N/A' }}
                    </div>
                    <div class="text-sm text-orange-800 font-medium">Creado</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Permisos del Rol -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-3c0-.265.105-.52.293-.707l10-10a6 6 0 017.414 7.414z"></path>
                        </svg>
                        Permisos Asignados ({{ $role->permisos->count() }})
                    </h3>
                    
                    @if($role->permisos->count() > 0)
                        <div class="max-h-96 overflow-y-auto space-y-4">
                            @php
                                $permissionsByModule = $role->permisos->groupBy(function($permission) {
                                    $parts = explode('_', $permission->nombre_permiso);
                                    return isset($parts[1]) ? $parts[1] : 'general';
                                });
                            @endphp
                            
                            @foreach($permissionsByModule as $module => $modulePermissions)
                                <div class="bg-white rounded-lg border border-gray-200 p-4">
                                    <h4 class="font-medium text-gray-900 capitalize mb-3 flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ ucfirst($module) }}
                                        <span class="ml-2 bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">{{ $modulePermissions->count() }}</span>
                                    </h4>
                                    <div class="space-y-2">
                                        @foreach($modulePermissions as $permission)
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                <div class="flex-1">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ ucfirst(str_replace('_', ' ', $permission->nombre_permiso)) }}
                                                    </div>
                                                    @if($permission->descripcion)
                                                        <div class="text-xs text-gray-500">{{ $permission->descripcion }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M8 14v20c0 4.418 7.163 8 16 8 1.381 0 2.721-.087 4-.252M8 14c0 4.418 7.163 8 16 8s16-3.582 16-8M8 14c0-4.418 7.163-8 16-8s16 3.582 16 8m0 0v14m-16-4c0 4.418 7.163 8 16 8 1.381 0 2.721-.087 4-.252" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Sin permisos asignados</h3>
                            <p class="mt-1 text-sm text-gray-500">Este rol no tiene permisos asignados actualmente.</p>
                        </div>
                    @endif
                </div>

                <!-- Usuarios con este Rol -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                        </svg>
                        Usuarios Asignados ({{ $role->usuarios->count() }})
                    </h3>
                    
                    @if($role->usuarios->count() > 0)
                        <div class="max-h-96 overflow-y-auto space-y-3">
                            @foreach($role->usuarios as $user)
                                <div class="bg-white rounded-lg border border-gray-200 p-4 flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $user->personal ? $user->personal->nombres . ' ' . $user->personal->apellidos : 'Usuario sin personal asignado' }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                        @if($user->personal)
                                            <div class="text-xs text-gray-400">
                                                {{ $user->personal->cargo ?? 'Sin cargo definido' }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-shrink-0">
                                        @if($user->deleted_at)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Inactivo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Activo
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Sin usuarios asignados</h3>
                            <p class="mt-1 text-sm text-gray-500">Ningún usuario tiene asignado este rol actualmente.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="text-sm text-blue-700">
                        <p><strong>Información del Rol:</strong></p>
                        <ul class="mt-2 space-y-1">
                            <li><strong>Creado:</strong> {{ $role->created_at ? $role->created_at->format('d/m/Y H:i') : 'N/A' }}</li>
                            <li><strong>Última actualización:</strong> {{ $role->updated_at ? $role->updated_at->format('d/m/Y H:i') : 'N/A' }}</li>
                            <li><strong>Estado:</strong> 
                                @if(in_array($role->nombre_rol, ['Admin', 'Administrador', 'Supervisor', 'Operador']))
                                    <span class="text-green-600 font-medium">Rol del Sistema</span>
                                @else
                                    <span class="text-blue-600 font-medium">Rol Personalizado</span>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
