@extends('layouts.app')

@section('title', 'Agregar Vehículo')

@section('header', 'Agregar Vehículo')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Vehículos', 'url' => route('vehiculos.index')],
        ['label' => 'Agregar Vehículo']
    ]" />

    <!-- Encabezado -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Agregar Nuevo Vehículo</h2>
        <a href="{{ route('vehiculos.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Volver al listado
        </a>
    </div>

    <!-- Formulario -->
    <div class="bg-white rounded-lg shadow p-6" x-data="vehiculoFormController()">
        <form action="{{ route('vehiculos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="space-y-8">
                <!-- Información del Vehículo -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                            <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"/>
                        </svg>
                        Información del Vehículo
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <x-form-input name="marca" label="Marca" required placeholder="Ej: Ford, Chevrolet, Toyota" />
                        <x-form-input name="modelo" label="Modelo" required placeholder="Ej: F-150, Silverado, Hilux" />
                        <x-form-input name="anio" label="Año" type="number" required min="1990" max="2025" placeholder="2023" />
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <x-form-input name="n_serie" label="Número de Serie (VIN)" required placeholder="1FTFW1ET5DFA12345" />
                        <x-form-input name="placas" label="Placas" required placeholder="ABC-123-A" />
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <x-form-input name="kilometraje_actual" label="Kilometraje Actual (km)" type="number" required min="0" placeholder="15000" />
                        
                        <div class="form-group">
                            <label for="estatus_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Estatus <span class="text-red-500">*</span>
                            </label>
                            <select name="estatus_id" 
                                    id="estatus_id" 
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('estatus_id') border-red-500 @enderror">
                                <option value="">Seleccione el estatus</option>
                                @foreach($estatus as $estatusOption)
                                    <option value="{{ $estatusOption->id }}" {{ old('estatus_id') == $estatusOption->id ? 'selected' : '' }}>
                                        {{ ucfirst($estatusOption->nombre_estatus) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('estatus_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Sección de Documentos del Vehículo -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h4 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" clip-rule="evenodd" />
                        </svg>
                        Documentos del Vehículo
                    </h4>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Primera columna -->
                        <div class="space-y-6">
                            <!-- 1. Tarjeta de Circulación -->
                            <div class="space-y-3 p-4 border border-gray-200 rounded-lg">
                                <label class="block text-sm font-medium text-gray-700">
                                    Tarjeta de Circulación
                                </label>
                                <div class="flex items-center space-x-3">
                                    <input type="text" 
                                           name="no_tarjeta_circulacion" 
                                           placeholder="Número de tarjeta de circulación" 
                                           value="{{ old('no_tarjeta_circulacion') }}"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                                    <div class="flex-shrink-0">
                                        <input type="file" 
                                               id="tarjeta_circulacion_file" 
                                               name="tarjeta_circulacion_file" 
                                               accept=".pdf,.jpg,.jpeg,.png" 
                                               class="hidden" 
                                               @change="handleFileInput($event, 'tarjeta_circulacion')" />
                                        <label for="tarjeta_circulacion_file" 
                                               class="cursor-pointer inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            Adjuntar
                                        </label>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <input type="date" 
                                           name="fecha_vencimiento_tarjeta" 
                                           placeholder="Fecha de vencimiento"
                                           value="{{ old('fecha_vencimiento_tarjeta') }}"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                                    <span class="text-sm text-gray-500 flex-shrink-0">Vencimiento</span>
                                </div>
                                <p class="text-xs text-gray-500" x-text="fileStatus.tarjeta_circulacion || 'PDF, JPG, PNG (máx. 5MB)'"></p>
                            </div>

                            <!-- 2. Derecho Vehicular -->
                            <div class="space-y-3 p-4 border border-gray-200 rounded-lg">
                                <label class="block text-sm font-medium text-gray-700">
                                    Derecho Vehicular
                                </label>
                                <div class="flex items-center space-x-3">
                                    <input type="text" 
                                           name="no_derecho_vehicular" 
                                           placeholder="Número de derecho vehicular" 
                                           value="{{ old('no_derecho_vehicular') }}"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                                    <div class="flex-shrink-0">
                                        <input type="file" 
                                               id="derecho_vehicular_file" 
                                               name="derecho_vehicular_file" 
                                               accept=".pdf,.jpg,.jpeg,.png" 
                                               class="hidden" 
                                               @change="handleFileInput($event, 'derecho_vehicular')" />
                                        <label for="derecho_vehicular_file" 
                                               class="cursor-pointer inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            Adjuntar
                                        </label>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <input type="date" 
                                           name="fecha_vencimiento_derecho" 
                                           placeholder="Fecha de vencimiento"
                                           value="{{ old('fecha_vencimiento_derecho') }}"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                                    <span class="text-sm text-gray-500 flex-shrink-0">Vencimiento</span>
                                </div>
                                <p class="text-xs text-gray-500" x-text="fileStatus.derecho_vehicular || 'PDF, JPG, PNG (máx. 5MB)'"></p>
                            </div>
                        </div>

                        <!-- Segunda columna -->
                        <div class="space-y-6">
                            <!-- 3. Póliza de Seguro -->
                            <div class="space-y-3 p-4 border border-gray-200 rounded-lg">
                                <label class="block text-sm font-medium text-gray-700">
                                    Póliza de Seguro
                                </label>
                                <div class="flex items-center space-x-3">
                                    <input type="text" 
                                           name="no_poliza_seguro" 
                                           placeholder="Número de póliza" 
                                           value="{{ old('no_poliza_seguro') }}"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                                    <div class="flex-shrink-0">
                                        <input type="file" 
                                               id="poliza_seguro_file" 
                                               name="poliza_seguro_file" 
                                               accept=".pdf,.jpg,.jpeg,.png" 
                                               class="hidden" 
                                               @change="handleFileInput($event, 'poliza_seguro')" />
                                        <label for="poliza_seguro_file" 
                                               class="cursor-pointer inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            Adjuntar
                                        </label>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <input type="date" 
                                           name="fecha_vencimiento_seguro" 
                                           placeholder="Fecha de vencimiento"
                                           value="{{ old('fecha_vencimiento_seguro') }}"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                                    <span class="text-sm text-gray-500 flex-shrink-0">Vencimiento</span>
                                </div>
                                <div class="mt-2">
                                    <input type="text" 
                                           name="aseguradora" 
                                           placeholder="Nombre de la aseguradora" 
                                           value="{{ old('aseguradora') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                                </div>
                                <p class="text-xs text-gray-500" x-text="fileStatus.poliza_seguro || 'PDF, JPG, PNG (máx. 5MB)'"></p>
                            </div>

                            <!-- 4. Factura y/o Pedimento -->
                            <div class="space-y-3 p-4 border border-gray-200 rounded-lg">
                                <label class="block text-sm font-medium text-gray-700">
                                    Factura y/o Pedimento
                                </label>
                                <div class="flex items-center space-x-3">
                                    <input type="text" 
                                           name="no_factura_pedimento" 
                                           placeholder="Número de factura y/o pedimento" 
                                           value="{{ old('no_factura_pedimento') }}"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                                    <div class="flex-shrink-0">
                                        <input type="file" 
                                               id="factura_pedimento_file" 
                                               name="factura_pedimento_file" 
                                               accept=".pdf,.jpg,.jpeg,.png" 
                                               class="hidden" 
                                               @change="handleFileInput($event, 'factura_pedimento')" />
                                        <label for="factura_pedimento_file" 
                                               class="cursor-pointer inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            Adjuntar
                                        </label>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500" x-text="fileStatus.factura_pedimento || 'PDF, JPG, PNG (máx. 5MB)'"></p>
                            </div>

                            <!-- 5. Fotografía -->
                            <div class="space-y-3 p-4 border border-gray-200 rounded-lg">
                                <label class="block text-sm font-medium text-gray-700">
                                    Fotografía del Vehículo
                                </label>
                                <div class="flex items-center space-x-3">
                                    <div class="flex-1">
                                        <input type="file" 
                                               id="fotografia_file" 
                                               name="fotografia_file" 
                                               accept=".jpg,.jpeg,.png" 
                                               class="hidden" 
                                               @change="handleFileInput($event, 'fotografia')" />
                                        <label for="fotografia_file" 
                                               class="cursor-pointer inline-flex items-center justify-center w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-petroyellow transition-colors">
                                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            Subir Fotografía del Vehículo
                                        </label>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 text-center" x-text="fileStatus.fotografia || 'JPG, PNG (máx. 5MB)'"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configuración de Mantenimiento -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                        </svg>
                        Intervalos de Mantenimiento (Opcional)
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="form-group">
                            <label for="intervalo_km_motor" class="block text-sm font-medium text-gray-700 mb-2">
                                Intervalo Aceite Motor (km)
                            </label>
                            <input type="number" 
                                   id="intervalo_km_motor" 
                                   name="intervalo_km_motor" 
                                   min="1000" 
                                   step="500"
                                   placeholder="5000"
                                   value="{{ old('intervalo_km_motor') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('intervalo_km_motor') border-red-500 @enderror">
                            @error('intervalo_km_motor') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-500">Cada cuántos kilómetros cambiar aceite de motor</p>
                        </div>
                        
                        <div class="form-group">
                            <label for="intervalo_km_transmision" class="block text-sm font-medium text-gray-700 mb-2">
                                Intervalo Aceite Transmisión (km)
                            </label>
                            <input type="number" 
                                   id="intervalo_km_transmision" 
                                   name="intervalo_km_transmision" 
                                   min="5000" 
                                   step="1000"
                                   placeholder="40000"
                                   value="{{ old('intervalo_km_transmision') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('intervalo_km_transmision') border-red-500 @enderror">
                            @error('intervalo_km_transmision') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-500">Cada cuántos kilómetros cambiar aceite de transmisión</p>
                        </div>
                        
                        <div class="form-group">
                            <label for="intervalo_km_hidraulico" class="block text-sm font-medium text-gray-700 mb-2">
                                Intervalo Aceite Hidráulico (km)
                            </label>
                            <input type="number" 
                                   id="intervalo_km_hidraulico" 
                                   name="intervalo_km_hidraulico" 
                                   min="1000" 
                                   step="500"
                                   placeholder="10000"
                                   value="{{ old('intervalo_km_hidraulico') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('intervalo_km_hidraulico') border-red-500 @enderror">
                            @error('intervalo_km_hidraulico') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-500">Cada cuántos kilómetros cambiar aceite hidráulico (si aplica)</p>
                        </div>
                    </div>
                </div>
                
                <!-- Observaciones -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        Observaciones Adicionales
                    </h3>
                    
                    <div class="form-group">
                        <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                            Notas y Observaciones
                        </label>
                        <textarea name="observaciones" 
                                id="observaciones"
                                rows="4" 
                                placeholder="Agregue cualquier información adicional sobre el vehículo (características especiales, modificaciones, etc.)" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('observaciones') border-red-500 @enderror">{{ old('observaciones') }}</textarea>
                        @error('observaciones') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="mt-8 flex justify-end space-x-4">
                <a href="{{ route('vehiculos.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-petrodark bg-petroyellow hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                    Guardar Vehículo
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script src="//unpkg.com/alpinejs" defer></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('vehiculoFormController', () => ({
            fileStatus: {
                tarjeta_circulacion: '',
                poliza_seguro: '',
                derecho_vehicular: '',
                factura_pedimento: '',
                fotografia: ''
            },
            
            handleFileInput(event, type) {
                const file = event.target.files[0];
                if (!file) {
                    this.fileStatus[type] = '';
                    return;
                }

                // Validar tamaño (5MB para todos los archivos)
                const maxSize = 5 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert(`El archivo es demasiado grande. Máximo ${maxSize / 1024 / 1024}MB`);
                    event.target.value = '';
                    this.fileStatus[type] = '';
                    return;
                }

                // Validar tipo de archivo (PDF, JPG, PNG)
                const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
                
                if (!allowedTypes.includes(file.type)) {
                    alert('Formato de archivo no permitido');
                    event.target.value = '';
                    this.fileStatus[type] = '';
                    return;
                }

                this.fileStatus[type] = `Archivo seleccionado: ${file.name}`;
            }
        }));
    });
</script>
@endpush
