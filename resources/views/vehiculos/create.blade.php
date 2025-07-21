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
                                <option value="1" {{ old('estatus_id') == '1' ? 'selected' : '' }}>Disponible</option>
                                <option value="2" {{ old('estatus_id') == '2' ? 'selected' : '' }}>En Uso</option>
                                <option value="3" {{ old('estatus_id') == '3' ? 'selected' : '' }}>Mantenimiento</option>
                                <option value="4" {{ old('estatus_id') == '4' ? 'selected' : '' }}>Fuera de Servicio</option>
                                <option value="5" {{ old('estatus_id') == '5' ? 'selected' : '' }}>Baja</option>
                            </select>
                            @error('estatus_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
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

                <!-- Sección de Documentos del Vehículo -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h4 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" clip-rule="evenodd" />
                        </svg>
                        Documentos del Vehículo
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- 1. Tarjeta de Circulación -->
                        <div class="space-y-3">
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

                        <!-- 2. Póliza de Seguro -->
                        <div class="space-y-3">
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

                        <!-- 3. Verificación Vehicular -->
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700">
                                Verificación Vehicular
                            </label>
                            <div class="flex items-center space-x-3">
                                <input type="text" 
                                       name="no_verificacion" 
                                       placeholder="Folio de verificación" 
                                       value="{{ old('no_verificacion') }}"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                                <div class="flex-shrink-0">
                                    <input type="file" 
                                           id="verificacion_file" 
                                           name="verificacion_file" 
                                           accept=".pdf,.jpg,.jpeg,.png" 
                                           class="hidden" 
                                           @change="handleFileInput($event, 'verificacion')" />
                                    <label for="verificacion_file" 
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
                                       name="fecha_vencimiento_verificacion" 
                                       placeholder="Próxima verificación"
                                       value="{{ old('fecha_vencimiento_verificacion') }}"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                                <span class="text-sm text-gray-500 flex-shrink-0">Próxima verificación</span>
                            </div>
                            <p class="text-xs text-gray-500" x-text="fileStatus.verificacion || 'PDF, JPG, PNG (máx. 5MB)'"></p>
                        </div>

                        <!-- 4. Tenencia/Refrendo -->
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700">
                                Tenencia/Refrendo
                            </label>
                            <div class="flex items-center space-x-3">
                                <select name="tenencia_anio" 
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow">
                                    <option value="">Año de tenencia</option>
                                    @for($year = date('Y'); $year >= (date('Y') - 10); $year--)
                                        <option value="{{ $year }}" {{ old('tenencia_anio') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endfor
                                </select>
                                <div class="flex-shrink-0">
                                    <input type="file" 
                                           id="tenencia_file" 
                                           name="tenencia_file" 
                                           accept=".pdf,.jpg,.jpeg,.png" 
                                           class="hidden" 
                                           @change="handleFileInput($event, 'tenencia')" />
                                    <label for="tenencia_file" 
                                           class="cursor-pointer inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        Adjuntar
                                    </label>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500" x-text="fileStatus.tenencia || 'PDF, JPG, PNG (máx. 5MB)'"></p>
                        </div>

                        <!-- 5. Factura Original -->
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700">
                                Factura Original
                            </label>
                            <div class="flex items-center space-x-3">
                                <input type="text" 
                                       name="no_factura" 
                                       placeholder="Número de factura" 
                                       value="{{ old('no_factura') }}"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                                <div class="flex-shrink-0">
                                    <input type="file" 
                                           id="factura_file" 
                                           name="factura_file" 
                                           accept=".pdf,.jpg,.jpeg,.png" 
                                           class="hidden" 
                                           @change="handleFileInput($event, 'factura')" />
                                    <label for="factura_file" 
                                           class="cursor-pointer inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        Adjuntar
                                    </label>
                                </div>
                            </div>
                            <div class="mt-2">
                                <input type="date" 
                                       name="fecha_compra" 
                                       placeholder="Fecha de compra"
                                       value="{{ old('fecha_compra') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                            </div>
                            <p class="text-xs text-gray-500" x-text="fileStatus.factura || 'PDF, JPG, PNG (máx. 5MB)'"></p>
                        </div>

                        <!-- 6. Manual del Vehículo -->
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700">
                                Manual del Vehículo
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-md p-4 text-center hover:border-petroyellow transition-colors">
                                <svg class="mx-auto h-6 w-6 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <input type="file" 
                                       id="manual_file" 
                                       name="manual_file" 
                                       accept=".pdf,.doc,.docx" 
                                       class="hidden" 
                                       @change="handleFileInput($event, 'manual')" />
                                <label for="manual_file" 
                                       class="cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                                    Seleccionar Manual
                                </label>
                                <p class="mt-2 text-xs text-gray-500" x-show="!fileStatus.manual">
                                    PDF, DOC, DOCX (máx. 10MB)
                                </p>
                                <p class="mt-2 text-sm text-petroyellow font-medium" x-show="fileStatus.manual" x-text="fileStatus.manual">
                                </p>
                            </div>
                        </div>
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
                verificacion: '',
                tenencia: '',
                factura: '',
                manual: ''
            },
            
            handleFileInput(event, type) {
                const file = event.target.files[0];
                if (!file) {
                    this.fileStatus[type] = '';
                    return;
                }

                // Validar tamaño (10MB para manual, 5MB para otros)
                const maxSize = type === 'manual' ? 10 * 1024 * 1024 : 5 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert(`El archivo es demasiado grande. Máximo ${maxSize / 1024 / 1024}MB`);
                    event.target.value = '';
                    this.fileStatus[type] = '';
                    return;
                }

                // Validar tipo de archivo
                const allowedTypes = type === 'manual' 
                    ? ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
                    : ['application/pdf', 'image/jpeg', 'image/png'];
                
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