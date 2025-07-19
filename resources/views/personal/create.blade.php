@extends('layouts.app')

@section('title', 'Agregar Personal')

@section('header', 'Agregar Personal')

@section('content')
    <!-- Breadcrumb -->
    <div class="mb-4">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-petroyellow">
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('personal.index') }}" class="text-gray-700 hover:text-petroyellow ml-1 md:ml-2">Personal</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-500 ml-1 md:ml-2">Agregar</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Encabezado -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Agregar Nuevo Personal</h2>
        <a href="{{ route('personal.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Volver al listado
        </a>
    </div>

    <!-- Formulario -->
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('personal.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Información Personal -->
                <div class="space-y-6">
                    <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">
                        Información Personal
                    </h3>

                    <!-- Nombre Completo -->
                    <div>
                        <label for="nombre_completo" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre Completo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="nombre_completo" 
                               name="nombre_completo" 
                               value="{{ old('nombre_completo') }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow {{ $errors->has('nombre_completo') ? 'border-red-500' : '' }}">
                        @error('nombre_completo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- CURP -->
                    <div>
                        <label for="curp" class="block text-sm font-medium text-gray-700 mb-2">
                            CURP <span class="text-red-500">*</span>
                        </label>
                        <div class="flex space-x-2">
                            <input type="text" 
                                   id="curp" 
                                   name="curp" 
                                   value="{{ old('curp') }}"
                                   pattern="[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9]{2}"
                                   maxlength="18"
                                   placeholder="Ingrese CURP"
                                   required
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow {{ $errors->has('curp') ? 'border-red-500' : '' }}">
                            
                            <!-- Botón para subir archivo CURP -->
                            <div class="relative">
                                <input type="file" 
                                       id="curp_archivo" 
                                       name="curp_archivo" 
                                       accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                       class="hidden">
                                <button type="button" 
                                        onclick="document.getElementById('curp_archivo').click()"
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow transition duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <span class="hidden sm:inline">Archivo</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Información del archivo seleccionado -->
                        <div id="curp_archivo_info" class="mt-2 text-sm text-gray-600 hidden">
                            <div class="flex items-center space-x-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span id="curp_archivo_nombre"></span>
                                <button type="button" 
                                        onclick="clearCurpFile()"
                                        class="text-red-500 hover:text-red-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        @error('curp')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('curp_archivo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Formato: ABCD123456HMNPRS99 | Archivos: PDF, JPG, PNG, DOC, DOCX (máx. 5MB)</p>
                    </div>

                    <!-- RFC -->
                    <div>
                        <label for="rfc" class="block text-sm font-medium text-gray-700 mb-2">
                            RFC
                        </label>
                        <div class="flex space-x-2">
                            <input type="text" 
                                   id="rfc" 
                                   name="rfc" 
                                   value="{{ old('rfc') }}"
                                   maxlength="13"
                                   placeholder="Ingrese RFC"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow {{ $errors->has('rfc') ? 'border-red-500' : '' }}">
                            
                            <!-- Botón para subir archivo RFC -->
                            <div class="relative">
                                <input type="file" 
                                       id="rfc_archivo" 
                                       name="rfc_archivo" 
                                       accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                       class="hidden">
                                <button type="button" 
                                        onclick="document.getElementById('rfc_archivo').click()"
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow transition duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <span class="hidden sm:inline">Archivo</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Información del archivo seleccionado -->
                        <div id="rfc_archivo_info" class="mt-2 text-sm text-gray-600 hidden">
                            <div class="flex items-center space-x-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span id="rfc_archivo_nombre"></span>
                                <button type="button" 
                                        onclick="clearRfcFile()"
                                        class="text-red-500 hover:text-red-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        @error('rfc')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('rfc_archivo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Formatos permitidos: PDF, JPG, PNG, DOC, DOCX (máx. 5MB)</p>
                    </div>

                    <!-- Identificación (INE) -->
                    <div>
                        <label for="identificacion" class="block text-sm font-medium text-gray-700 mb-2">
                            Identificación (INE/IFE)
                        </label>
                        <div class="flex space-x-2">
                            <input type="text" 
                                   id="identificacion" 
                                   name="identificacion" 
                                   value="{{ old('identificacion') }}"
                                   maxlength="18"
                                   placeholder="Ingrese número de identificación"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow {{ $errors->has('identificacion') ? 'border-red-500' : '' }}">
                            
                            <!-- Botón para subir archivo de Identificación -->
                            <div class="relative">
                                <input type="file" 
                                       id="identificacion_archivo" 
                                       name="identificacion_archivo" 
                                       accept=".pdf,.jpg,.jpeg,.png"
                                       class="hidden">
                                <button type="button" 
                                        onclick="document.getElementById('identificacion_archivo').click()"
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow transition duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <span class="hidden sm:inline">Archivo</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Información del archivo seleccionado -->
                        <div id="identificacion_archivo_info" class="mt-2 text-sm text-gray-600 hidden">
                            <div class="flex items-center space-x-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span id="identificacion_archivo_nombre"></span>
                                <button type="button" 
                                        onclick="clearIdentificacionFile()"
                                        class="text-red-500 hover:text-red-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        @error('identificacion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('identificacion_archivo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Formatos permitidos: PDF, JPG, PNG (máx. 5MB)</p>
                    </div>

                    <!-- NSS -->
                    <div>
                        <label for="nss" class="block text-sm font-medium text-gray-700 mb-2">
                            NSS (Número de Seguridad Social)
                        </label>
                        <div class="flex space-x-2">
                            <input type="text" 
                                   id="nss" 
                                   name="nss" 
                                   value="{{ old('nss') }}"
                                   maxlength="11"
                                   placeholder="Ingrese NSS"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow {{ $errors->has('nss') ? 'border-red-500' : '' }}">
                            
                            <!-- Botón para subir archivo NSS -->
                            <div class="relative">
                                <input type="file" 
                                       id="nss_archivo" 
                                       name="nss_archivo" 
                                       accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                       class="hidden">
                                <button type="button" 
                                        onclick="document.getElementById('nss_archivo').click()"
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow transition duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <span class="hidden sm:inline">Archivo</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Información del archivo seleccionado -->
                        <div id="nss_archivo_info" class="mt-2 text-sm text-gray-600 hidden">
                            <div class="flex items-center space-x-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span id="nss_archivo_nombre"></span>
                                <button type="button" 
                                        onclick="clearNssFile()"
                                        class="text-red-500 hover:text-red-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        @error('nss')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('nss_archivo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Formatos permitidos: PDF, JPG, PNG, DOC, DOCX (máx. 5MB)</p>
                    </div>
                </div>

                <!-- Información Laboral -->
                <div class="space-y-6">
                    <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">
                        Información Laboral
                    </h3>

                    <!-- Categoría -->
                    <div>
                        <label for="categoria_personal_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Categoría <span class="text-red-500">*</span>
                        </label>
                        <select id="categoria_personal_id" 
                                name="categoria_personal_id" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow {{ $errors->has('categoria_personal_id') ? 'border-red-500' : '' }}">
                            <option value="">Seleccionar categoría</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}" {{ old('categoria_personal_id') == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nombre_categoria }}
                                </option>
                            @endforeach
                        </select>
                        @error('categoria_personal_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Estatus -->
                    <div>
                        <label for="estatus" class="block text-sm font-medium text-gray-700 mb-2">
                            Estatus <span class="text-red-500">*</span>
                        </label>
                        <select id="estatus" 
                                name="estatus" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow {{ $errors->has('estatus') ? 'border-red-500' : '' }}">
                            <option value="activo" {{ old('estatus', 'activo') == 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="inactivo" {{ old('estatus') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        @error('estatus')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- CV Profesional -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            CV Profesional
                        </label>
                        <div class="relative">
                            <input type="file" 
                                   id="cv_profesional" 
                                   name="cv_profesional" 
                                   accept=".pdf,.doc,.docx"
                                   class="hidden">
                            <button type="button" 
                                    onclick="document.getElementById('cv_profesional').click()"
                                    class="w-full inline-flex items-center justify-center px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-petroyellow focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow transition duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <div class="text-center">
                                    <div class="font-medium">Adjuntar Currículum Vitae</div>
                                    <div class="text-xs text-gray-500 mt-1">PDF, DOC, DOCX (máx. 10MB)</div>
                                </div>
                            </button>
                        </div>
                        
                        <!-- Información del CV seleccionado -->
                        <div id="cv_profesional_info" class="mt-3 text-sm text-gray-600 hidden">
                            <div class="flex items-center space-x-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="flex-1">
                                    <div class="font-medium text-green-800">CV Profesional adjuntado</div>
                                    <div id="cv_profesional_nombre" class="text-green-600"></div>
                                </div>
                                <button type="button" 
                                        onclick="clearCvFile()"
                                        class="text-red-500 hover:text-red-700 p-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        @error('cv_profesional')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Dirección -->
                    <div>
                        <label for="direccion" class="block text-sm font-medium text-gray-700 mb-2">
                            Dirección
                        </label>
                        <div class="space-y-2">
                            <textarea id="direccion" 
                                      name="direccion" 
                                      rows="3"
                                      placeholder="Ingrese la dirección completa"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow {{ $errors->has('direccion') ? 'border-red-500' : '' }}">{{ old('direccion') }}</textarea>
                            
                            <!-- Botón para subir comprobante de domicilio -->
                            <div class="flex items-center space-x-2">
                                <div class="relative flex-1">
                                    <input type="file" 
                                           id="comprobante_domicilio" 
                                           name="comprobante_domicilio" 
                                           accept=".pdf,.jpg,.jpeg,.png"
                                           class="hidden">
                                    <button type="button" 
                                            onclick="document.getElementById('comprobante_domicilio').click()"
                                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-dashed border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow transition duration-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        Subir Comprobante de Domicilio
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Información del archivo seleccionado -->
                            <div id="comprobante_domicilio_info" class="text-sm text-gray-600 hidden">
                                <div class="flex items-center space-x-2 p-3 bg-green-50 border border-green-200 rounded-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span id="comprobante_domicilio_nombre" class="flex-1"></span>
                                    <button type="button" 
                                            onclick="clearComprobanteFile()"
                                            class="text-red-500 hover:text-red-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        @error('direccion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('comprobante_domicilio')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Comprobante: PDF, JPG, PNG (máx. 5MB)</p>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="mt-8 flex justify-end space-x-4">
                <a href="{{ route('personal.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-6 rounded-md transition duration-200">
                    Cancelar
                </a>
                <button type="submit" 
                        class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-6 rounded-md transition duration-200">
                    Guardar Personal
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
// Función para validar archivo
function validateFile(file, maxSizeMB = 5, allowedTypes = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx']) {
    // Validar tamaño
    const maxSize = maxSizeMB * 1024 * 1024; // Convertir MB a bytes
    if (file.size > maxSize) {
        alert(`El archivo es demasiado grande. El tamaño máximo permitido es ${maxSizeMB}MB.`);
        return false;
    }
    
    // Validar tipo
    const fileExtension = file.name.split('.').pop().toLowerCase();
    if (!allowedTypes.includes(fileExtension)) {
        alert(`Tipo de archivo no permitido. Los tipos permitidos son: ${allowedTypes.join(', ')}`);
        return false;
    }
    
    return true;
}

// Función para mostrar información del archivo CURP
document.getElementById('curp_archivo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const infoDiv = document.getElementById('curp_archivo_info');
    const nombreSpan = document.getElementById('curp_archivo_nombre');
    
    if (file) {
        if (validateFile(file, 5, ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'])) {
            nombreSpan.textContent = file.name;
            infoDiv.classList.remove('hidden');
        } else {
            e.target.value = '';
        }
    } else {
        infoDiv.classList.add('hidden');
    }
});

// Función para limpiar archivo CURP
function clearCurpFile() {
    document.getElementById('curp_archivo').value = '';
    document.getElementById('curp_archivo_info').classList.add('hidden');
}

// Función para mostrar información del archivo RFC
document.getElementById('rfc_archivo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const infoDiv = document.getElementById('rfc_archivo_info');
    const nombreSpan = document.getElementById('rfc_archivo_nombre');
    
    if (file) {
        if (validateFile(file, 5, ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'])) {
            nombreSpan.textContent = file.name;
            infoDiv.classList.remove('hidden');
        } else {
            e.target.value = '';
        }
    } else {
        infoDiv.classList.add('hidden');
    }
});

// Función para limpiar archivo RFC
function clearRfcFile() {
    document.getElementById('rfc_archivo').value = '';
    document.getElementById('rfc_archivo_info').classList.add('hidden');
}

// Función para mostrar información del archivo NSS
document.getElementById('nss_archivo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const infoDiv = document.getElementById('nss_archivo_info');
    const nombreSpan = document.getElementById('nss_archivo_nombre');
    
    if (file) {
        if (validateFile(file, 5, ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'])) {
            nombreSpan.textContent = file.name;
            infoDiv.classList.remove('hidden');
        } else {
            e.target.value = '';
        }
    } else {
        infoDiv.classList.add('hidden');
    }
});

// Función para limpiar archivo NSS
function clearNssFile() {
    document.getElementById('nss_archivo').value = '';
    document.getElementById('nss_archivo_info').classList.add('hidden');
}

// Función para mostrar información del archivo Identificación
document.getElementById('identificacion_archivo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const infoDiv = document.getElementById('identificacion_archivo_info');
    const nombreSpan = document.getElementById('identificacion_archivo_nombre');
    
    if (file) {
        if (validateFile(file, 5, ['pdf', 'jpg', 'jpeg', 'png'])) {
            nombreSpan.textContent = file.name;
            infoDiv.classList.remove('hidden');
        } else {
            e.target.value = '';
        }
    } else {
        infoDiv.classList.add('hidden');
    }
});

// Función para limpiar archivo Identificación
function clearIdentificacionFile() {
    document.getElementById('identificacion_archivo').value = '';
    document.getElementById('identificacion_archivo_info').classList.add('hidden');
}

// Función para mostrar información del CV Profesional
document.getElementById('cv_profesional').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const infoDiv = document.getElementById('cv_profesional_info');
    const nombreSpan = document.getElementById('cv_profesional_nombre');
    
    if (file) {
        if (validateFile(file, 10, ['pdf', 'doc', 'docx'])) {
            nombreSpan.textContent = file.name;
            infoDiv.classList.remove('hidden');
        } else {
            e.target.value = '';
        }
    } else {
        infoDiv.classList.add('hidden');
    }
});

// Función para limpiar CV Profesional
function clearCvFile() {
    document.getElementById('cv_profesional').value = '';
    document.getElementById('cv_profesional_info').classList.add('hidden');
}

// Función para mostrar información del Comprobante de Domicilio
document.getElementById('comprobante_domicilio').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const infoDiv = document.getElementById('comprobante_domicilio_info');
    const nombreSpan = document.getElementById('comprobante_domicilio_nombre');
    
    if (file) {
        if (validateFile(file, 5, ['pdf', 'jpg', 'jpeg', 'png'])) {
            nombreSpan.textContent = file.name;
            infoDiv.classList.remove('hidden');
        } else {
            e.target.value = '';
        }
    } else {
        infoDiv.classList.add('hidden');
    }
});

// Función para limpiar Comprobante de Domicilio
function clearComprobanteFile() {
    document.getElementById('comprobante_domicilio').value = '';
    document.getElementById('comprobante_domicilio_info').classList.add('hidden');
}

// Validaciones del formulario
document.addEventListener('DOMContentLoaded', function() {
    // Formateo automático de CURP
    const curpInput = document.getElementById('curp');
    curpInput.addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });
    
    // Formateo automático de RFC
    const rfcInput = document.getElementById('rfc');
    rfcInput.addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });
    
    // Validación de NSS (solo números)
    const nssInput = document.getElementById('nss');
    nssInput.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
    });
});
</script>
@endpush

@push('scripts')
<script>
    // Formatear CURP en mayúsculas
    document.getElementById('curp').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
    
    // Formatear RFC en mayúsculas
    document.getElementById('rfc').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
</script>
@endpush
