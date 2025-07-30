@extends('layouts.app')

@section('title', 'Control de Personal')

@section('header', 'Control de Personal de Petrotekno')

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
                            <label class="block text-sm font-medium text-gray-600">Categoría</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $personal->categoria->nombre_categoria ?? 'Sin categoría' }}
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                </button>
                                @elseif($personal->url_curp)
                                <button class="bg-green-600 hover:bg-green-700 text-white p-2 rounded text-sm transition duration-200 flex items-center" 
                                        onclick="viewPersonalDocument('{{ asset('storage/' . $personal->url_curp) }}')"
                                        title="Ver archivo adjunto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                </button>
                                @elseif($personal->url_rfc)
                                <button class="bg-green-600 hover:bg-green-700 text-white p-2 rounded text-sm transition duration-200 flex items-center" 
                                        onclick="viewPersonalDocument('{{ asset('storage/' . $personal->url_rfc) }}')"
                                        title="Ver archivo adjunto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                </button>
                                @elseif($personal->url_ine)
                                <button class="bg-green-600 hover:bg-green-700 text-white p-2 rounded text-sm transition duration-200 flex items-center" 
                                        onclick="viewPersonalDocument('{{ asset('storage/' . $personal->url_ine) }}')"
                                        title="Ver archivo adjunto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                </button>
                                @elseif($personal->url_nss)
                                <button class="bg-green-600 hover:bg-green-700 text-white p-2 rounded text-sm transition duration-200 flex items-center" 
                                        onclick="viewPersonalDocument('{{ asset('storage/' . $personal->url_nss) }}')"
                                        title="Ver archivo adjunto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
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
                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium flex-1">
                                    {{ $personal->no_licencia ?? 'No registrado' }}
                                </div>
                                @php
                                    $documentoLicencia = $documentosPorTipo['licencia'] ?? $documentosPorTipo['Licencia de Manejo'] ?? null;
                                    $tieneDocumentoLicencia = !is_null($documentoLicencia) && is_object($documentoLicencia);
                                @endphp
                                @if($tieneDocumentoLicencia)
                                <button class="bg-green-600 hover:bg-green-700 text-white p-2 rounded text-sm transition duration-200 flex items-center" 
                                        onclick="viewPersonalDocument('{{ $documentoLicencia->url_documento }}')"
                                        title="Ver archivo adjunto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                </button>
                                @elseif($personal->url_licencia)
                                <button class="bg-green-600 hover:bg-green-700 text-white p-2 rounded text-sm transition duration-200 flex items-center" 
                                        onclick="viewPersonalDocument('{{ asset('storage/' . $personal->url_licencia) }}')"
                                        title="Ver archivo adjunto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
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
                    
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Dirección</label>
                            <div class="flex items-center space-x-2">
                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium flex-1">
                                    {{ $personal->direccion ?? 'No registrado' }}
                                </div>
                                @if($personal->url_comprobante_domicilio)
                                <button class="bg-green-600 hover:bg-green-700 text-white p-2 rounded text-sm transition duration-200 flex items-center" 
                                        onclick="viewPersonalDocument('{{ asset('storage/' . $personal->url_comprobante_domicilio) }}')"
                                        title="Ver comprobante de domicilio">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
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
                    
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">CV Profesional</label>
                            @if(isset($documentosPorTipo['cv']) || isset($documentosPorTipo['CV Profesional']))
                                @php $doc = $documentosPorTipo['cv'] ?? $documentosPorTipo['CV Profesional']; @endphp
                                <div class="mb-2 p-2 bg-green-50 border border-green-200 rounded-md">
                                    <p class="text-xs text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        {{ $doc->tipoDocumento->descripcion }}
                                    </p>
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-paperclip mr-1"></i>
                                            Archivo adjunto
                                        </span>
                                    </div>
                                </div>
                            @endif
                            <div class="flex items-center space-x-2">
                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium flex-1">
                                    @php
                                        $documentoCV = $documentosPorTipo['CV Profesional'] ?? null;
                                        $tieneDocumentoCV = !is_null($documentoCV) && is_object($documentoCV);
                                    @endphp
                                    @if($tieneDocumentoCV && isset($documentoCV->descripcion))
                                        {{ $documentoCV->descripcion }}
                                    @else
                                        No registrado
                                    @endif
                                </div>
                                @if($tieneDocumentoCV)
                                <button class="bg-gray-600 hover:bg-gray-700 text-white p-2 rounded text-sm transition duration-200 flex items-center" 
                                        onclick="viewPersonalDocument('{{ $documentoCV->url_documento }}')"
                                        title="Ver archivo adjunto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Derecho - Información Adicional -->
        <div class="space-y-6">
            <!-- Tabs de Información -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-0 border-b border-gray-300">
                    <div class="flex space-x-0" role="tablist">
                        {{-- <button id="asignacion-tab" 
                                class="px-4 py-3 text-sm font-medium border-b-2 border-blue-600 text-blue-600 bg-white transition-colors duration-200"
                                onclick="switchTab('asignacion')" 
                                role="tab" 
                                aria-selected="true"
                                aria-controls="asignacion-content">
                            Asignación
                        </button> --}}
                        <button id="documentos-tab" 
                                class="px-4 py-3 text-sm font-medium border-b-2 border-gray-600 text-gray-600 bg-white transition-colors duration-200"
                                onclick="switchTab('documentos')" 
                                role="tab" 
                                aria-selected="true"
                                aria-controls="documentos-content">
                            Documentos
                        </button>
                    </div>
                </div>
                
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    Ir a Obra
                                </button>
                            </div>
                        </div>
                    </div>
                </div> --}}

                <!-- Contenido de Documentos -->
                <div id="documentos-content" class="tab-content p-4" role="tabpanel" aria-labelledby="documentos-tab">
                    <div class="space-y-6">
                        <!-- Documentos del Personal -->
                        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                            <div class="flex justify-between items-center mb-4">
                                <h5 class="text-base font-semibold text-gray-800 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Documentos del Personal
                                </h5>

                            </div>
                            
                            <!-- Documentos Obligatorios -->
                            <h6 class="text-sm font-medium text-gray-700 mb-2">Documentos Obligatorios</h6>
                            <ul class="divide-y divide-gray-200 mb-4">
                                @php
                                    $documentosObligatorios = ['INE', 'CURP', 'RFC', 'NSS'];
                                @endphp
                                
                                @foreach($documentosObligatorios as $tipoDoc)
                                    @php
                                        $documento = $documentosPorTipo[$tipoDoc] ?? null;
                                        $tieneDocumento = !is_null($documento) && is_object($documento);
                                    @endphp
                                    
                                    <li class="py-2 flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-{{ $tieneDocumento ? 'green' : 'red' }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <div>
                                                <span class="text-sm font-medium text-gray-800">
                                                    @if($tipoDoc === 'INE')
                                                        Identificación Oficial (INE)
                                                    @elseif($tipoDoc === 'NSS')
                                                        NSS (Número de Seguro Social)
                                                    @else
                                                        {{ $tipoDoc }}
                                                    @endif
                                                </span>
                                                @if($tieneDocumento && isset($documento->fecha_vencimiento) && $documento->fecha_vencimiento)
                                                    <p class="text-xs text-gray-500">Vence: {{ \Carbon\Carbon::parse($documento->fecha_vencimiento)->format('d/m/Y') }}</p>
                                                @elseif($tieneDocumento)
                                                    <p class="text-xs text-gray-500">{{ $documento->descripcion ?? 'Documento disponible' }}</p>
                                                @else
                                                    <p class="text-xs text-red-500">No disponible</p>
                                                @endif
                                            </div>
                                        </div>
                                        @if($tieneDocumento)
                                            <div class="flex space-x-2">
                                                <button data-document-id="{{ $documento->id }}" class="btn-view-document bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    Ver
                                                </button>
                                                <button data-document-id="{{ $primerDocumento['id'] }}" class="btn-download-document bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                    Descargar
                                                </button>
                                            </div>
                                        @else
                                            <div class="text-xs text-red-500">
                                                Faltante
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                            
                            <!-- Documentos Adicionales -->
                            <h6 class="text-sm font-medium text-gray-700 mb-2">Documentos Adicionales</h6>
                            <div id="documentos-adicionales-personal">
                                <!-- Documentos adicionales existentes -->
                                <ul class="divide-y divide-gray-200 mb-4">
                                    @php
                                        $documentosAdicionales = array_diff_key($documentosPorTipo, array_flip(['INE', 'CURP', 'RFC', 'NSS']));
                                    @endphp
                                    
                                    @forelse($documentosAdicionales as $tipoDocumento => $documentos)
                                        @foreach($documentos as $documento)
                                            <li class="py-2 flex items-center justify-between">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    <div>
                                                        <span class="text-sm font-medium text-gray-800">{{ $tipoDocumento }}</span>
                                                        @if(isset($documento['descripcion']) && $documento['descripcion'])
                                                            <p class="text-xs text-gray-500">{{ $documento['descripcion'] }}</p>
                                                        @endif
                                                        @if(isset($documento['fecha_vencimiento']) && $documento['fecha_vencimiento'])
                                                            <p class="text-xs text-gray-500">Vence: {{ \Carbon\Carbon::parse($documento['fecha_vencimiento'])->format('d/m/Y') }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex space-x-2">
                                                    <button data-document-id="{{ $documento['id'] }}" class="btn-view-document bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        </svg>
                                                        Ver
                                                    </button>
                                                    <button data-document-id="{{ $documento['id'] }}" class="btn-download-document bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                        </svg>
                                                        Descargar
                                                    </button>
                                                </div>
                                            </li>
                                        @endforeach
                                    @empty
                                        <li class="py-4 text-center text-gray-500 text-sm">
                                            No hay documentos adicionales registrados
                                        </li>
                                    @endforelse
                                </ul>
                                
                                <!-- Lista de documentos adicionales dinámicos (se llenará dinámicamente) -->
                                <ul class="divide-y divide-gray-200" id="lista-documentos-adicionales-personal">
                                    <!-- Los documentos adicionales se agregarán aquí dinámicamente -->
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
            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
        </svg>
        <span>Editar</span>
    </a>
    @endhasPermission

    @hasPermission('eliminar_personal')
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
                <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd" />
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
            </svg>
            <span>Eliminar</span>
        </button>
    </form>
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
            // Activar pestaña seleccionada
            tabButton.classList.add('border-gray-600', 'text-gray-600', 'bg-white');
            tabButton.classList.remove('text-gray-400', 'border-transparent');
            tabButton.setAttribute('aria-selected', 'true');
            
            // Mostrar contenido de la pestaña activa
            tabContent.classList.remove('hidden');
            console.log(`Activated tab: ${tab}`); // Debug
        } else {
            // Desactivar pestañas no seleccionadas
            tabButton.classList.remove('border-gray-600', 'text-gray-600', 'bg-white');
            tabButton.classList.add('text-gray-400', 'border-transparent');
            tabButton.setAttribute('aria-selected', 'false');
            
            // Ocultar contenido de pestañas inactivas
            tabContent.classList.add('hidden');
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
