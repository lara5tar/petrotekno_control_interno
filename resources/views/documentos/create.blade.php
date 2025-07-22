@extends('layouts.app')

@section('title', 'Crear Documento')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Encabezado -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Crear Nuevo Documento</h1>
            <a href="{{ route('documentos.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                Volver
            </a>
        </div>

        <!-- Formulario -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('documentos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tipo de Documento -->
                    <div class="md:col-span-2">
                        <label for="tipo_documento_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Tipo de Documento <span class="text-red-500">*</span>
                        </label>
                        <select id="tipo_documento_id" name="tipo_documento_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tipo_documento_id') border-red-500 @enderror">
                            <option value="">Seleccionar tipo de documento</option>
                            @foreach($tiposDocumento as $tipo)
                                <option value="{{ $tipo->id }}" {{ old('tipo_documento_id') == $tipo->id ? 'selected' : '' }}>
                                    {{ $tipo->nombre_tipo_documento }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo_documento_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Descripción -->
                    <div class="md:col-span-2">
                        <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">
                            Descripción
                        </label>
                        <textarea id="descripcion" name="descripcion" rows="3"
                                  placeholder="Descripción opcional del documento"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('descripcion') border-red-500 @enderror">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fecha de Vencimiento -->
                    <div>
                        <label for="fecha_vencimiento" class="block text-sm font-medium text-gray-700 mb-1">
                            Fecha de Vencimiento
                        </label>
                        <input type="date" id="fecha_vencimiento" name="fecha_vencimiento" 
                               value="{{ old('fecha_vencimiento') }}"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('fecha_vencimiento') border-red-500 @enderror">
                        @error('fecha_vencimiento')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Archivo -->
                    <div>
                        <label for="archivo" class="block text-sm font-medium text-gray-700 mb-1">
                            Archivo
                        </label>
                        <input type="file" id="archivo" name="archivo"
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('archivo') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">
                            Formatos permitidos: PDF, DOC, DOCX, JPG, JPEG, PNG. Máximo 10MB.
                        </p>
                        @error('archivo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Asociación con Vehículo -->
                    <div>
                        <label for="vehiculo_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Vehículo
                        </label>
                        <select id="vehiculo_id" name="vehiculo_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('vehiculo_id') border-red-500 @enderror">
                            <option value="">Sin asociar a vehículo</option>
                            @foreach($vehiculos as $vehiculo)
                                <option value="{{ $vehiculo->id }}" {{ old('vehiculo_id') == $vehiculo->id ? 'selected' : '' }}>
                                    {{ $vehiculo->marca }} {{ $vehiculo->modelo }} - {{ $vehiculo->placas }}
                                </option>
                            @endforeach
                        </select>
                        @error('vehiculo_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Asociación con Personal -->
                    <div>
                        <label for="personal_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Personal
                        </label>
                        <select id="personal_id" name="personal_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('personal_id') border-red-500 @enderror">
                            <option value="">Sin asociar a personal</option>
                            @foreach($personal as $persona)
                                <option value="{{ $persona->id }}" {{ old('personal_id') == $persona->id ? 'selected' : '' }}>
                                    {{ $persona->nombre_completo }}
                                </option>
                            @endforeach
                        </select>
                        @error('personal_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Asociación con Obra -->
                    <div>
                        <label for="obra_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Obra
                        </label>
                        <select id="obra_id" name="obra_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('obra_id') border-red-500 @enderror">
                            <option value="">Sin asociar a obra</option>
                            @foreach($obras as $obra)
                                <option value="{{ $obra->id }}" {{ old('obra_id') == $obra->id ? 'selected' : '' }}>
                                    {{ $obra->nombre_obra }}
                                </option>
                            @endforeach
                        </select>
                        @error('obra_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contenido JSON (Opcional) -->
                    <div class="md:col-span-2">
                        <label for="contenido" class="block text-sm font-medium text-gray-700 mb-1">
                            Contenido JSON (Opcional)
                        </label>
                        <textarea id="contenido" name="contenido" rows="3"
                                  placeholder='{"campo": "valor"}'
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('contenido') border-red-500 @enderror">{{ old('contenido') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">
                            Para almacenar datos estructurados en formato JSON.
                        </p>
                        @error('contenido')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-4 mt-6">
                    <a href="{{ route('documentos.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        Crear Documento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Limpiar otros campos cuando se selecciona uno
document.addEventListener('DOMContentLoaded', function() {
    const vehiculoSelect = document.getElementById('vehiculo_id');
    const personalSelect = document.getElementById('personal_id');
    const obraSelect = document.getElementById('obra_id');

    function clearOtherSelects(currentSelect) {
        if (currentSelect !== vehiculoSelect) vehiculoSelect.value = '';
        if (currentSelect !== personalSelect) personalSelect.value = '';
        if (currentSelect !== obraSelect) obraSelect.value = '';
    }

    vehiculoSelect.addEventListener('change', () => clearOtherSelects(vehiculoSelect));
    personalSelect.addEventListener('change', () => clearOtherSelects(personalSelect));
    obraSelect.addEventListener('change', () => clearOtherSelects(obraSelect));
});
</script>
@endsection
