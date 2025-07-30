@extends('layouts.app')

@section('title', 'Agregar Personal')

@section('header', 'Agregar Personal')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Personal', 'url' => route('personal.index')],
        ['label' => 'Agregar Personal']
    ]" />

    <!-- Encabezado -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Agregar Nuevo Personal</h2>
        @hasPermission('ver_personal')
        <a href="{{ route('personal.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Volver al listado
        </a>
        @endhasPermission
    </div>

    <!-- Formulario -->
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('personal.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="space-y-8">
                <!-- Información Personal -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                        Información Personal
                    </h3>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div class="form-group">
                            <label for="nombre_completo" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre Completo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="nombre_completo" 
                                   id="nombre_completo"
                                   value="{{ old('nombre_completo') }}"
                                   placeholder="Nombre completo del empleado" 
                                   required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow transition duration-200 @error('nombre_completo') border-red-500 @enderror" />
                            @error('nombre_completo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div class="form-group">
                            <label for="categoria_personal_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Categoría <span class="text-red-500">*</span>
                            </label>
                            <select name="categoria_personal_id" 
                                    id="categoria_personal_id" 
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('categoria_personal_id') border-red-500 @enderror">
                                <option value="">Seleccione una categoría</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" {{ old('categoria_personal_id') == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->nombre_categoria }}
                                    </option>
                                @endforeach
                            </select>
                            @error('categoria_personal_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="estatus" class="block text-sm font-medium text-gray-700 mb-2">
                                Estatus <span class="text-red-500">*</span>
                            </label>
                            <select name="estatus" 
                                    id="estatus" 
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('estatus') border-red-500 @enderror">
                                <option value="">Seleccione el estatus</option>
                                <option value="activo" {{ old('estatus') == 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ old('estatus') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('estatus') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Sección de Documentos -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h4 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" clip-rule="evenodd" />
                        </svg>
                        Documentos del Personal (Opcional)
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- 1. Identificación INE -->
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700">
                                Identificación (INE)
                            </label>
                            <div class="flex items-center space-x-3">
                                <input type="text" 
                                       name="ine" 
                                       value="{{ old('ine') }}"
                                       placeholder="Número de INE" 
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('ine') border-red-500 @enderror" />
                                <div class="flex-shrink-0">
                                    <input type="file" 
                                           id="identificacion_file" 
                                           name="identificacion_file" />
                                </div>
                            </div>
                            @error('ine') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            @error('identificacion_file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- 2. CURP -->
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700">CURP</label>
                            <div class="flex items-center space-x-3">
                                <input type="text" 
                                       name="curp_numero" 
                                       value="{{ old('curp_numero') }}"
                                       placeholder="Ingrese CURP" 
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('curp_numero') border-red-500 @enderror" />
                                <div class="flex-shrink-0">
                                    <input type="file" 
                                           id="curp_file" 
                                           name="curp_file" />
                                </div>
                            </div>
                            @error('curp_numero') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            @error('curp_file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- 3. RFC -->
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700">RFC</label>
                            <div class="flex items-center space-x-3">
                                <input type="text" 
                                       name="rfc" 
                                       value="{{ old('rfc') }}"
                                       placeholder="Ingrese RFC" 
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('rfc') border-red-500 @enderror" />
                                <div class="flex-shrink-0">
                                    <input type="file" 
                                           id="rfc_file" 
                                           name="rfc_file" />
                                </div>
                            </div>
                            @error('rfc') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            @error('rfc_file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- 4. NSS -->
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700">NSS</label>
                            <div class="flex items-center space-x-3">
                                <input type="text" 
                                       name="nss" 
                                       value="{{ old('nss') }}"
                                       placeholder="Número de Seguro Social" 
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('nss') border-red-500 @enderror" />
                                <div class="flex-shrink-0">
                                    <input type="file" 
                                           id="nss_file" 
                                           name="nss_file" />
                                </div>
                            </div>
                            @error('nss') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            @error('nss_file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- 5. Licencia de Manejo -->
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700">
                                Licencia de Manejo
                            </label>
                            <div class="flex items-center space-x-3">
                                <input type="text" 
                                       name="no_licencia" 
                                       value="{{ old('no_licencia') }}"
                                       placeholder="Número de Licencia" 
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('no_licencia') border-red-500 @enderror" />
                                <div class="flex-shrink-0">
                                    <input type="file" 
                                           id="licencia_file" 
                                           name="licencia_file" />
                                </div>
                            </div>
                            @error('no_licencia') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            @error('licencia_file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Comprobante de Domicilio -->
                    <div class="mt-6 space-y-3">
                        <label class="block text-sm font-medium text-gray-700">
                            Comprobante de Domicilio
                        </label>
                        <textarea name="direccion" 
                                rows="2" 
                                placeholder="Dirección completa" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('direccion') border-red-500 @enderror">{{ old('direccion') }}</textarea>
                        @error('direccion') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        <div class="flex items-center">
                            <input type="file" 
                                   id="comprobante_file" 
                                   name="comprobante_file" />
                        </div>
                        @error('comprobante_file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- CV Profesional -->
                    <div class="mt-6 space-y-3">
                        <label class="block text-sm font-medium text-gray-700">
                            CV Profesional
                        </label>
                        <input type="file" 
                                id="cv_file" 
                                name="cv_file" />
                        @error('cv_file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="mt-8 flex justify-end space-x-4">
                @hasPermission('ver_personal')
                <a href="{{ route('personal.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                    Cancelar
                </a>
                @endhasPermission
                @hasPermission('crear_personal')
                <button type="submit" 
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-petrodark bg-petroyellow hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                    Guardar Personal
                </button>
                @endhasPermission
            </div>
        </form>
    </div>
@endsection

