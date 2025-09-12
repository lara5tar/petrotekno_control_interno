@extends('layouts.app')

@section('title', 'Crear Kilometraje')

@section('header', 'Nuevo Registro de Kilometraje')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Kilometrajes', 'url' => route('kilometrajes.index')],
        ['label' => 'Nuevo Registro']
    ]" />

    <!-- Encabezado -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Nuevo Registro de Kilometraje</h2>
        <a href="{{ route('kilometrajes.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded flex items-center transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Volver al listado
        </a>
    </div>

    <!-- Alertas de validación -->
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            <div class="font-bold">Por favor corrige los siguientes errores:</div>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Formulario -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="POST" action="{{ route('kilometrajes.store') }}" id="kilometraje-form">
            @csrf

            <!-- Selección de Vehículo -->
            <div class="mb-6">
                <label for="vehiculo_id" class="block text-sm font-medium text-gray-700 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" clip-rule="evenodd" />
                    </svg>
                    Vehículo *
                </label>
                <div class="relative">
                    <select 
                        id="vehiculo_id" 
                        name="vehiculo_id" 
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('vehiculo_id') border-red-500 @enderror"
                        onchange="actualizarInfoVehiculo()">
                        <option value="">Selecciona un vehículo</option>
                        @foreach($vehiculos as $vehiculo)
                        <option value="{{ $vehiculo->id }}" 
                                data-ultimo-km="{{ $vehiculo->kilometraje_actual ?? 0 }}"
                                data-placas="{{ $vehiculo->placas }}"
                                data-marca="{{ $vehiculo->marca }}"
                                data-modelo="{{ $vehiculo->modelo }}"
                                {{ old('vehiculo_id') == $vehiculo->id ? 'selected' : '' }}>
                            {{ $vehiculo->marca }} {{ $vehiculo->modelo }} - {{ $vehiculo->placas }} ({{ number_format($vehiculo->kilometraje_actual ?? 0) }} km)
                        </option>
                        @endforeach
                    </select>
                </div>
                @error('vehiculo_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror

                <!-- Info dinámica del vehículo -->
                <div id="vehiculo-info" class="mt-3 p-4 bg-blue-50 border border-blue-200 rounded-md" style="display: none;">
                    <h6 class="font-semibold text-blue-800 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        Información del Vehículo
                    </h6>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <strong class="text-sm">Placas:</strong> <span id="info-placas" class="text-sm">-</span><br>
                            <strong class="text-sm">Marca/Modelo:</strong> <span id="info-marca-modelo" class="text-sm">-</span>
                        </div>
                        <div>
                            <strong class="text-sm">Último kilometraje:</strong> 
                            <span id="ultimo-km-display" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">0 km</span><br>
                            <small class="text-gray-600">El nuevo kilometraje debe ser mayor a este valor</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kilometraje y Fecha -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="kilometraje" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.414L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                        Kilometraje Actual *
                    </label>
                    <div class="relative">
                        <input type="number" 
                               class="w-full p-2 pr-12 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kilometraje') border-red-500 @enderror" 
                               id="kilometraje" 
                               name="kilometraje" 
                               value="{{ old('kilometraje') }}"
                               required 
                               min="1" 
                               max="9999999"
                               placeholder="Ej: 25000"
                               onchange="validarKilometraje()">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <span class="text-gray-500 sm:text-sm">km</span>
                        </div>
                    </div>
                    @error('kilometraje')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-600 text-sm mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        Ingresa la lectura exacta del odómetro
                    </p>
                </div>

                <div>
                    <label for="fecha_captura" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                        </svg>
                        Fecha de Captura *
                    </label>
                    <input type="date" 
                           class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('fecha_captura') border-red-500 @enderror" 
                           id="fecha_captura" 
                           name="fecha_captura" 
                           value="{{ old('fecha_captura', date('Y-m-d')) }}"
                           required
                           max="{{ date('Y-m-d') }}">
                    @error('fecha_captura')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Cantidad de Combustible -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="cantidad_combustible" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
                        </svg>
                        Cantidad de Combustible (Litros)
                    </label>
                    <input type="number" 
                           class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('cantidad_combustible') border-red-500 @enderror" 
                           id="cantidad_combustible" 
                           name="cantidad_combustible" 
                           value="{{ old('cantidad_combustible') }}"
                           min="0"
                           max="9999.99"
                           step="0.01"
                           placeholder="Ej: 50.5">
                    @error('cantidad_combustible')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-600 text-sm mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        Cantidad de combustible cargado (opcional)
                    </p>
                </div>
            </div>

            <!-- Observaciones -->
            <div class="mb-6">
                <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v6a1 1 0 01-1 1H9.414l-3.707 3.707A1 1 0 014 14.414V11a1 1 0 01-1-1V4z" clip-rule="evenodd" />
                    </svg>
                    Observaciones
                </label>
                <textarea class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('observaciones') border-red-500 @enderror" 
                          id="observaciones" 
                          name="observaciones" 
                          rows="4"
                          maxlength="500"
                          placeholder="Ej: Recorrido a sitio de trabajo, abastecimiento de combustible, mantenimiento preventivo...">{{ old('observaciones') }}</textarea>
                @error('observaciones')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-600 text-sm mt-1">
                    <span id="contador-caracteres">0</span>/500 caracteres
                </p>
            </div>

            <!-- Campos ocultos -->
            <input type="hidden" name="usuario_captura_id" value="{{ auth()->id() }}">

            <!-- Botones -->
            <div class="flex justify-end gap-4">
                <a href="{{ route('kilometrajes.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded transition duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Cancelar
                </a>
                <button type="submit" class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded transition duration-200" id="btn-guardar">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M7.707 10.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V6h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h5v5.586l-1.293-1.293zM9 4a1 1 0 012 0v2H9V4z" />
                    </svg>
                    Registrar Kilometraje
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar contador de caracteres
    actualizarContador();
    
    // Si hay un vehículo pre-seleccionado, mostrar info
    if (document.getElementById('vehiculo_id').value) {
        actualizarInfoVehiculo();
    }
    
    // Configurar evento para el selector de vehículos
    document.getElementById('vehiculo_id').addEventListener('change', function() {
        actualizarInfoVehiculo();
    });
});

function actualizarInfoVehiculo() {
    const vehiculoSelect = document.getElementById('vehiculo_id');
    const vehiculoId = vehiculoSelect.value;
    const info = document.getElementById('vehiculo-info');
    const placasDisplay = document.getElementById('info-placas');
    const marcaModeloDisplay = document.getElementById('info-marca-modelo');
    const kmDisplay = document.getElementById('ultimo-km-display');
    const kilometrajeInput = document.getElementById('kilometraje');
    
    if (vehiculoId) {
        // Obtener la opción seleccionada
        const vehiculoOption = vehiculoSelect.options[vehiculoSelect.selectedIndex];
        if (vehiculoOption) {
            const ultimoKm = parseInt(vehiculoOption.dataset.ultimoKm) || 0;
            const placas = vehiculoOption.dataset.placas;
            const marca = vehiculoOption.dataset.marca;
            const modelo = vehiculoOption.dataset.modelo;
            
            // Actualizar displays
            placasDisplay.textContent = placas;
            marcaModeloDisplay.textContent = marca + ' ' + modelo;
            kmDisplay.textContent = ultimoKm.toLocaleString() + ' km';
            
            // Configurar validación de kilometraje
            kilometrajeInput.min = ultimoKm + 1;
            
            // Mostrar info
            info.style.display = 'block';
            
            // Validar kilometraje actual si ya hay valor
            if (kilometrajeInput.value) {
                validarKilometraje();
            }
        }
    } else {
        info.style.display = 'none';
        kilometrajeInput.min = 1;
    }
}

function validarKilometraje() {
    const vehiculoSelect = document.getElementById('vehiculo_id');
    const vehiculoId = vehiculoSelect.value;
    const kilometrajeInput = document.getElementById('kilometraje');
    
    if (!vehiculoId || !kilometrajeInput.value) return;
    
    const vehiculoOption = vehiculoSelect.options[vehiculoSelect.selectedIndex];
    if (!vehiculoOption) return;
    
    const ultimoKm = parseInt(vehiculoOption.dataset.ultimoKm) || 0;
    const nuevoKm = parseInt(kilometrajeInput.value);
    
    // Limpiar clases previas
    kilometrajeInput.classList.remove('border-green-500', 'border-red-500');
    
    if (nuevoKm <= ultimoKm) {
        kilometrajeInput.classList.add('border-red-500');
        mostrarError('El kilometraje debe ser mayor al último registrado (' + ultimoKm.toLocaleString() + ' km)');
    } else if (nuevoKm - ultimoKm > 100000) {
        kilometrajeInput.classList.add('border-red-500');
        mostrarError('El incremento parece excesivo. ¿Estás seguro del valor?');
    } else {
        kilometrajeInput.classList.add('border-green-500');
        limpiarError();
    }
}

function mostrarError(mensaje) {
    let errorDiv = document.getElementById('kilometraje-error');
    if (!errorDiv) {
        errorDiv = document.createElement('p');
        errorDiv.id = 'kilometraje-error';
        errorDiv.className = 'text-red-500 text-sm mt-1';
        document.getElementById('kilometraje').parentNode.appendChild(errorDiv);
    }
    errorDiv.textContent = mensaje;
    errorDiv.style.display = 'block';
}

function limpiarError() {
    const errorDiv = document.getElementById('kilometraje-error');
    if (errorDiv) {
        errorDiv.style.display = 'none';
    }
}

function actualizarContador() {
    const textarea = document.getElementById('observaciones');
    const contador = document.getElementById('contador-caracteres');
    
    textarea.addEventListener('input', function() {
        contador.textContent = this.value.length;
        
        if (this.value.length > 450) {
            contador.style.color = '#dc2626';
        } else if (this.value.length > 400) {
            contador.style.color = '#f59e0b';
        } else {
            contador.style.color = '#6b7280';
        }
    });
    
    // Trigger inicial
    contador.textContent = textarea.value.length;
}

// Validación antes de enviar
document.getElementById('kilometraje-form').addEventListener('submit', function(e) {
    const vehiculoSelect = document.getElementById('vehiculo_id');
    const vehiculoId = vehiculoSelect.value;
    const kilometrajeInput = document.getElementById('kilometraje');
    
    if (!vehiculoId) {
        e.preventDefault();
        alert('Por favor selecciona un vehículo');
        vehiculoSelect.focus();
        return;
    }
    
    if (!kilometrajeInput.value) {
        e.preventDefault();
        alert('Por favor ingresa el kilometraje');
        kilometrajeInput.focus();
        return;
    }
    
    // Validar kilometraje
    const vehiculoOption = vehiculoSelect.options[vehiculoSelect.selectedIndex];
    if (vehiculoOption) {
        const ultimoKm = parseInt(vehiculoOption.dataset.ultimoKm) || 0;
        const nuevoKm = parseInt(kilometrajeInput.value);
        
        if (nuevoKm <= ultimoKm) {
            e.preventDefault();
            alert('El kilometraje debe ser mayor al último registrado (' + ultimoKm.toLocaleString() + ' km)');
            kilometrajeInput.focus();
            return;
        }
        
        // Confirmar si hay gran diferencia
        if (nuevoKm - ultimoKm > 50000) {
            if (!confirm('El incremento de kilometraje es muy alto (' + (nuevoKm - ultimoKm).toLocaleString() + ' km). ¿Estás seguro?')) {
                e.preventDefault();
                return;
            }
        }
    }
    
    // Deshabilitar botón para evitar doble envío
    document.getElementById('btn-guardar').disabled = true;
});
</script>
@endpush
