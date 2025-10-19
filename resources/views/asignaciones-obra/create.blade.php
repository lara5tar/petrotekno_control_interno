@extends('layouts.app')

@section('title', 'Nueva Asignación')

@section('header', 'Gestión de Asignaciones')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Asignaciones', 'url' => route('asignaciones-obra.index')],
        ['label' => 'Nueva Asignación']
    ]" />

    {{-- Mensajes de estado --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if(session('warning'))
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6" role="alert">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <span>{{ session('warning') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            <div class="flex items-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <div>
                    <strong class="font-bold">¡Errores de validación!</strong>
                    <ul class="mt-1 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Header principal --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Nueva Asignación de Obra</h2>
        <a href="{{ route('asignaciones-obra.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Volver al listado
        </a>
    </div>

    {{-- Formulario principal --}}
    <form action="{{ route('asignaciones-obra.store') }}" method="POST" x-data="asignacionController()" class="space-y-6">
        @csrf

        {{-- Información de la Asignación --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center mb-6">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Datos de la Asignación</h3>
                    <p class="text-sm text-gray-600">Seleccione los elementos para la nueva asignación</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Selección de Obra --}}
                <div>
                    <label for="obra_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Obra de Destino
                        <span class="text-red-500">*</span>
                    </label>
                    <select id="obra_id" 
                            name="obra_id" 
                            required
                            class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('obra_id') border-red-500 @else border-gray-300 @enderror">
                        <option value="">Seleccione una obra</option>
                        @foreach($obras as $obra)
                            <option value="{{ $obra->id }}" 
                                    {{ (old('obra_id', $obra_preseleccionada ?? '') == $obra->id) ? 'selected' : '' }}
                                    data-estatus="{{ $obra->estatus }}">
                                {{ $obra->nombre_obra }}
                            </option>
                        @endforeach
                    </select>
                    @error('obra_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p x-show="obraSeleccionada" class="mt-1 text-sm text-gray-600">
                        <span class="font-medium">Estatus:</span>
                        <span x-text="estatusObra" class="capitalize"></span>
                    </p>
                </div>

                {{-- Selección de Vehículo --}}
                <div>
                    <label for="vehiculo_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Vehículo
                        <span class="text-red-500">*</span>
                    </label>
                    <select id="vehiculo_id" 
                            name="vehiculo_id" 
                            required
                            x-model="vehiculoSeleccionado"
                            @change="actualizarVehiculoInfo()"
                            class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('vehiculo_id') border-red-500 @else border-gray-300 @enderror">
                        <option value="">Seleccione un vehículo</option>
                        @foreach($vehiculos as $vehiculo)
                            <option value="{{ $vehiculo->id }}" {{ old('vehiculo_id') == $vehiculo->id ? 'selected' : '' }}
                                    data-marca="{{ $vehiculo->marca }}"
                                    data-modelo="{{ $vehiculo->modelo }}"
                                    data-placas="{{ $vehiculo->placas }}"
                                    data-kilometraje="{{ $vehiculo->kilometraje_actual }}">
                                {{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->placas }})
                            </option>
                        @endforeach
                    </select>
                    @error('vehiculo_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p x-show="vehiculoSeleccionado" class="mt-1 text-sm text-gray-600">
                        <span class="font-medium">Kilometraje actual:</span>
                        <span x-text="kilometrajeActual" class="font-mono"></span> km
                    </p>
                </div>

                {{-- Selección de Operador --}}
                <div>
                    <label for="personal_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Operador
                        <span class="text-red-500">*</span>
                    </label>
                    <select id="personal_id" 
                            name="personal_id" 
                            required
                            class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('personal_id') border-red-500 @else border-gray-300 @enderror">
                        <option value="">Seleccione un operador</option>
                        @foreach($operadores as $operador)
                            <option value="{{ $operador->id }}" {{ old('personal_id') == $operador->id ? 'selected' : '' }}>
                                {{ $operador->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                    @error('personal_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Información Técnica --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center mb-6">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Información Técnica</h3>
                    <p class="text-sm text-gray-600">Registre los datos técnicos de la asignación</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Kilometraje Inicial --}}
                <div>
                    <label for="kilometraje_inicial" class="block text-sm font-medium text-gray-700 mb-2">
                        Kilometraje Inicial
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" 
                               id="kilometraje_inicial" 
                               name="kilometraje_inicial" 
                               value="{{ old('kilometraje_inicial') }}" 
                               required
                               min="0"
                               x-model="kilometrajeInicial"
                               class="w-full px-3 py-2 pr-12 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kilometraje_inicial') border-red-500 @else border-gray-300 @enderror"
                               placeholder="0">
                        <span class="absolute right-3 top-2 text-gray-500">km</span>
                    </div>
                    @error('kilometraje_inicial')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <button type="button" 
                            x-show="vehiculoSeleccionado"
                            @click="usarKilometrajeActual()"
                            class="mt-2 text-sm text-blue-600 hover:text-blue-800 font-medium">
                        Usar kilometraje actual del vehículo
                    </button>
                </div>

                {{-- Combustible Inicial --}}
                <div>
                    <label for="combustible_inicial" class="block text-sm font-medium text-gray-700 mb-2">
                        Combustible Inicial
                        <span class="text-gray-400">(Opcional)</span>
                    </label>
                    <div class="relative">
                        <input type="number" 
                               id="combustible_inicial" 
                               name="combustible_inicial" 
                               value="{{ old('combustible_inicial') }}" 
                               min="0"
                               max="1000"
                               step="0.01"
                               class="w-full px-3 py-2 pr-12 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('combustible_inicial') border-red-500 @else border-gray-300 @enderror"
                               placeholder="0.00">
                        <span class="absolute right-3 top-2 text-gray-500">L</span>
                    </div>
                    @error('combustible_inicial')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Cantidad de combustible en litros al momento de la asignación</p>
                </div>
            </div>
        </div>

        {{-- Observaciones --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center mb-6">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Observaciones</h3>
                    <p class="text-sm text-gray-600">Información adicional sobre la asignación</p>
                </div>
            </div>

            <div>
                <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                    Notas y Observaciones
                </label>
                <textarea id="observaciones" 
                          name="observaciones" 
                          rows="4"
                          maxlength="1000"
                          class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('observaciones') border-red-500 @else border-gray-300 @enderror"
                          placeholder="Ingrese cualquier observación relevante sobre esta asignación...">{{ old('observaciones') }}</textarea>
                @error('observaciones')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Máximo 1000 caracteres</p>
            </div>
        </div>

        {{-- Botones de acción --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                <a href="{{ route('asignaciones-obra.index') }}" 
                   class="w-full sm:w-auto px-6 py-3 border border-gray-300 rounded-md shadow-sm bg-white text-gray-700 font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200 text-center">
                    Cancelar
                </a>
                <button type="submit" 
                        :disabled="!puedeGuardar"
                        :class="puedeGuardar ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-400 cursor-not-allowed'"
                        class="w-full sm:w-auto px-6 py-3 border border-transparent rounded-md shadow-sm text-white font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    Crear Asignación
                </button>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    function asignacionController() {
        return {
            obraSeleccionada: '{{ old('obra_id', $obra_preseleccionada ?? '') }}',
            vehiculoSeleccionado: '{{ old('vehiculo_id') }}',
            operadorSeleccionado: '{{ old('personal_id') }}',
            kilometrajeInicial: '{{ old('kilometraje_inicial') }}',
            
            // Información dinámica
            estatusObra: '',
            kilometrajeActual: 0,

            get puedeGuardar() {
                return this.obraSeleccionada && 
                       this.vehiculoSeleccionado && 
                       this.operadorSeleccionado && 
                       this.kilometrajeInicial;
            },

            actualizarObraInfo() {
                const select = document.getElementById('obra_id');
                const option = select.options[select.selectedIndex];
                if (option && option.dataset.estatus) {
                    this.estatusObra = option.dataset.estatus.replace('_', ' ');
                }
            },

            actualizarVehiculoInfo() {
                const select = document.getElementById('vehiculo_id');
                const option = select.options[select.selectedIndex];
                if (option && option.dataset.kilometraje) {
                    this.kilometrajeActual = parseInt(option.dataset.kilometraje);
                }
            },

            usarKilometrajeActual() {
                if (this.kilometrajeActual > 0) {
                    this.kilometrajeInicial = this.kilometrajeActual;
                }
            },

            init() {
                // Inicializar información si hay valores previos
                if (this.obraSeleccionada) {
                    this.actualizarObraInfo();
                }
                if (this.vehiculoSeleccionado) {
                    this.actualizarVehiculoInfo();
                }
            }
        };
    }
</script>
@endpush
