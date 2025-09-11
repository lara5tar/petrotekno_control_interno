@extends('layouts.app')

@section('title', 'Registrar Kilometraje - ' . $vehiculo->marca . ' ' . $vehiculo->modelo)

@section('content')
<!-- Breadcrumb -->
<x-breadcrumb :items="[
    ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
    ['label' => 'Vehículos', 'url' => route('vehiculos.index')],
    ['label' => $vehiculo->marca . ' ' . $vehiculo->modelo, 'url' => route('vehiculos.show', $vehiculo)],
    ['label' => 'Registrar Kilometraje']
]" />

<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="bg-white border border-gray-300 rounded-lg mb-6">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-300">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-semibold text-gray-800">Registrar Nuevo Kilometraje</h1>
                        <p class="text-sm text-gray-600 mt-1">{{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->anio }})</p>
                        <p class="text-xs text-gray-500">Placas: {{ $vehiculo->placas }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-medium text-gray-600">Kilometraje Actual</div>
                        <div class="text-2xl font-bold text-blue-600">{{ number_format($vehiculo->kilometraje_actual ?? 0) }} km</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario -->
        <div class="bg-white border border-gray-300 rounded-lg">
            <div class="p-6">
                <form action="{{ route('vehiculos.kilometrajes.store.vehiculo', $vehiculo) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Kilometraje -->
                    <div>
                        <label for="kilometraje" class="block text-sm font-medium text-gray-700 mb-2">
                            Nuevo Kilometraje <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="kilometraje" 
                               id="kilometraje" 
                               value="{{ old('kilometraje') }}"
                               min="{{ ($vehiculo->kilometraje_actual ?? 0) + 1 }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('kilometraje') border-red-500 @enderror"
                               placeholder="Ingrese el nuevo kilometraje"
                               required>
                        @error('kilometraje')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            El kilometraje debe ser mayor a {{ number_format($vehiculo->kilometraje_actual ?? 0) }} km
                        </p>
                    </div>

                    <!-- Fecha de Captura -->
                    <div>
                        <label for="fecha_captura" class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha de Captura <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="fecha_captura" 
                               id="fecha_captura" 
                               value="{{ old('fecha_captura', date('Y-m-d')) }}"
                               max="{{ date('Y-m-d') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('fecha_captura') border-red-500 @enderror"
                               required>
                        @error('fecha_captura')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Imagen del Odómetro -->
                    <div>
                        <label for="imagen" class="block text-sm font-medium text-gray-700 mb-2">
                            Fotografía del Odómetro
                        </label>
                        <input type="file" 
                               name="imagen" 
                               id="imagen" 
                               accept="image/jpeg,image/png,image/jpg"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('imagen') border-red-500 @enderror">
                        @error('imagen')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            Suba una foto del odómetro del vehículo. Máximo 5MB. Formatos: JPG, PNG.
                        </p>
                    </div>

                    <!-- Cantidad de Combustible -->
                    <div>
                        <label for="cantidad_combustible" class="block text-sm font-medium text-gray-700 mb-2">
                            Cantidad de Combustible (Litros)
                        </label>
                        <input type="number" 
                               name="cantidad_combustible" 
                               id="cantidad_combustible" 
                               value="{{ old('cantidad_combustible') }}"
                               min="0"
                               max="9999.99"
                               step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('cantidad_combustible') border-red-500 @enderror"
                               placeholder="Ej: 50.5">
                        @error('cantidad_combustible')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            Cantidad de combustible cargado en litros (opcional)
                        </p>
                    </div>

                    <!-- Observaciones -->
                    <div>
                        <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                            Observaciones
                        </label>
                        <textarea name="observaciones" 
                                  id="observaciones" 
                                  rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('observaciones') border-red-500 @enderror"
                                  placeholder="Comentarios adicionales sobre el kilometraje...">{{ old('observaciones') }}</textarea>
                        @error('observaciones')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Botones -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('vehiculos.show', $vehiculo) }}" 
                           class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                            <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Registrar Kilometraje
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Actualizar el placeholder del kilometraje en tiempo real
    document.getElementById('kilometraje').addEventListener('input', function() {
        const valor = this.value;
        if (valor) {
            // Formatear el número con comas
            const valorFormateado = new Intl.NumberFormat().format(valor);
            console.log('Kilometraje ingresado:', valorFormateado + ' km');
        }
    });

    // Previsualizar imagen
    document.getElementById('imagen').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Verificar tamaño del archivo (5MB = 5 * 1024 * 1024 bytes)
            if (file.size > 5 * 1024 * 1024) {
                alert('El archivo es demasiado grande. El tamaño máximo es 5MB.');
                this.value = '';
                return;
            }
            
            // Verificar tipo de archivo
            const tiposPermitidos = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!tiposPermitidos.includes(file.type)) {
                alert('Tipo de archivo no válido. Use JPG o PNG.');
                this.value = '';
                return;
            }
        }
    });
</script>
@endsection