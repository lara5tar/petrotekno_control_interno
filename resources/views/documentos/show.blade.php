@extends('layouts.app')

@section('title', 'Detalle del Documento')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Encabezado -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Detalle del Documento</h1>
            <div class="flex space-x-2">
                <a href="{{ route('documentos.edit', $documento->id) }}" 
                   class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                    Editar
                </a>
                <a href="{{ route('documentos.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    Volver
                </a>
            </div>
        </div>

        <!-- Contenido -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <!-- Estado -->
                <div class="mb-6">
                    @if($documento->fecha_vencimiento)
                        @php
                            $dias = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($documento->fecha_vencimiento), false);
                        @endphp
                        @if($dias < 0)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                🚨 Documento Vencido ({{ abs($dias) }} días)
                            </span>
                        @elseif($dias <= 30)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                ⚠️ Próximo a Vencer ({{ $dias }} días)
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                ✅ Documento Vigente
                            </span>
                        @endif
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            📄 Sin Fecha de Vencimiento
                        </span>
                    @endif
                </div>

                <!-- Información Principal -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Información General</h3>
                        
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Tipo de Documento</label>
                                <p class="text-gray-900">{{ $documento->tipoDocumento->nombre_tipo_documento ?? 'N/A' }}</p>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-500">Descripción</label>
                                <p class="text-gray-900">{{ $documento->descripcion ?? 'Sin descripción' }}</p>
                            </div>

                            @if($documento->fecha_vencimiento)
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Fecha de Vencimiento</label>
                                    <p class="text-gray-900">{{ \Carbon\Carbon::parse($documento->fecha_vencimiento)->format('d/m/Y') }}</p>
                                </div>
                            @endif

                            <div>
                                <label class="text-sm font-medium text-gray-500">Fecha de Creación</label>
                                <p class="text-gray-900">{{ \Carbon\Carbon::parse($documento->created_at)->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Asociaciones</h3>
                        
                        <div class="space-y-3">
                            @if($documento->vehiculo)
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Vehículo</label>
                                    <p class="text-gray-900">
                                        🚗 {{ $documento->vehiculo->marca }} {{ $documento->vehiculo->modelo }}
                                        <br><span class="text-sm text-gray-600">Placas: {{ $documento->vehiculo->placas }}</span>
                                        @if($documento->vehiculo->n_serie)
                                            <br><span class="text-sm text-gray-600">Serie: {{ $documento->vehiculo->n_serie }}</span>
                                        @endif
                                    </p>
                                </div>
                            @endif

                            @if($documento->personal)
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Personal</label>
                                    <p class="text-gray-900">👤 {{ $documento->personal->nombre_completo }}</p>
                                </div>
                            @endif

                            @if($documento->obra)
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Obra</label>
                                    <p class="text-gray-900">
                                        🏗️ {{ $documento->obra->nombre_obra }}
                                        <br><span class="text-sm text-gray-600">Estado: {{ ucfirst($documento->obra->estatus) }}</span>
                                        @if($documento->obra->fecha_inicio)
                                            <br><span class="text-sm text-gray-600">
                                                Inicio: {{ \Carbon\Carbon::parse($documento->obra->fecha_inicio)->format('d/m/Y') }}
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            @endif

                            @if(!$documento->vehiculo && !$documento->personal && !$documento->obra)
                                <div>
                                    <p class="text-gray-500 italic">Sin asociaciones</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Archivo -->
                @if($documento->ruta_archivo)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Archivo Adjunto</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ basename($documento->ruta_archivo) }}
                                    </p>
                                    <p class="text-sm text-gray-500">Archivo adjunto</p>
                                </div>
                                <div>
                                    <a href="{{ Storage::url($documento->ruta_archivo) }}" 
                                       target="_blank"
                                       class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Descargar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Contenido JSON -->
                @if($documento->contenido)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Contenido Estructurado</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ json_encode(json_decode($documento->contenido), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                @endif

                <!-- Información Adicional del Tipo -->
                @if($documento->tipoDocumento->descripcion || $documento->tipoDocumento->requiere_vencimiento)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Tipo</h3>
                        <div class="bg-blue-50 rounded-lg p-4">
                            @if($documento->tipoDocumento->descripcion)
                                <p class="text-sm text-blue-700 mb-2">{{ $documento->tipoDocumento->descripcion }}</p>
                            @endif
                            @if($documento->tipoDocumento->requiere_vencimiento)
                                <p class="text-xs text-blue-600">✓ Este tipo de documento requiere fecha de vencimiento</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Acciones -->
            <div class="bg-gray-50 px-6 py-4">
                <div class="flex justify-between">
                    <form action="{{ route('documentos.destroy', $documento->id) }}" 
                          method="POST" 
                          onsubmit="return confirm('¿Estás seguro de que quieres eliminar este documento? Esta acción no se puede deshacer.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                            Eliminar Documento
                        </button>
                    </form>

                    <div class="flex space-x-2">
                        <a href="{{ route('documentos.edit', $documento->id) }}" 
                           class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                            Editar
                        </a>
                        <a href="{{ route('documentos.index') }}" 
                           class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                            Volver a la Lista
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
