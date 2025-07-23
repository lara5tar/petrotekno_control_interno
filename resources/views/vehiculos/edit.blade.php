@extends('layouts.app')

@section('title', 'Editar Vehículo')

@section('header', 'Editar Vehículo')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Encabezado -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Editar Vehículo: {{ $vehiculo->placas }}</h2>
            <div class="flex space-x-2">
                <a href="{{ route('vehiculos.show', $vehiculo->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                    </svg>
                    Ver detalles
                </a>
                <a href="{{ route('vehiculos.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Volver al listado
                </a>
            </div>
        </div>

        <!-- Mensajes de error -->
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulario -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <form method="POST" action="{{ route('vehiculos.update', $vehiculo->id) }}">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Marca -->
                    <div>
                        <label for="marca" class="block text-sm font-medium text-gray-700 mb-2">Marca *</label>
                        <input type="text" 
                               id="marca" 
                               name="marca" 
                               value="{{ old('marca', $vehiculo->marca) }}" 
                               required 
                               class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Ej: Toyota, Ford, Caterpillar">
                        @error('marca')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Modelo -->
                    <div>
                        <label for="modelo" class="block text-sm font-medium text-gray-700 mb-2">Modelo *</label>
                        <input type="text" 
                               id="modelo" 
                               name="modelo" 
                               value="{{ old('modelo', $vehiculo->modelo) }}" 
                               required 
                               class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Ej: Hilux, Ranger, 320D">
                        @error('modelo')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Año -->
                    <div>
                        <label for="anio" class="block text-sm font-medium text-gray-700 mb-2">Año *</label>
                        <input type="number" 
                               id="anio" 
                               name="anio" 
                               value="{{ old('anio', $vehiculo->anio) }}" 
                               required 
                               min="1990" 
                               max="{{ date('Y') + 1 }}"
                               class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Ej: {{ date('Y') }}">
                        @error('anio')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Número de Serie -->
                    <div>
                        <label for="n_serie" class="block text-sm font-medium text-gray-700 mb-2">Número de Serie *</label>
                        <input type="text" 
                               id="n_serie" 
                               name="n_serie" 
                               value="{{ old('n_serie', $vehiculo->n_serie) }}" 
                               required 
                               class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Número de serie del vehículo">
                        @error('n_serie')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Placas -->
                    <div>
                        <label for="placas" class="block text-sm font-medium text-gray-700 mb-2">Placas *</label>
                        <input type="text" 
                               id="placas" 
                               name="placas" 
                               value="{{ old('placas', $vehiculo->placas) }}" 
                               required 
                               class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Ej: ABC-123"
                               style="text-transform: uppercase;">
                        @error('placas')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Estado -->
                    <div>
                        <label for="estatus_id" class="block text-sm font-medium text-gray-700 mb-2">Estado *</label>
                        <select id="estatus_id" 
                                name="estatus_id" 
                                required 
                                class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Seleccionar estado</option>
                            @foreach($estatusOptions as $estatus)
                                <option value="{{ $estatus->id }}" 
                                        {{ (old('estatus_id', $vehiculo->estatus_id) == $estatus->id) ? 'selected' : '' }}>
                                    {{ $estatus->nombre_estatus }}
                                </option>
                            @endforeach
                        </select>
                        @error('estatus_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kilometraje Actual -->
                    <div>
                        <label for="kilometraje_actual" class="block text-sm font-medium text-gray-700 mb-2">Kilometraje Actual</label>
                        <div class="relative">
                            <input type="number" 
                                   id="kilometraje_actual" 
                                   name="kilometraje_actual" 
                                   value="{{ old('kilometraje_actual', $vehiculo->kilometraje_actual) }}" 
                                   min="0"
                                   class="w-full p-3 pr-12 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="0">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">km</span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Nota: El kilometraje se actualiza automáticamente con los registros de kilometrajes</p>
                        @error('kilometraje_actual')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Intervalo Motor (Opcional) -->
                    <div>
                        <label for="intervalo_km_motor" class="block text-sm font-medium text-gray-700 mb-2">Intervalo Mantenimiento Motor (km)</label>
                        <div class="relative">
                            <input type="number" 
                                   id="intervalo_km_motor" 
                                   name="intervalo_km_motor" 
                                   value="{{ old('intervalo_km_motor', $vehiculo->intervalo_km_motor) }}" 
                                   min="0"
                                   class="w-full p-3 pr-12 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Ej: 10000">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">km</span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Opcional: Cada cuántos kilómetros requiere mantenimiento de motor</p>
                        @error('intervalo_km_motor')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Intervalo Transmisión (Opcional) -->
                    <div>
                        <label for="intervalo_km_transmision" class="block text-sm font-medium text-gray-700 mb-2">Intervalo Mantenimiento Transmisión (km)</label>
                        <div class="relative">
                            <input type="number" 
                                   id="intervalo_km_transmision" 
                                   name="intervalo_km_transmision" 
                                   value="{{ old('intervalo_km_transmision', $vehiculo->intervalo_km_transmision) }}" 
                                   min="0"
                                   class="w-full p-3 pr-12 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Ej: 50000">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">km</span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Opcional: Cada cuántos kilómetros requiere mantenimiento de transmisión</p>
                        @error('intervalo_km_transmision')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Intervalo Hidráulico (Opcional) -->
                    <div>
                        <label for="intervalo_km_hidraulico" class="block text-sm font-medium text-gray-700 mb-2">Intervalo Mantenimiento Hidráulico (km)</label>
                        <div class="relative">
                            <input type="number" 
                                   id="intervalo_km_hidraulico" 
                                   name="intervalo_km_hidraulico" 
                                   value="{{ old('intervalo_km_hidraulico', $vehiculo->intervalo_km_hidraulico) }}" 
                                   min="0"
                                   class="w-full p-3 pr-12 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Ej: 25000">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">km</span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Opcional: Cada cuántos kilómetros requiere mantenimiento hidráulico</p>
                        @error('intervalo_km_hidraulico')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Información de fechas -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                        <div>
                            <strong>Creado:</strong> {{ $vehiculo->created_at->format('d/m/Y H:i') }}
                        </div>
                        @if($vehiculo->updated_at != $vehiculo->created_at)
                        <div>
                            <strong>Última actualización:</strong> {{ $vehiculo->updated_at->format('d/m/Y H:i') }}
                        </div>
                        @endif
                        @if($vehiculo->deleted_at)
                        <div class="text-red-600">
                            <strong>Eliminado:</strong> {{ $vehiculo->deleted_at->format('d/m/Y H:i') }}
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('vehiculos.show', $vehiculo->id) }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-md transition duration-150 ease-in-out">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md transition duration-150 ease-in-out">
                        Actualizar Vehículo
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Convertir placas a mayúsculas automáticamente
        document.getElementById('placas').addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase();
        });
    </script>
@endsection
