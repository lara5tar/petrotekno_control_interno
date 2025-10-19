@extends('layouts.app')

@section('title', 'Editar Obra')

@section('header', 'Gestión de Obras')

@section('content')
    <div class="container mx-auto px-1 sm:px-2 py-3 max-w-full">
        {{-- Breadcrumb --}}
        <x-breadcrumb :items="[
            ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
            ['label' => 'Obras', 'url' => route('obras.index')],
            ['label' => 'Editar Obra']
        ]" />

        {{-- Mensajes de estado --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-3 py-2 rounded mb-3" role="alert">
                <strong class="font-bold">¡Éxito!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded mb-3" role="alert">
                <strong class="font-bold">¡Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded mb-3" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Se encontraron {{ $errors->count() }} error(es) en el formulario:
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Header principal --}}
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-3 gap-2">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Editar Obra</h2>
                <p class="text-sm text-gray-600 mt-1">{{ $obra->nombre_obra }}</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ route('obras.show', $obra->id) }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md flex items-center justify-center transition duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                    </svg>
                    Ver Detalles
                </a>
                <a href="{{ route('obras.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md flex items-center justify-center transition duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Volver al Listado
                </a>
            </div>
        </div>

        {{-- Formulario principal --}}
        <form action="{{ route('obras.update', $obra->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
            @csrf
            @method('PUT')

            {{-- Información Básica --}}
            <div class="bg-white border border-gray-200 rounded-lg p-3 sm:p-4">
                <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2 mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                    </svg>
                    Información Básica
                </h3>
                <p class="text-sm text-gray-500 mb-3">Datos generales de la obra</p>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    {{-- Nombre de la Obra --}}
                    <div class="lg:col-span-2">
                        <label for="nombre_obra" class="block text-sm font-medium text-gray-700 mb-1">
                            Nombre de la Obra <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="nombre_obra" 
                               name="nombre_obra" 
                               value="{{ old('nombre_obra', $obra->nombre_obra) }}"
                               class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('nombre_obra') border-red-500 @enderror"
                               placeholder="Ingrese el nombre de la obra"
                               required>
                        @error('nombre_obra')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Ubicación de la Obra --}}
                    <div class="lg:col-span-2">
                        <label for="ubicacion" class="block text-sm font-medium text-gray-700 mb-1">
                            Ubicación de la Obra
                        </label>
                        <input type="text" 
                               id="ubicacion" 
                               name="ubicacion" 
                               value="{{ old('ubicacion', $obra->ubicacion) }}"
                               class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('ubicacion') border-red-500 @enderror"
                               placeholder="Ingrese la ubicación donde se realizará la obra">
                        @error('ubicacion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Estatus --}}
                    <div>
                        <label for="estatus" class="block text-sm font-medium text-gray-700 mb-1">
                            Estatus <span class="text-red-500">*</span>
                        </label>
                        <select id="estatus" 
                                name="estatus" 
                                class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('estatus') border-red-500 @enderror"
                                required>
                            <option value="">Seleccione un estatus</option>
                            @foreach($estatusOptions as $valor => $nombre)
                                <option value="{{ $valor }}" {{ old('estatus', $obra->estatus) == $valor ? 'selected' : '' }}>
                                    {{ $nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('estatus')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Avance --}}
                    <div>
                        <label for="avance" class="block text-sm font-medium text-gray-700 mb-1">
                            Avance (%)
                        </label>
                        <input type="number" 
                               id="avance" 
                               name="avance" 
                               value="{{ old('avance', $obra->avance ?? 0) }}"
                               min="0" 
                               max="100"
                               class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('avance') border-red-500 @enderror"
                               placeholder="0">
                        @error('avance')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Fecha de Inicio --}}
                    <div>
                        <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-1">
                            Fecha de Inicio <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               id="fecha_inicio" 
                               name="fecha_inicio" 
                               value="{{ old('fecha_inicio', $obra->fecha_inicio ? $obra->fecha_inicio->format('Y-m-d') : '') }}"
                               class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('fecha_inicio') border-red-500 @enderror"
                               required>
                        @error('fecha_inicio')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Fecha de Fin --}}
                    <div>
                        <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-1">
                            Fecha de Fin
                        </label>
                        <input type="date" 
                               id="fecha_fin" 
                               name="fecha_fin" 
                               value="{{ old('fecha_fin', $obra->fecha_fin ? $obra->fecha_fin->format('Y-m-d') : '') }}"
                               class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('fecha_fin') border-red-500 @enderror">
                        @error('fecha_fin')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Responsable de la obra --}}
                    <div class="lg:col-span-2">
                        <label for="encargado_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Responsable de la obra <span class="text-red-500">*</span>
                        </label>
                        <select id="encargado_id" 
                                name="encargado_id" 
                                class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('encargado_id') border-red-500 @enderror"
                                required>
                            <option value="">Seleccione un responsable</option>
                            @if($encargados && $encargados->count() > 0)
                                @foreach($encargados as $encargado)
                                    <option value="{{ $encargado['id'] }}" 
                                            {{ old('encargado_id', $obra->encargado_id) == $encargado['id'] ? 'selected' : '' }}>
                                        {{ $encargado['nombre_usuario'] }}
                                        @if($encargado['tipo'] == 'usuario')
                                            - {{ $encargado['rol'] ?? 'Usuario' }}
                                        @else
                                            - {{ $encargado['categoria'] ?? 'Personal' }}
                                        @endif
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('encargado_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Personal activo disponible para supervisión del proyecto (puede ser cualquier categoría)</p>
                    </div>

                    {{-- Observaciones --}}
                    <div class="lg:col-span-2">
                        <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1">
                            Observaciones
                        </label>
                        <textarea id="observaciones" 
                                  name="observaciones" 
                                  rows="3"
                                  class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('observaciones') border-red-500 @enderror"
                                  placeholder="Observaciones adicionales sobre la obra">{{ old('observaciones', $obra->observaciones) }}</textarea>
                        @error('observaciones')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Información de Vehículos Asignados --}}
            <div class="bg-white border border-gray-200 rounded-lg p-3 sm:p-4">
                <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2 mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1 1 0 11-3 0 1.5 1.5 0 013 0z" />
                        <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z" />
                    </svg>
                    Información de Vehículos Asignados
                </h3>
                <p class="text-sm text-gray-500 mb-3">Vehículos actualmente asignados a esta obra</p>

                @php
                    $tieneVehiculoDirecto = $obra->vehiculo_id && $obra->vehiculo;
                    $tieneAsignacionesActivas = method_exists($obra, 'asignacionesActivas') && $obra->asignacionesActivas && $obra->asignacionesActivas->count() > 0;
                @endphp

                @if($tieneVehiculoDirecto || $tieneAsignacionesActivas)
                    <div class="space-y-3">
                        {{-- Vehículo directo --}}
                        @if($tieneVehiculoDirecto)
                            <div class="flex items-center justify-between p-4 bg-gray-50 border border-gray-300 rounded-lg shadow-sm">
                                <div class="flex items-center space-x-3">
                                    <div class="flex items-center justify-center w-8 h-8 bg-petroyellow rounded-lg">
                                        <svg class="w-4 h-4 text-petrodark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="text-sm font-semibold text-gray-900">{{ $obra->vehiculo->marca }} {{ $obra->vehiculo->modelo }}</span>
                                        <p class="text-xs text-gray-700">Placas: {{ $obra->vehiculo->placas }}</p>
                                        @if($obra->operador)
                                            <p class="text-xs text-gray-600">Operador: {{ $obra->operador->nombre_completo }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium border border-green-200">
                                    Sistema Directo
                                </div>
                            </div>
                        @endif

                        {{-- Asignaciones activas --}}
                        @if($tieneAsignacionesActivas)
                            @foreach($obra->asignacionesActivas as $asignacion)
                                <div class="flex items-center justify-between p-4 bg-gray-50 border border-gray-300 rounded-lg shadow-sm">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex items-center justify-center w-8 h-8 bg-blue-500 rounded-lg">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            @if($asignacion->vehiculo)
                                                <span class="text-sm font-semibold text-gray-900">{{ $asignacion->vehiculo->marca }} {{ $asignacion->vehiculo->modelo }}</span>
                                                <p class="text-xs text-gray-700">Placas: {{ $asignacion->vehiculo->placas }}</p>
                                            @else
                                                <span class="text-sm font-semibold text-gray-900">Vehículo no disponible</span>
                                            @endif
                                            @if($asignacion->operador)
                                                <p class="text-xs text-gray-600">Operador: {{ $asignacion->operador->nombre_completo }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-medium border border-blue-200">
                                        Sistema Asignaciones
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                @else
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <p class="text-sm text-gray-700 mt-2">No hay vehículos asignados a esta obra actualmente.</p>
                        <p class="text-xs text-gray-500 mt-1">Use la sección de <a href="{{ route('obras.show', $obra->id) }}" class="text-blue-600 underline">detalles de la obra</a> para gestionar asignaciones.</p>
                    </div>
                @endif
            </div>

            {{-- Documentos --}}
            <div class="bg-white border border-gray-200 rounded-lg p-3 sm:p-4">
                <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2 mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                    </svg>
                    Documentos de la Obra
                </h3>
                <p class="text-sm text-gray-500 mb-3">Archivos relacionados con la obra (subir nuevos reemplazará los existentes)</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Contrato --}}
                    <div>
                        <x-file-upload 
                            name="archivo_contrato"
                            label="Contrato"
                            accept=".pdf,.doc,.docx"
                            max-size="10MB"
                            :existing-file="$obra->getUrlContrato()"
                            existing-file-label="Ver contrato actual"
                            change-text="Reemplazar Contrato"
                            upload-text="Subir Contrato"
                            help-text="PDF, DOC, DOCX (máx. 10MB)"
                        />
                    </div>

                    {{-- Fianza --}}
                    <div>
                        <x-file-upload 
                            name="archivo_fianza"
                            label="Fianza"
                            accept=".pdf,.doc,.docx"
                            max-size="10MB"
                            :existing-file="$obra->getUrlFianza()"
                            existing-file-label="Ver fianza actual"
                            change-text="Reemplazar Fianza"
                            upload-text="Subir Fianza"
                            help-text="PDF, DOC, DOCX (máx. 10MB)"
                        />
                    </div>

                    {{-- Acta de Entrega-Recepción --}}
                    <div>
                        <x-file-upload 
                            name="archivo_acta_entrega_recepcion"
                            label="Acta de Entrega-Recepción"
                            accept=".pdf,.doc,.docx"
                            max-size="10MB"
                            :existing-file="$obra->getUrlActaEntregaRecepcion()"
                            existing-file-label="Ver acta actual"
                            change-text="Reemplazar Acta"
                            upload-text="Subir Acta"
                            help-text="PDF, DOC, DOCX (máx. 10MB)"
                        />
                    </div>
                </div>
            </div>

            {{-- Información de Auditoría --}}
            <div class="bg-white border border-gray-200 rounded-lg p-3 sm:p-4">
                <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2 mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" clip-rule="evenodd" />
                    </svg>
                    Información de Auditoría
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Fecha de Creación</label>
                        <div class="bg-gray-100 px-3 py-2 rounded-md text-sm">
                            {{ $obra->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Última Actualización</label>
                        <div class="bg-gray-100 px-3 py-2 rounded-md text-sm">
                            {{ $obra->updated_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Botones de acción --}}
            <div class="flex flex-col sm:flex-row justify-end gap-2 pt-3 border-t border-gray-200">
                <a href="{{ route('obras.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md transition duration-200 text-center">
                    Cancelar
                </a>
                <button type="submit" 
                        class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded-md transition duration-200">
                    Actualizar Obra
                </button>
            </div>
        </form>
    </div>
@endsection
