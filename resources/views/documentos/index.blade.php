@extends('layouts.app')

@section('title', 'Documentos')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Encabezado -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestión de Documentos</h1>
        <a href="{{ route('documentos.create') }}" 
           class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
            Nuevo Documento
        </a>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('documentos.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Tipo de Documento -->
            <div>
                <label for="tipo_documento_id" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Documento</label>
                <select id="tipo_documento_id" name="tipo_documento_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos los tipos</option>
                    @foreach($tiposDocumento as $tipo)
                        <option value="{{ $tipo->id }}" {{ request('tipo_documento_id') == $tipo->id ? 'selected' : '' }}>
                            {{ $tipo->nombre_tipo_documento }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Activo -->
            <div>
                <label for="vehiculo_id" class="block text-sm font-medium text-gray-700 mb-1">Activo</label>
                <select id="vehiculo_id" name="vehiculo_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos los activos</option>
                    @foreach($vehiculos as $vehiculo)
                        <option value="{{ $vehiculo->id }}" {{ request('vehiculo_id') == $vehiculo->id ? 'selected' : '' }}>
                            {{ $vehiculo->marca }} {{ $vehiculo->modelo }} - {{ $vehiculo->placas }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Personal -->
            <div>
                <label for="personal_id" class="block text-sm font-medium text-gray-700 mb-1">Personal</label>
                <select id="personal_id" name="personal_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todo el personal</option>
                    @foreach($personal as $persona)
                        <option value="{{ $persona->id }}" {{ request('personal_id') == $persona->id ? 'selected' : '' }}>
                            {{ $persona->nombre_completo }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Estado -->
            <div>
                <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select id="estado" name="estado" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos los estados</option>
                    <option value="vencidos" {{ request('estado') == 'vencidos' ? 'selected' : '' }}>Vencidos</option>
                    <option value="proximos_a_vencer" {{ request('estado') == 'proximos_a_vencer' ? 'selected' : '' }}>Próximos a vencer</option>
                </select>
            </div>

            <!-- Búsqueda -->
            <div class="lg:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" 
                       placeholder="Buscar por descripción..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Botones -->
            <div class="flex space-x-2">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    Filtrar
                </button>
                <a href="{{ route('documentos.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Lista de documentos -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @if($documentos->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tipo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Descripción
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Asociado
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Vencimiento
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($documentos as $documento)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $documento->tipoDocumento->nombre_tipo_documento ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $documento->descripcion ?? 'Sin descripción' }}
                                    @if($documento->ruta_archivo)
                                        <br><span class="text-blue-600 text-xs">📎 Archivo adjunto</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    @if($documento->vehiculo)
                                        <span class="text-blue-600">🔧 {{ $documento->vehiculo->marca }} {{ $documento->vehiculo->modelo }}</span>
                                    @elseif($documento->personal)
                                        <span class="text-green-600">👤 {{ $documento->personal->nombre_completo }}</span>
                                    @elseif($documento->obra)
                                        <span class="text-orange-600">🏗️ {{ $documento->obra->nombre_obra }}</span>
                                    @else
                                        <span class="text-gray-500">Sin asociar</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($documento->fecha_vencimiento)
                                        {{ \Carbon\Carbon::parse($documento->fecha_vencimiento)->format('d/m/Y') }}
                                    @else
                                        <span class="text-gray-500">Sin vencimiento</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($documento->fecha_vencimiento)
                                        @php
                                            $dias = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($documento->fecha_vencimiento), false);
                                        @endphp
                                        @if($dias < 0)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Vencido
                                            </span>
                                        @elseif($dias <= 30)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Por vencer
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Vigente
                                            </span>
                                        @endif
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            N/A
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('documentos.show', $documento->id) }}" 
                                       class="text-indigo-600 hover:text-indigo-900 mr-3">Ver</a>
                                    <a href="{{ route('documentos.edit', $documento->id) }}" 
                                       class="text-yellow-600 hover:text-yellow-900 mr-3">Editar</a>
                                    <form action="{{ route('documentos.destroy', $documento->id) }}" 
                                          method="POST" class="inline" 
                                          onsubmit="return confirm('¿Estás seguro de que quieres eliminar este documento?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="px-6 py-4 bg-gray-50">
                {{ $documentos->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500 text-lg">No se encontraron documentos.</p>
                <a href="{{ route('documentos.create') }}" 
                   class="mt-4 inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    Crear primer documento
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
