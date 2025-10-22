@extends('layouts.app')

@section('title', 'Control de Personal')

@section('header', 'Control de Personal de Solupatch')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
{{-- Breadcrumb --}}
<x-breadcrumb :items="[
    ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
    ['label' => 'Personal', 'url' => route('personal.index')],
    ['label' => $personal->nombre_completo ?? 'Personal']
]" />

<!-- Contenido Principal -->
<div class="p-6">
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
                            <label class="block text-sm font-medium text-gray-600">Nombre</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $personal->nombre_completo }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Puesto</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $personal->categoria->nombre_categoria ?? 'Sin puesto' }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">ID Empleado</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ str_pad($personal->id, 4, '0', STR_PAD_LEFT) }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Estatus</label>
                            <div class="bg-{{ $personal->estatus === 'activo' ? 'green' : 'red' }}-600 text-white px-3 py-2 rounded text-sm font-medium flex items-center">
                                <span class="w-2 h-2 bg-{{ $personal->estatus === 'activo' ? 'green' : 'red' }}-300 rounded-full mr-2"></span>
                                {{ ucfirst($personal->estatus) }}
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">CURP</label>
                            <div class="flex items-center space-x-2">
                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium flex-1">
                                    {{ $personal->curp_numero ?? 'No registrado' }}
                                </div>
                                @php
                                    $documentoCURP = $documentosPorTipo['curp'] ?? $documentosPorTipo['CURP'] ?? null;
                                    $tieneDocumentoCURP = !is_null($documentoCURP) && is_object($documentoCURP);
                                @endphp
                                @if($tieneDocumentoCURP)
                                <button class="bg-green-600 hover:bg-green-700 text-white p-2 rounded text-sm transition duration-200 flex items-center" 
                                        onclick="viewPersonalDocument('{{ $documentoCURP->url_documento }}')"
                                        title="Ver archivo adjunto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                </button>
                                @elseif($personal->url_curp)
                                <button class="bg-green-600 hover:bg-green-700 text-white p-2 rounded text-sm transition duration-200 flex items-center" 
                                        onclick="viewPersonalDocument('{{ asset('storage/' . $personal->url_curp) }}')"
                                        title="Ver archivo adjunto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                </button>
                                @else
                                <span class="bg-red-600 text-white p-2 rounded text-sm text-xs">
                                    Sin archivo
                                </span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">RFC</label>
                            <div class="flex items-center space-x-2">
                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium flex-1">
                                    {{ $personal->rfc ?? 'No registrado' }}
                                </div>
                                @php
                                    $documentoRFC = $documentosPorTipo['rfc'] ?? $documentosPorTipo['RFC'] ?? null;
                                    $tieneDocumentoRFC = !is_null($documentoRFC) && is_object($documentoRFC);
                                @endphp
                                @if($tieneDocumentoRFC)
                                <button class="bg-green-600 hover:bg-green-700 text-white p-2 rounded text-sm transition duration-200 flex items-center" 
                                        onclick="viewPersonalDocument('{{ $documentoRFC->url_documento }}')"
                                        title="Ver archivo adjunto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                </button>
                                @elseif($personal->url_rfc)
                                <button class="bg-green-600 hover:bg-green-700 text-white p-2 rounded text-sm transition duration-200 flex items-center" 
                                        onclick="viewPersonalDocument('{{ asset('storage/' . $personal->url_rfc) }}')"
                                        title="Ver archivo adjunto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                </button>
                                @else
                                <span class="bg-red-600 text-white p-2 rounded text-sm text-xs">
                                    Sin archivo
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Identificación (INE)</label>
                            <div class="flex items-center space-x-2">
                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium flex-1">
                                    {{ $personal->ine ?? 'No registrado' }}
                                </div>
                                @php
                                    $documentoINE = $documentosPorTipo['identificacion'] ?? $documentosPorTipo['INE'] ?? null;
                                    $tieneDocumentoINE = !is_null($documentoINE) && is_object($documentoINE);
                                @endphp
                                @if($tieneDocumentoINE)
                                <button class="bg-green-600 hover:bg-green-700 text-white p-2 rounded text-sm transition duration-200 flex items-center" 
                                        onclick="viewPersonalDocument('{{ $documentoINE->url_documento }}')"
                                        title="Ver archivo adjunto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                </button>
                                @elseif($personal->url_ine)
                                <button class="bg-green-600 hover:bg-green-700 text-white p-2 rounded text-sm transition duration-200 flex items-center" 
                                        onclick="viewPersonalDocument('{{ asset('storage/' . $personal->url_ine) }}')"
                                        title="Ver archivo adjunto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                </button>
                                @else
                                <span class="bg-red-600 text-white p-2 rounded text-sm text-xs">
                                    Sin archivo
                                </span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">NSS</label>
                            <div class="flex items-center space-x-2">
                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium flex-1">
                                    {{ $personal->nss ?? 'No registrado' }}
                                </div>
                                @php
                                    $documentoNSS = $documentosPorTipo['nss'] ?? $documentosPorTipo['NSS'] ?? null;
                                    $tieneDocumentoNSS = !is_null($documentoNSS) && is_object($documentoNSS);
                                @endphp
                                @if($tieneDocumentoNSS)
                                <button class="bg-green-600 hover:bg-green-700 text-white p-2 rounded text-sm transition duration-200 flex items-center" 
                                        onclick="viewPersonalDocument('{{ $documentoNSS->url_documento }}')"
                                        title="Ver archivo adjunto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                </button>
                                @elseif($personal->url_nss)
                                <button class="bg-green-600 hover:bg-green-700 text-white p-2 rounded text-sm transition duration-200 flex items-center" 
                                        onclick="viewPersonalDocument('{{ asset('storage/' . $personal->url_nss) }}')"
                                        title="Ver archivo adjunto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                </button>
                                @else
                                <span class="bg-red-600 text-white p-2 rounded text-sm text-xs">
                                    Sin archivo
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Licencia de Manejo</label>
                            <div class="flex items-center space-x-2">
                                @php
                                    $documentoLicencia = $documentosPorTipo['licencia'] ?? $documentosPorTipo['Licencia de Manejo'] ?? null;
                                    $tieneDocumentoLicencia = !is_null($documentoLicencia) && is_object($documentoLicencia);
                                    $tieneArchivoLicencia = !empty($personal->url_licencia);
                                    $tieneAlgunaLicencia = $tieneDocumentoLicencia || $tieneArchivoLicencia;
                                @endphp
                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium flex-1">
                                    {{ $personal->no_licencia ?? 'No registrado' }}
                                </div>
                                @if($tieneDocumentoLicencia)
                                <button class="bg-green-600 hover:bg-green-700 text-white p-2 rounded text-sm transition duration-200 flex items-center" 
                                        onclick="viewPersonalDocument('{{ $documentoLicencia->url_documento }}')"
                                        title="Ver archivo adjunto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                </button>
                                @elseif($tieneArchivoLicencia)
                                <button class="bg-green-600 hover:bg-green-700 text-white p-2 rounded text-sm transition duration-200 flex items-center" 
                                        onclick="viewPersonalDocument('{{ asset('storage/' . $personal->url_licencia) }}')"
                                        title="Ver archivo adjunto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                </button>
                                @else
                                <span class="bg-red-600 text-white p-2 rounded text-sm text-xs">
                                    Sin archivo
                                </span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Dirección</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $personal->direccion ?? 'No registrado' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información Laboral -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 text-gray-700" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                        </svg>
                        Información Laboral
                    </h3>
                </div>
                <div class="p-4 space-y-4">
                    {{-- Cuenta Bancaria --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Cuenta Bancaria</label>
                        <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                            {{ $personal->cuenta_bancaria ?? 'No registrado' }}
                        </div>
                    </div>

                    {{-- Fechas Laborales --}}
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Fecha de Inicio Laboral -->
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Fecha de Inicio Laboral</label>
                            <div class="flex items-center space-x-2">
                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium flex-1">
                                    {{ $personal->fecha_inicio_laboral ? $personal->fecha_inicio_laboral->format('d/m/Y') : 'No registrado' }}
                                </div>
                                @if($personal->url_inicio_laboral)
                                    <button class="bg-green-600 hover:bg-green-700 text-white p-2 rounded text-sm transition duration-200 flex items-center" 
                                            onclick="viewPersonalDocument('{{ asset('storage/' . $personal->url_inicio_laboral) }}')"
                                            title="Ver documento de inicio laboral">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                        </svg>
                                    </button>
                                @else
                                    <span class="bg-red-600 text-white p-2 rounded text-sm text-xs">
                                        Sin archivo
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Fecha de Término Laboral -->
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Fecha de Término Laboral</label>
                            <div class="flex items-center space-x-2">
                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium flex-1">
                                    {{ $personal->fecha_termino_laboral ? $personal->fecha_termino_laboral->format('d/m/Y') : 'No registrado' }}
                                </div>
                                @if($personal->url_termino_laboral)
                                    <button class="bg-green-600 hover:bg-green-700 text-white p-2 rounded text-sm transition duration-200 flex items-center" 
                                            onclick="viewPersonalDocument('{{ asset('storage/' . $personal->url_termino_laboral) }}')"
                                            title="Ver documento de término laboral">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                        </svg>
                                    </button>
                                @else
                                    <span class="bg-red-600 text-white p-2 rounded text-sm text-xs">
                                        Sin archivo
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Antigüedad (si hay fecha de inicio) --}}
                    @if($personal->fecha_inicio_laboral)
                        <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700">Antigüedad</span>
                                </div>
                                <span class="text-sm font-bold text-blue-700">
                                    @php
                                        $fechaInicio = $personal->fecha_inicio_laboral;
                                        $fechaFin = $personal->fecha_termino_laboral ?? now();
                                        $diff = $fechaInicio->diff($fechaFin);
                                        
                                        $years = $diff->y;
                                        $months = $diff->m;
                                        $days = $diff->d;
                                        
                                        $antiguedad = [];
                                        if ($years > 0) {
                                            $antiguedad[] = $years . ($years == 1 ? ' año' : ' años');
                                        }
                                        if ($months > 0) {
                                            $antiguedad[] = $months . ($months == 1 ? ' mes' : ' meses');
                                        }
                                        if ($days > 0 && $years == 0) {
                                            $antiguedad[] = $days . ($days == 1 ? ' día' : ' días');
                                        }
                                        
                                        echo !empty($antiguedad) ? implode(', ', $antiguedad) : 'Menos de un día';
                                    @endphp
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Datos de Usuario del Sistema -->
            @if($personal->usuario)
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-blue-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Datos de Usuario del Sistema
                    </h3>
                </div>
                <div class="p-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Correo Electrónico</label>
                            <div class="bg-blue-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $personal->usuario->email }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Rol en el Sistema</label>
                            <div class="bg-blue-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $personal->usuario->rol->nombre_rol ?? 'Sin rol asignado' }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">ID de Usuario</label>
                            <div class="bg-blue-600 text-white px-3 py-2 rounded text-sm font-medium">
                                #{{ str_pad($personal->usuario->id, 4, '0', STR_PAD_LEFT) }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Fecha de Creación</label>
                            <div class="bg-blue-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $personal->usuario->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Último Acceso</label>
                            <div class="bg-blue-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $personal->usuario->last_login_at ? $personal->usuario->last_login_at->format('d/m/Y H:i') : 'Nunca ha iniciado sesión' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Usuario del Sistema
                    </h3>
                </div>
                <div class="p-4">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.232 13.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <p class="text-yellow-800 font-medium">Este personal no tiene usuario del sistema</p>
                        </div>
                        <p class="text-yellow-700 text-sm mt-2">
                            Para crear un usuario, edite este registro y marque la opción "Crear usuario para acceso al sistema".
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Panel Derecho - Información Adicional -->
        <div class="space-y-6">
            <!-- Tabs de Información - Diseño tipo carpetas -->
            <div class="bg-white flex-1 flex flex-col" id="tabs-container">
                <div class="relative">
                    <nav class="flex space-x-1 pr-3 pt-3">
                        <button onclick="switchTab('documentos')" 
                                id="documentos-tab"
                                class="relative px-4 py-2 text-sm font-medium rounded-t-lg transition-all duration-200 border-b-0 ml-0 bg-gray-50 border-gray-300 border-t border-l border-r text-gray-800 shadow-sm z-10"
                                role="tab" 
                                aria-selected="true"
                                aria-controls="documentos-content">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                 </svg>
                                Documentos
                            </span>
                        </button>
                    </nav>
                    <!-- Línea base que conecta con las pestañas -->
                    <div class="absolute bottom-0 left-0 right-0 h-px bg-gray-300 z-0"></div>
                </div>

                <!-- Contenido de pestañas con bordes tipo carpeta -->
                <div class="flex-1 overflow-hidden bg-gray-50 border-l border-r border-b border-gray-300 rounded-b-lg">
                <div class="flex-1 overflow-hidden">
                
                {{-- <!-- Contenido de Asignación -->
                <div id="asignacion-content" class="tab-content p-4" role="tabpanel" aria-labelledby="asignacion-tab">
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-700 mb-3">Obra Actual</h4>
                        <div class="space-y-3">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-600">Nombre de Obra</label>
                                    <div class="bg-gray-600 text-white px-2 py-1 rounded text-sm">
                                        Mantenimiento Vial Monterrey
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600">Ubicación</label>
                                    <div class="bg-gray-600 text-white px-2 py-1 rounded text-sm">
                                        Monterrey, N.L.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-600">Puesto Asignado</label>
                                    <div class="bg-gray-600 text-white px-2 py-1 rounded text-sm">
                                        Técnico Especializado
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600">Supervisor</label>
                                    <div class="bg-gray-600 text-white px-2 py-1 rounded text-sm">
                                        Ing. Carlos López
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-600">Fecha Inicio</label>
                                    <div class="bg-gray-600 text-white px-2 py-1 rounded text-sm">
                                        15/07/2025
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600">Fecha Término</label>
                                    <div class="bg-gray-600 text-white px-2 py-1 rounded text-sm">
                                        15/12/2025
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Botón Ir a Obra -->
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md transition-colors duration-200 flex items-center justify-center" 
                                        title="Ver detalles de la obra">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    Ir a Obra
                                </button>
                            </div>
                        </div>
                    </div>
                </div> --}}

                <!-- Contenido de Documentos -->
                <div id="documentos-content" class="p-6 bg-gray-50 tab-content" role="tabpanel" aria-labelledby="documentos-tab" style="display: block;">
                    <div class="space-y-6">
                            <!-- Documentos del Personal -->
                        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                            <div class="flex justify-between items-center mb-4">
                                <h5 class="text-base font-semibold text-gray-800 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Documentos del Personal
                                </h5>
                            </div>
                            
                            <!-- Documentos del Personal -->
                            <h6 class="text-sm font-medium text-gray-700 mb-2">Documentos del Personal</h6>
                            <!-- Lista unificada de documentos -->
                            <ul class="divide-y divide-gray-200 mb-6">
                                @php
                                    $documentosObligatorios = [
                                        'INE' => ['titulo' => 'Identificación Oficial (INE)', 'url_field' => 'url_ine'],
                                        'CURP' => ['titulo' => 'CURP', 'url_field' => 'url_curp'],
                                        'RFC' => ['titulo' => 'RFC', 'url_field' => 'url_rfc'],
                                        'NSS' => ['titulo' => 'NSS (Número de Seguro Social)', 'url_field' => 'url_nss'],
                                    ];
                                @endphp
                                
                                @foreach($documentosObligatorios as $tipoDoc => $config)
                                    @php
                                        $documento = $documentosPorTipo[$tipoDoc] ?? null;
                                        $tieneDocumento = !is_null($documento) && is_object($documento);
                                        $urlField = $personal->{$config['url_field']} ?? null;
                                        
                                        // Obtener el valor del campo de texto (número de documento)
                                        $valorCampo = null;
                                        switch($tipoDoc) {
                                            case 'INE':
                                                $valorCampo = $personal->ine;
                                                break;
                                            case 'CURP':
                                                $valorCampo = $personal->curp_numero;
                                                break;
                                            case 'RFC':
                                                $valorCampo = $personal->rfc;
                                                break;
                                            case 'NSS':
                                                $valorCampo = $personal->nss;
                                                break;
                                        }
                                        
                                        // Generar subtítulo
                                        $subtitle = null;
                                        if($tieneDocumento && isset($documento->fecha_vencimiento) && $documento->fecha_vencimiento) {
                                            $subtitle = '<p class="text-xs text-gray-500">Vence: ' . \Carbon\Carbon::parse($documento->fecha_vencimiento)->format('d/m/Y') . '</p>';
                                        } elseif($tieneDocumento && isset($documento->descripcion)) {
                                            $subtitle = '<p class="text-xs text-gray-500">' . e($documento->descripcion) . '</p>';
                                        } elseif(!empty($valorCampo)) {
                                            $subtitle = '<p class="text-xs text-gray-500">' . e($valorCampo) . '</p>';
                                        }
                                    @endphp
                                    
                                    <x-document-field 
                                        :title="$config['titulo']"
                                        :subtitle="$subtitle"
                                        :hasDocument="$tieneDocumento"
                                        :documentId="$tieneDocumento ? $documento->id : null"
                                        :directUrl="$urlField"
                                    />
                                @endforeach

                                <!-- Licencia de Manejo -->
                                @php
                                    $documentoLicencia = $documentosPorTipo['licencia'] ?? $documentosPorTipo['Licencia de Conducir'] ?? null;
                                    $tieneDocumentoLicencia = !is_null($documentoLicencia) && is_object($documentoLicencia);
                                    $urlLicencia = $personal->url_licencia ?? null;
                                    $tieneNumeroLicencia = !empty($personal->no_licencia);
                                    
                                    $subtitleLicencia = null;
                                    if($tieneNumeroLicencia || $tieneDocumentoLicencia || $urlLicencia) {
                                        $subtitleLicencia = '<p class="text-xs text-gray-500">Número: ' . ($personal->no_licencia ?? 'No registrado') . '</p>';
                                        if($tieneDocumentoLicencia && isset($documentoLicencia->fecha_vencimiento) && $documentoLicencia->fecha_vencimiento) {
                                            $subtitleLicencia .= '<p class="text-xs text-gray-500">Vence: ' . \Carbon\Carbon::parse($documentoLicencia->fecha_vencimiento)->format('d/m/Y') . '</p>';
                                        }
                                    }
                                @endphp
                                
                                <x-document-field 
                                    title="Licencia de Manejo"
                                    :subtitle="$subtitleLicencia"
                                    :hasDocument="$tieneDocumentoLicencia"
                                    :documentId="$tieneDocumentoLicencia ? $documentoLicencia->id : null"
                                    :directUrl="$urlLicencia"
                                />

                                    <!-- Comprobante de Domicilio -->
                                    @php
                                        $documentoComprobante = $documentosPorTipo['domicilio'] ?? $documentosPorTipo['Comprobante de Domicilio'] ?? null;
                                        $tieneDocumentoComprobante = !is_null($documentoComprobante) && is_object($documentoComprobante);
                                        $urlComprobante = $personal->url_comprobante_domicilio ?? null;
                                        $tieneDireccion = !empty($personal->direccion);
                                        
                                        $subtitleComprobante = null;
                                        if($tieneDireccion || $tieneDocumentoComprobante || $urlComprobante) {
                                            $subtitleComprobante = '<p class="text-xs text-gray-500">' . ($personal->direccion ? Str::limit($personal->direccion, 50) : 'Dirección no registrada') . '</p>';
                                            if($tieneDocumentoComprobante && isset($documentoComprobante->fecha_vencimiento) && $documentoComprobante->fecha_vencimiento) {
                                                $subtitleComprobante .= '<p class="text-xs text-gray-500">Vence: ' . \Carbon\Carbon::parse($documentoComprobante->fecha_vencimiento)->format('d/m/Y') . '</p>';
                                            }
                                        }
                                    @endphp
                                    
                                    <x-document-field 
                                        title="Comprobante de Domicilio"
                                        :subtitle="$subtitleComprobante"
                                        :hasDocument="$tieneDocumentoComprobante"
                                        :documentId="$tieneDocumentoComprobante ? $documentoComprobante->id : null"
                                        :directUrl="$urlComprobante"
                                    />

                                    <!-- CV Profesional -->
                                    @php
                                        $documentoCV = $documentosPorTipo['cv'] ?? $documentosPorTipo['CV Profesional'] ?? null;
                                        $tieneDocumentoCV = !is_null($documentoCV) && is_object($documentoCV);
                                        $urlCV = $personal->url_cv ?? null;
                                        $tieneAlgunCV = $tieneDocumentoCV || !empty($urlCV);
                                        
                                        $subtitleCV = null;
                                        if($tieneAlgunCV) {
                                            if($tieneDocumentoCV && isset($documentoCV->descripcion)) {
                                                $subtitleCV = '<p class="text-xs text-gray-500">' . e($documentoCV->descripcion) . '</p>';
                                            } else {
                                                $subtitleCV = '<p class="text-xs text-gray-500">Curriculum vitae del empleado</p>';
                                            }
                                            if($tieneDocumentoCV && isset($documentoCV->created_at)) {
                                                $subtitleCV .= '<p class="text-xs text-gray-500">Actualizado: ' . \Carbon\Carbon::parse($documentoCV->created_at)->format('d/m/Y') . '</p>';
                                            }
                                        }
                                    @endphp
                                    
                                    <x-document-field 
                                        title="CV Profesional"
                                        :subtitle="$subtitleCV"
                                        :hasDocument="$tieneDocumentoCV"
                                        :documentId="$tieneDocumentoCV ? $documentoCV->id : null"
                                        :directUrl="$urlCV"
                                    />

                                <!-- Documento de Inicio Laboral -->
                                @php
                                    $documentoInicioLaboral = $documentosPorTipo['inicio_laboral'] ?? $documentosPorTipo['Documento de Inicio Laboral'] ?? null;
                                    $tieneDocumentoInicioLaboral = !is_null($documentoInicioLaboral) && is_object($documentoInicioLaboral);
                                    $urlInicioLaboral = $personal->url_inicio_laboral ?? null;
                                    $tieneFechaInicio = !empty($personal->fecha_inicio_laboral);
                                    
                                    $subtitleInicioLaboral = null;
                                    if($tieneFechaInicio || $tieneDocumentoInicioLaboral || $urlInicioLaboral) {
                                        $subtitleInicioLaboral = '<p class="text-xs text-gray-500">Fecha: ' . ($personal->fecha_inicio_laboral ? $personal->fecha_inicio_laboral->format('d/m/Y') : 'No registrada') . '</p>';
                                        if($tieneDocumentoInicioLaboral && isset($documentoInicioLaboral->descripcion)) {
                                            $subtitleInicioLaboral .= '<p class="text-xs text-gray-500">' . e($documentoInicioLaboral->descripcion) . '</p>';
                                        }
                                    }
                                @endphp
                                
                                <x-document-field 
                                    title="Documento de Inicio Laboral"
                                    :subtitle="$subtitleInicioLaboral"
                                    :hasDocument="$tieneDocumentoInicioLaboral"
                                    :documentId="$tieneDocumentoInicioLaboral ? $documentoInicioLaboral->id : null"
                                    :directUrl="$urlInicioLaboral"
                                />

                                <!-- Documento de Término Laboral -->
                                @php
                                    $documentoTerminoLaboral = $documentosPorTipo['termino_laboral'] ?? $documentosPorTipo['Documento de Término Laboral'] ?? null;
                                    $tieneDocumentoTerminoLaboral = !is_null($documentoTerminoLaboral) && is_object($documentoTerminoLaboral);
                                    $urlTerminoLaboral = $personal->url_termino_laboral ?? null;
                                    $tieneFechaTermino = !empty($personal->fecha_termino_laboral);
                                    
                                    $subtitleTerminoLaboral = null;
                                    if($tieneFechaTermino || $tieneDocumentoTerminoLaboral || $urlTerminoLaboral) {
                                        $subtitleTerminoLaboral = '<p class="text-xs text-gray-500">Fecha: ' . ($personal->fecha_termino_laboral ? $personal->fecha_termino_laboral->format('d/m/Y') : 'No registrada') . '</p>';
                                        if($tieneDocumentoTerminoLaboral && isset($documentoTerminoLaboral->descripcion)) {
                                            $subtitleTerminoLaboral .= '<p class="text-xs text-gray-500">' . e($documentoTerminoLaboral->descripcion) . '</p>';
                                        }
                                    }
                                @endphp
                                
                                <x-document-field 
                                    title="Documento de Término Laboral"
                                    :subtitle="$subtitleTerminoLaboral"
                                    :hasDocument="$tieneDocumentoTerminoLaboral"
                                    :documentId="$tieneDocumentoTerminoLaboral ? $documentoTerminoLaboral->id : null"
                                    :directUrl="$urlTerminoLaboral"
                                />
                            </ul>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Botones de Acción Flotantes -->
<div class="fixed bottom-6 right-6 flex space-x-3 z-50">
    @hasPermission('editar_personal')
    <!-- Botón Editar -->
    <a href="{{ route('personal.edit', $personal->id ?? 1) }}" 
       class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded text-sm transition-colors duration-200 flex items-center space-x-2 shadow-lg"
       title="Editar Personal">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
        </svg>
        <span>Editar</span>
    </a>
    @endhasPermission

    @hasPermission('eliminar_personal')
        @if($personal->id !== 1)
        <!-- Botón Eliminar -->
        <form action="{{ route('personal.destroy', $personal->id ?? 1) }}" 
              method="POST" 
              class="inline" 
              onsubmit="return confirm('¿Estás seguro de que quieres eliminar este personal? Esta acción no se puede deshacer.')">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded text-sm transition-colors duration-200 flex items-center space-x-2 shadow-lg"
                    title="Eliminar Personal">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                </svg>
                <span>Eliminar</span>
            </button>
        </form>
        @else
        <!-- Usuario admin protegido -->
        <div class="inline-flex items-center bg-gray-400 text-white font-medium px-4 py-2 rounded text-sm cursor-not-allowed shadow-lg space-x-2"
             title="Usuario administrador del sistema - No se puede eliminar">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
            </svg>
            <span>Protegido</span>
        </div>
        @endif
    @endhasPermission
</div>
@endsection

@push('scripts')
<script>
// Hacer la función global para que sea accesible desde los elementos HTML
window.switchTab = function(tabName) {
    console.log('Switching to tab:', tabName); // Debug
    
    // Lista de todas las pestañas disponibles (solo documentos por ahora)
    const tabs = ['documentos']; // 'asignacion' comentado temporalmente
    
    tabs.forEach(tab => {
        // Referencias a los elementos
        const tabButton = document.getElementById(`${tab}-tab`);
        const tabContent = document.getElementById(`${tab}-content`);
        
        if (!tabButton || !tabContent) {
            console.error(`Element not found for tab: ${tab}`);
            return;
        }
        
        if (tab === tabName) {
            // Activar pestaña seleccionada - estilo carpeta
            tabButton.className = 'relative px-4 py-2 text-sm font-medium rounded-t-lg transition-all duration-200 border-b-0 ml-0 bg-gray-50 border-gray-300 border-t border-l border-r text-gray-800 shadow-sm z-10';
            tabButton.setAttribute('aria-selected', 'true');
            
            // Mostrar contenido de la pestaña activa
            tabContent.style.display = 'block';
            console.log(`Activated tab: ${tab}`); // Debug
        } else {
            // Desactivar pestañas no seleccionadas - estilo carpeta inactivo
            tabButton.className = 'relative px-4 py-2 text-sm font-medium rounded-t-lg transition-all duration-200 border-b-0 bg-gray-100 border-gray-300 border-t border-l border-r text-gray-600 hover:bg-gray-200';
            tabButton.setAttribute('aria-selected', 'false');
            
            // Ocultar contenido de pestañas inactivas
            tabContent.style.display = 'none';
        }
    });
};



// Funciones para ver y mostrar datos de documentos de personal
window.viewPersonalDocument = function(documentUrl) {
    if (!documentUrl) {
        alert('URL de documento no válida');
        return;
    }

    // Si es un ID numérico, usar la ruta de documentos
    if (/^\d+$/.test(documentUrl)) {
        const url = `/documentos/${documentUrl}/file`;
        window.open(url, '_blank');
    } else {
        // Si es una URL directa, abrirla directamente
        window.open(documentUrl, '_blank');
    }
};

window.downloadPersonalDocument = function(documentId) {
    // Como no hay archivos, mostramos los datos en formato de texto
    const documentRow = document.querySelector(`li[data-document-id="${documentId}"]`);
    if (documentRow) {
        const tipo = documentRow.querySelector('.document-type')?.textContent || 'No especificado';
        const descripcion = documentRow.querySelector('.document-description')?.textContent || 'No especificada';
        const fecha = documentRow.querySelector('.document-date')?.textContent || 'No especificada';
        
        const documentData = `DATOS DEL DOCUMENTO\n\nTipo: ${tipo}\nDatos: ${descripcion}\nFecha de Vencimiento: ${fecha}\n\nGenerado el: ${new Date().toLocaleString()}`;
        
        // Crear un blob con los datos y "descargarlo"
        const blob = new Blob([documentData], { type: 'text/plain' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `documento_${tipo.replace(/\s+/g, '_').toLowerCase()}.txt`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    } else {
        alert('No se encontró la información del documento');
    }
};

// Navegación con teclado para accesibilidad
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing tabs'); // Debug
    
    // Agregar event listeners para botones de documentos
    const viewButtons = document.querySelectorAll('.btn-view-document');
    const downloadButtons = document.querySelectorAll('.btn-download-document');
    
    viewButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const documentId = this.getAttribute('data-document-id');
            viewPersonalDocument(documentId);
        });
    });
    
    downloadButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const documentId = this.getAttribute('data-document-id');
            downloadPersonalDocument(documentId);
        });
    });
    
    const tabButtons = document.querySelectorAll('[role="tab"]');
    console.log('Found tab buttons:', tabButtons.length); // Debug
    
    tabButtons.forEach((button, index) => {
        button.addEventListener('keydown', function(e) {
            let targetIndex = index;
            
            // Navegación con flechas
            if (e.key === 'ArrowRight') {
                targetIndex = (index + 1) % tabButtons.length;
                e.preventDefault();
            } else if (e.key === 'ArrowLeft') {
                targetIndex = (index - 1 + tabButtons.length) % tabButtons.length;
                e.preventDefault();
            } else if (e.key === 'Home') {
                targetIndex = 0;
                e.preventDefault();
            } else if (e.key === 'End') {
                targetIndex = tabButtons.length - 1;
                e.preventDefault();
            }
            
            if (targetIndex !== index) {
                tabButtons[targetIndex].focus();
                
                // Extraer el nombre de la pestaña del ID del botón
                const tabName = tabButtons[targetIndex].id.replace('-tab', '');
                window.switchTab(tabName);
            }
        });
    });
    
    // Añadir click listeners como alternativa
    tabButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const tabName = this.id.replace('-tab', '');
            window.switchTab(tabName);
        });
    });
    

});

// Función auxiliar para indicadores de carga
window.showTabLoading = function(tabName) {
    const tabContent = document.getElementById(`${tabName}-content`);
    if (tabContent) {
        tabContent.style.opacity = '0.6';
        setTimeout(() => {
            tabContent.style.opacity = '1';
        }, 200);
    }
};
</script>
@endpush
