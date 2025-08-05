@extends('layouts.app')

@section('title', 'Editar Kilometraje')

@section('header', 'Editar Registro de Kilometraje')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Kilometrajes', 'url' => route('kilometrajes.index')],
        ['label' => 'Editar Registro']
    ]" />

    <!-- Encabezado -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Editar Registro de Kilometraje</h2>
            <p class="text-gray-600 mt-1">
                Modificar registro del {{ $kilometraje->fecha_captura->format('d/m/Y') }} - {{ $kilometraje->vehiculo->placas }}
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('kilometrajes.show', $kilometraje) }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded flex items-center transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                </svg>
                Ver detalles
            </a>
            <a href="{{ route('kilometrajes.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded flex items-center transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Volver al listado
            </a>
        </div>
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
        <form method="POST" action="{{ route('kilometrajes.update', $kilometraje) }}" id="kilometraje-form">
            @csrf
            @method('PUT')

            <!-- Información del Vehículo (Solo lectura) -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" clip-rule="evenodd" />
                    </svg>
                    Vehículo
                </label>
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-md border-l-4 border-l-blue-500">
                    <div class="flex items-center">
                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">{{ $kilometraje->vehiculo->placas }}</div>
                            <div class="text-gray-600">
                                {{ $kilometraje->vehiculo->marca }} {{ $kilometraje->vehiculo->modelo }} 
                                ({{ $kilometraje->vehiculo->anio }})
                            </div>
                            <small class="text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                No se puede cambiar el vehículo en la edición
                            </small>
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
                        Kilometraje *
                    </label>
                    <div class="relative">
                        <input type="number" 
                               class="w-full p-2 pr-12 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kilometraje') border-red-500 @enderror" 
                               id="kilometraje" 
                               name="kilometraje" 
                               value="{{ old('kilometraje', $kilometraje->kilometraje) }}"
                               required 
                               min="1" 
                               max="9999999"
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
                        Valor actual: {{ number_format($kilometraje->kilometraje) }} km
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
                           value="{{ old('fecha_captura', $kilometraje->fecha_captura->format('Y-m-d')) }}"
                           required
                           max="{{ date('Y-m-d') }}">
                    @error('fecha_captura')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-600 text-sm mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        Fecha original: {{ $kilometraje->fecha_captura->format('d/m/Y') }}
                    </p>
                </div>
            </div>

            <!-- Alertas de Validación -->
            <div id="validacion-alerts" class="mb-6"></div>

            <!-- Obra -->
            <div class="mb-6">
                <label for="obra_search" class="block text-sm font-medium text-gray-700 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-6a1 1 0 00-1-1H9a1 1 0 00-1 1v6a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd" />
                    </svg>
                    Obra Asociada
                </label>
                <div class="relative">
                    <input type="text" 
                           class="w-full p-2 pr-8 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('obra_id') border-red-500 @enderror" 
                           id="obra_search" 
                           placeholder="Busca por nombre de obra..."
                           autocomplete="off">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer" id="obra_arrow">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                    <input type="hidden" id="obra_id" name="obra_id" value="{{ old('obra_id', $kilometraje->obra_id) }}">
                    
                    <!-- Dropdown de obras -->
                    <div id="obra-dropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg mt-1 max-h-60 overflow-y-auto hidden">
                        <div class="obra-option cursor-pointer px-3 py-2 hover:bg-blue-50 border-b border-gray-100" 
                             data-id=""
                             data-text="Sin obra específica"
                             onclick="selectObra(this)">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
                                </svg>
                                <div>
                                    <div class="font-medium text-gray-500">Sin obra específica</div>
                                </div>
                            </div>
                        </div>
                        @foreach($obras as $obra)
                        <div class="obra-option cursor-pointer px-3 py-2 hover:bg-blue-50 border-b border-gray-100 last:border-b-0" 
                             data-id="{{ $obra->id }}"
                             data-text="{{ $obra->nombre_obra }}"
                             onclick="selectObra(this)">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $obra->nombre_obra }}</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @error('obra_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-600 text-sm mt-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    Obra actual: 
                    @if($kilometraje->obra)
                        {{ $kilometraje->obra->nombre_obra }}
                    @else
                        <span class="text-gray-500">Sin obra específica</span>
                    @endif
                </p>
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
                          placeholder="Ej: Recorrido a obra zona norte, abastecimiento de combustible, mantenimiento preventivo...">{{ old('observaciones', $kilometraje->observaciones) }}</textarea>
                @error('observaciones')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-600 text-sm mt-1">
                    <span id="contador-caracteres">{{ strlen($kilometraje->observaciones ?? '') }}</span>/500 caracteres
                </p>
            </div>

            <!-- Información de Auditoría -->
            <div class="mb-6">
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-md border-l-4 border-l-blue-500">
                    <h6 class="font-semibold text-blue-800 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        Información de Registro
                    </h6>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <small class="text-blue-700">
                                <strong>Registrado por:</strong> {{ $kilometraje->usuarioCaptura->name }}<br>
                                <strong>Fecha de registro:</strong> {{ $kilometraje->created_at->format('d/m/Y H:i') }}
                            </small>
                        </div>
                        @if($kilometraje->updated_at != $kilometraje->created_at)
                        <div>
                            <small class="text-blue-700">
                                <strong>Última modificación:</strong> {{ $kilometraje->updated_at->format('d/m/Y H:i') }}
                            </small>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end gap-4">
                <a href="{{ route('kilometrajes.show', $kilometraje) }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded transition duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Cancelar
                </a>
                <button type="submit" class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded transition duration-200" id="btn-actualizar">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M7.707 10.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V6h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h5v5.586l-1.293-1.293zM9 4a1 1 0 012 0v2H9V4z" />
                    </svg>
                    Actualizar Registro
                </button>
            </div>
        </form>
    </div>
@endsection

@endsection

@push('scripts')
<script>
// Variables globales
const kilometrajeOriginal = {!! json_encode($kilometraje->kilometraje) !!};
const vehiculoId = {!! json_encode($kilometraje->vehiculo_id) !!};
const kilometrajeId = {!! json_encode($kilometraje->id) !!};

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar contador de caracteres
    actualizarContador();
    
    // Validar kilometraje inicial
    validarKilometraje();
    
    // Configurar buscador de obras si hay valor pre-seleccionado
    const obraIdActual = document.getElementById('obra_id').value;
    if (obraIdActual) {
        const obraOption = document.querySelector(`[data-id="${obraIdActual}"]`);
        if (obraOption) {
            document.getElementById('obra_search').value = obraOption.dataset.text;
        }
    }
    
    // Configurar event listeners para el dropdown de obra
    const obraSearch = document.getElementById('obra_search');
    const obraArrow = document.getElementById('obra_arrow');
    
    // Event listeners para obra
    obraSearch.addEventListener('focus', function(e) {
        e.stopPropagation();
        showObraDropdown();
    });
    
    obraSearch.addEventListener('click', function(e) {
        e.stopPropagation();
        showObraDropdown();
    });
    
    obraSearch.addEventListener('input', function() {
        filterObras();
    });
    
    obraArrow.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleObraDropdown();
        obraSearch.focus();
    });
});

// Funciones para el dropdown de obras
function toggleObraDropdown() {
    const dropdown = document.getElementById('obra-dropdown');
    dropdown.classList.toggle('hidden');
}

function showObraDropdown() {
    const dropdown = document.getElementById('obra-dropdown');
    dropdown.classList.remove('hidden');
}

function hideObraDropdown() {
    const dropdown = document.getElementById('obra-dropdown');
    dropdown.classList.add('hidden');
}

function filterObras() {
    const searchValue = document.getElementById('obra_search').value.toLowerCase();
    const options = document.querySelectorAll('.obra-option');
    
    options.forEach(option => {
        const text = option.dataset.text.toLowerCase();
        
        if (text.includes(searchValue)) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });
    
    showObraDropdown();
}

function selectObra(element) {
    const searchInput = document.getElementById('obra_search');
    const hiddenInput = document.getElementById('obra_id');
    
    searchInput.value = element.dataset.text;
    hiddenInput.value = element.dataset.id;
    
    hideObraDropdown();
}

// Cerrar dropdown al hacer clic fuera
document.addEventListener('click', function(event) {
    const obraSearch = document.getElementById('obra_search');
    const obraDropdown = document.getElementById('obra-dropdown');
    const obraArrow = document.getElementById('obra_arrow');
    
    // Cerrar dropdown de obras si se hace clic fuera de todo el componente
    const isObraClick = obraSearch.contains(event.target) || 
                       obraDropdown.contains(event.target) || 
                       obraArrow.contains(event.target);
                       
    if (!isObraClick) {
        hideObraDropdown();
    }
});

function validarKilometraje() {
    const kilometrajeInput = document.getElementById('kilometraje');
    const nuevoKm = parseInt(kilometrajeInput.value);
    const alertsContainer = document.getElementById('validacion-alerts');
    
    // Limpiar alertas previas
    alertsContainer.innerHTML = '';
    kilometrajeInput.classList.remove('border-green-500', 'border-red-500');
    
    if (!nuevoKm) return;
    
    let alertas = [];
    
    // Verificar cambios muy grandes
    const diferencia = Math.abs(nuevoKm - kilometrajeOriginal);
    if (diferencia > 50000) {
        alertas.push({
            type: 'warning',
            message: `El cambio es muy grande (${diferencia.toLocaleString()} km). ¿Estás seguro?`
        });
    }
    
    // Mostrar alertas
    alertas.forEach(alerta => {
        const alertDiv = document.createElement('div');
        let bgColor = alerta.type === 'danger' ? 'bg-red-100 border-red-400 text-red-700' : 'bg-yellow-100 border-yellow-400 text-yellow-700';
        alertDiv.className = `${bgColor} px-4 py-3 rounded border mb-2`;
        alertDiv.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            ${alerta.message}
        `;
        alertsContainer.appendChild(alertDiv);
    });
    
    // Configurar validación visual
    if (alertas.some(a => a.type === 'danger')) {
        kilometrajeInput.classList.add('border-red-500');
    } else if (alertas.length === 0) {
        kilometrajeInput.classList.add('border-green-500');
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
}

// Validación antes de enviar
document.getElementById('kilometraje-form').addEventListener('submit', function(e) {
    const kilometrajeInput = document.getElementById('kilometraje');
    const nuevoKm = parseInt(kilometrajeInput.value);
    
    if (!nuevoKm) {
        e.preventDefault();
        alert('Por favor ingresa un kilometraje válido');
        kilometrajeInput.focus();
        return;
    }
    
    // Confirmar cambios grandes
    const diferencia = Math.abs(nuevoKm - kilometrajeOriginal);
    if (diferencia > 50000) {
        if (!confirm(`El cambio es muy grande (${diferencia.toLocaleString()} km). ¿Estás seguro de continuar?`)) {
            e.preventDefault();
            return;
        }
    }
    
    // Deshabilitar botón para evitar doble envío
    document.getElementById('btn-actualizar').disabled = true;
});

// Validar en tiempo real
document.getElementById('kilometraje').addEventListener('input', function() {
    // Debounce para evitar muchas llamadas
    clearTimeout(this.validationTimeout);
    this.validationTimeout = setTimeout(validarKilometraje, 500);
});
</script>
@endpush
