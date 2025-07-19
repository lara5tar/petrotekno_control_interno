@extends('layouts.app')

@section('title', 'Control de Usuarios')

@section('header', 'Control de Usuarios de Petrotekno')

@section('content')
<div class="bg-white min-h-screen">
    <!-- Header con navegación -->
    <div class="bg-petrodark text-white p-4 flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <button class="text-white hover:text-petroyellow">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <h1 class="text-lg font-semibold">Control de Usuarios de Petrotekno</h1>
            <span class="text-sm bg-gray-700 px-2 py-1 rounded">v1.0</span>
        </div>
        <div class="text-sm">
            <span class="bg-petroyellow text-petrodark px-3 py-1 rounded font-medium">MARCO DELGADO</span>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="bg-gray-100 px-4 py-2 text-sm text-gray-600">
        Gestionar Usuarios / Marco Delgado [admin001]
    </div>

    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 bg-petroyellow p-4">
            <div class="mb-8">
                <img src="/images/petrotekno-logo.png" alt="Petrotekno" class="w-32 mx-auto">
            </div>
            
            <div class="text-center mb-8">
                <h3 class="font-bold text-petrodark text-lg">Menú Principal</h3>
            </div>
            
            <nav class="space-y-2">
                <a href="#" class="flex items-center space-x-3 text-petrodark hover:bg-yellow-400 p-3 rounded">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Gestionar Usuarios</span>
                </a>
                <a href="#" class="flex items-center space-x-3 text-petrodark hover:bg-yellow-400 p-3 rounded">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 001 1h6a1 1 0 001-1V3a2 2 0 012 2v6.5a1.5 1.5 0 01-1.5 1.5h-7A1.5 1.5 0 015 11.5V5z" clip-rule="evenodd" />
                    </svg>
                    <span>Reportes</span>
                </a>
                <a href="#" class="flex items-center space-x-3 text-petrodark hover:bg-yellow-400 p-3 rounded">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                    </svg>
                    <span>Configuración</span>
                </a>
            </nav>

            <div class="mt-auto pt-8">
                <a href="#" class="flex items-center space-x-3 text-petrodark hover:bg-yellow-400 p-3 rounded">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" />
                    </svg>
                    <span>Cerrar Sesión</span>
                </a>
            </div>
        </div>

        <!-- Contenido Principal -->
        <div class="flex-1 p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Panel Izquierdo - Datos Generales -->
                <div class="space-y-6">
                    <!-- Datos Generales -->
                    <div class="bg-white border border-gray-300 rounded-lg">
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                            <h3 class="font-semibold text-gray-800">Datos Generales</h3>
                        </div>
                        <div class="p-4 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Usuario</label>
                                    <div class="bg-blue-600 text-white px-3 py-2 rounded text-sm font-medium">
                                        marco.delgado
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Rol</label>
                                    <div class="bg-blue-600 text-white px-3 py-2 rounded text-sm font-medium">
                                        Administrador
                                    </div>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">ID Usuario</label>
                                    <div class="bg-blue-600 text-white px-3 py-2 rounded text-sm font-medium">
                                        USR001
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Estado</label>
                                    <div class="bg-green-600 text-white px-3 py-2 rounded text-sm font-medium flex items-center">
                                        <span class="w-2 h-2 bg-green-300 rounded-full mr-2"></span>
                                        Activo
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Email</label>
                                    <div class="bg-blue-600 text-white px-3 py-2 rounded text-sm font-medium">
                                        marco.delgado@petrotekno.com
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Personal Asociado</label>
                                    <div class="bg-blue-600 text-white px-3 py-2 rounded text-sm font-medium">
                                        Marco Delgado Reyes
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Último Acceso</label>
                                    <div class="bg-blue-600 text-white px-3 py-2 rounded text-sm font-medium">
                                        19/07/2025 09:45
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Foto de Perfil -->
                    <div class="bg-white border border-gray-300 rounded-lg">
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                            <h3 class="font-semibold text-gray-800">Foto de Perfil</h3>
                        </div>
                        <div class="p-4">
                            <div class="bg-gray-200 rounded-lg h-64 flex items-center justify-center">
                                <div class="text-center">
                                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                    <p class="text-gray-500 text-sm">Foto de Usuario</p>
                                    <p class="text-gray-400 text-xs">marco.delgado</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-between p-4 border-t border-gray-200">
                            <button class="bg-gray-600 text-white px-4 py-2 rounded text-sm hover:bg-gray-700">
                                Regresar
                            </button>
                            <button class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Guardar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Panel Derecho - Información Adicional -->
                <div class="space-y-6">
                    <!-- Estado Actual -->
                    <div class="bg-white border border-gray-300 rounded-lg">
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                            <h3 class="font-semibold text-gray-800">Último Acceso</h3>
                        </div>
                        <div class="p-4">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-gray-800 mb-2">09:45</div>
                                <div class="text-sm text-gray-600">19/07/2025</div>
                                <div class="bg-green-500 text-white px-4 py-2 rounded mt-3 font-medium">
                                    En Línea
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs de Información -->
                    <div class="bg-white border border-gray-300 rounded-lg">
                        <div class="bg-gray-50 px-4 py-0 border-b border-gray-300">
                            <div class="flex space-x-0">
                                <button class="px-4 py-3 text-sm font-medium border-b-2 border-blue-600 text-blue-600 bg-white">
                                    Permisos
                                </button>
                                <button class="px-4 py-3 text-sm font-medium text-gray-600 hover:text-gray-800">
                                    Actividad
                                </button>
                                <button class="px-4 py-3 text-sm font-medium text-gray-600 hover:text-gray-800">
                                    Sesiones
                                </button>
                            </div>
                        </div>
                        
                        <!-- Contenido de Permisos -->
                        <div class="p-4">
                            <!-- Rol y Permisos -->
                            <div class="mb-6">
                                <h4 class="font-medium text-gray-700 mb-3">Rol Asignado</h4>
                                <div class="space-y-3">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs text-gray-600">Rol Principal</label>
                                            <div class="bg-gray-600 text-white px-2 py-1 rounded text-sm">
                                                Administrador
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600">Fecha Asignación</label>
                                            <div class="bg-gray-600 text-white px-2 py-1 rounded text-sm">
                                                15/01/2025
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 gap-4">
                                        <div>
                                            <label class="block text-xs text-gray-600">Descripción del Rol</label>
                                            <div class="bg-gray-600 text-white px-2 py-1 rounded text-sm">
                                                Acceso completo al sistema
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Permisos Específicos -->
                            <div class="mb-6">
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="font-medium text-gray-700">Permisos Específicos</h4>
                                    <button class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                                        Editar Permisos
                                    </button>
                                </div>
                                
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between p-2 bg-green-50 rounded">
                                        <span class="text-sm text-gray-700">Gestión de Personal</span>
                                        <span class="bg-green-600 text-white px-2 py-1 rounded text-xs">Permitido</span>
                                    </div>
                                    <div class="flex items-center justify-between p-2 bg-green-50 rounded">
                                        <span class="text-sm text-gray-700">Gestión de Vehículos</span>
                                        <span class="bg-green-600 text-white px-2 py-1 rounded text-xs">Permitido</span>
                                    </div>
                                    <div class="flex items-center justify-between p-2 bg-green-50 rounded">
                                        <span class="text-sm text-gray-700">Gestión de Documentos</span>
                                        <span class="bg-green-600 text-white px-2 py-1 rounded text-xs">Permitido</span>
                                    </div>
                                    <div class="flex items-center justify-between p-2 bg-green-50 rounded">
                                        <span class="text-sm text-gray-700">Administración del Sistema</span>
                                        <span class="bg-green-600 text-white px-2 py-1 rounded text-xs">Permitido</span>
                                    </div>
                                    <div class="flex items-center justify-between p-2 bg-red-50 rounded">
                                        <span class="text-sm text-gray-700">Eliminación de Registros</span>
                                        <span class="bg-red-600 text-white px-2 py-1 rounded text-xs">Denegado</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Historial de Actividad -->
                            <div>
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="font-medium text-gray-700">Actividad Reciente</h4>
                                    <button class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                                        Ver Historial
                                    </button>
                                </div>

                                <div class="bg-gray-100 rounded overflow-hidden">
                                    <table class="w-full text-sm">
                                        <thead class="bg-gray-600 text-white">
                                            <tr>
                                                <th class="px-3 py-2 text-left">Acción</th>
                                                <th class="px-3 py-2 text-left">Fecha</th>
                                                <th class="px-3 py-2 text-left">IP</th>
                                                <th class="px-3 py-2 text-left">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-gray-700">
                                            <tr class="border-b border-gray-300">
                                                <td class="px-3 py-2">Inicio de Sesión</td>
                                                <td class="px-3 py-2">19/07/2025 09:45</td>
                                                <td class="px-3 py-2">192.168.1.100</td>
                                                <td class="px-3 py-2">
                                                    <span class="bg-green-600 text-white px-2 py-1 rounded text-xs">Exitoso</span>
                                                </td>
                                            </tr>
                                            <tr class="border-b border-gray-300">
                                                <td class="px-3 py-2">Crear Personal</td>
                                                <td class="px-3 py-2">19/07/2025 09:30</td>
                                                <td class="px-3 py-2">192.168.1.100</td>
                                                <td class="px-3 py-2">
                                                    <span class="bg-green-600 text-white px-2 py-1 rounded text-xs">Exitoso</span>
                                                </td>
                                            </tr>
                                            <tr class="border-b border-gray-300">
                                                <td class="px-3 py-2">Editar Vehículo</td>
                                                <td class="px-3 py-2">18/07/2025 16:22</td>
                                                <td class="px-3 py-2">192.168.1.100</td>
                                                <td class="px-3 py-2">
                                                    <span class="bg-green-600 text-white px-2 py-1 rounded text-xs">Exitoso</span>
                                                </td>
                                            </tr>
                                            <tr class="border-b border-gray-300">
                                                <td class="px-3 py-2">Subir Documento</td>
                                                <td class="px-3 py-2">18/07/2025 14:15</td>
                                                <td class="px-3 py-2">192.168.1.100</td>
                                                <td class="px-3 py-2">
                                                    <span class="bg-green-600 text-white px-2 py-1 rounded text-xs">Exitoso</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="px-3 py-2">Cerrar Sesión</td>
                                                <td class="px-3 py-2">17/07/2025 18:00</td>
                                                <td class="px-3 py-2">192.168.1.100</td>
                                                <td class="px-3 py-2">
                                                    <span class="bg-green-600 text-white px-2 py-1 rounded text-xs">Exitoso</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .container-responsive {
        max-width: 100%;
        padding: 0;
    }
    
    body {
        background-color: #f5f5f5;
    }
</style>
@endpush
