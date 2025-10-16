@extends('layouts.app')

@section('title', 'Registrar Personal')

@section('header', 'Gestión de Personal')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Personal', 'url' => route('personal.index')],
        ['label' => 'Registrar Nuevo']
    ]" />

    {{-- Mensaje de éxito --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
            <strong class="font-bold">¡Éxito!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Mensaje de error --}}
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            <strong class="font-bold">¡Error!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Header principal --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Registrar Nuevo Personal</h2>
        <a href="{{ route('personal.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Volver al Listado
        </a>
    </div>

    {{-- Formulario principal --}}
    <form action="{{ route('personal.store') }}" method="POST" enctype="multipart/form-data" x-data="formController()" class="space-y-6">
        @csrf

        {{-- Información Personal --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                </svg>
                Información Personal
            </h3>
            <p class="text-sm text-gray-500 mb-6">Datos básicos del empleado</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nombre_completo" class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo *</label>
                    <input type="text" id="nombre_completo" name="nombre_completo" required 
                           value="{{ old('nombre_completo') }}"
                           placeholder="Ej: Juan Carlos Pérez García"
                           x-ref="nombreCompleto"
                           class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('nombre_completo') border-red-500 @enderror">
                    @error('nombre_completo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="categoria_personal_id" class="block text-sm font-medium text-gray-700 mb-1">Puesto *</label>
                    <select id="categoria_personal_id" name="categoria_personal_id" required 
                            class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('categoria_personal_id') border-red-500 @enderror">
                        <option value="">Seleccione un puesto</option>
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


            </div>
        </div>

        {{-- Documentos del Personal --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                </svg>
                Documentos del Personal
            </h3>
            {{-- Documentos de Identificación --}}
            <div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Identificación (INE)</label>
                        <div class="space-y-2">
                            <input type="text" name="ine" placeholder="Número de INE" 
                                   value="{{ old('ine') }}"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow">
                            <div class="relative">
                                <input type="file" name="identificacion_file" accept=".pdf,.jpg,.jpeg,.png" 
                                       class="hidden" id="identificacion_file" x-on:change="handleFileInput($event, 'ine')">
                                <label for="identificacion_file" 
                                       class="cursor-pointer inline-flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-petroyellow transition-colors">
                                    <svg class="h-8 w-8 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <span class="text-base">Subir Identificación (INE)</span>
                                </label>
                                <p class="text-xs text-gray-500 text-center mt-2" x-text="fileStatus.ine || 'PDF, PNG, JPG (máx. 10MB)'"></p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">CURP</label>
                        <div class="space-y-2">
                            <input type="text" name="curp_numero" placeholder="Ej: PEGJ801015HDFXXX01" 
                                   value="{{ old('curp_numero') }}"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow">
                            <div class="relative">
                                <input type="file" name="curp_file" accept=".pdf,.jpg,.jpeg,.png" 
                                       class="hidden" id="curp_file" x-on:change="handleFileInput($event, 'curp')">
                                <label for="curp_file" 
                                       class="cursor-pointer inline-flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-petroyellow transition-colors">
                                    <svg class="h-8 w-8 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <span class="text-base">Subir CURP</span>
                                </label>
                                <p class="text-xs text-gray-500 text-center mt-2" x-text="fileStatus.curp || 'PDF, PNG, JPG (máx. 10MB)'"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Documentos Fiscales y Laborales --}}
            <div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">RFC</label>
                        <div class="space-y-2">
                            <input type="text" name="rfc" placeholder="Ej: PEGJ801015ABC" 
                                   value="{{ old('rfc') }}"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow">
                            <div class="relative">
                                <input type="file" name="rfc_file" accept=".pdf,.jpg,.jpeg,.png" 
                                       class="hidden" id="rfc_file" x-on:change="handleFileInput($event, 'rfc')">
                                <label for="rfc_file" 
                                       class="cursor-pointer inline-flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-petroyellow transition-colors">
                                    <svg class="h-8 w-8 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <span class="text-base">Subir RFC</span>
                                </label>
                                <p class="text-xs text-gray-500 text-center mt-2" x-text="fileStatus.rfc || 'PDF, PNG, JPG (máx. 10MB)'"></p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">NSS (Número de Seguro Social)</label>
                        <div class="space-y-2">
                            <input type="text" name="nss" placeholder="Ej: 12345678901" 
                                   value="{{ old('nss') }}"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow">
                            <div class="relative">
                                <input type="file" name="nss_file" accept=".pdf,.jpg,.jpeg,.png" 
                                       class="hidden" id="nss_file" x-on:change="handleFileInput($event, 'nss')">
                                <label for="nss_file" 
                                       class="cursor-pointer inline-flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-petroyellow transition-colors">
                                    <svg class="h-8 w-8 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <span class="text-base">Subir NSS</span>
                                </label>
                                <p class="text-xs text-gray-500 text-center mt-2" x-text="fileStatus.nss || 'PDF, PNG, JPG (máx. 10MB)'"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Documentos Adicionales --}}
            <div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Licencia de Manejo</label>
                        <div class="space-y-2">
                            <input type="text" name="no_licencia" placeholder="Número de Licencia" 
                                   value="{{ old('no_licencia') }}"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow">
                            <div class="relative">
                                <input type="file" name="licencia_file" accept=".pdf,.jpg,.jpeg,.png" 
                                       class="hidden" id="licencia_file" x-on:change="handleFileInput($event, 'licencia')">
                                <label for="licencia_file" 
                                       class="cursor-pointer inline-flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-petroyellow transition-colors">
                                    <svg class="h-8 w-8 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <span class="text-base">Subir Licencia</span>
                                </label>
                                <p class="text-xs text-gray-500 text-center mt-2" x-text="fileStatus.licencia || 'PDF, PNG, JPG (máx. 10MB)'"></p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">CV Profesional</label>
                        <div class="relative">
                            <input type="file" name="cv_file" accept=".pdf,.doc,.docx" 
                                   class="hidden" id="cv_file" x-on:change="handleFileInput($event, 'cv')">
                            <label for="cv_file" 
                                   class="cursor-pointer inline-flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-petroyellow transition-colors">
                                <svg class="h-8 w-8 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <span class="text-base">Subir CV</span>
                            </label>
                            <p class="text-xs text-gray-500 text-center mt-2" x-text="fileStatus.cv || 'PDF, DOC, DOCX (máx. 10MB)'"></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Comprobante de Domicilio --}}
            <div>
                <div class="space-y-4">
                    <div>
                        <label for="direccion" class="block text-sm font-medium text-gray-700 mb-1">Dirección completa</label>
                        <textarea id="direccion" name="direccion" rows="3" 
                                  placeholder="Calle, número, colonia, ciudad, estado, código postal..."
                                  class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow">{{ old('direccion') }}</textarea>
                    </div>
                    <div>

                        <div class="relative">
                            <input type="file" name="comprobante_file" accept=".pdf,.jpg,.jpeg,.png" 
                                   class="hidden" id="comprobante_file" x-on:change="handleFileInput($event, 'comprobante')">
                            <label for="comprobante_file" 
                                   class="cursor-pointer inline-flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-petroyellow transition-colors">
                                <svg class="h-8 w-8 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <span class="text-base">Subir Comprobante</span>
                            </label>
                            <p class="text-xs text-gray-500 text-center mt-2" x-text="fileStatus.comprobante || 'PDF, PNG, JPG (máx. 10MB)'"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Acceso al Sistema --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd" />
                </svg>
                Acceso al Sistema
            </h3>
            <p class="text-sm text-gray-500 mb-6">Configurar cuenta de usuario (opcional)</p>

            <div class="space-y-4">
                <div class="flex items-center">
                    <input type="checkbox" id="crear_usuario" name="crear_usuario" value="1" 
                           x-model="crearUsuario" 
                           class="h-4 w-4 text-petroyellow focus:ring-petroyellow border-gray-300 rounded">
                    <label for="crear_usuario" class="ml-2 block text-sm text-gray-900 font-medium">
                        Crear usuario para acceso al sistema
                    </label>
                </div>

                <div x-show="crearUsuario" x-transition class="space-y-4 pl-6 border-l-2 border-gray-200">
                    <div class="bg-gray-50 border border-gray-200 rounded-md p-3">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-gray-700">
                                    Al marcar esta opción se creará que el personal tenga acceso al sistema. 
                                    Se generarán credenciales de acceso automáticamente.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="email_usuario" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico *</label>
                            <input type="email" id="email_usuario" name="email_usuario" 
                                   placeholder="correo@ejemplo.com"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow">
                            <p class="mt-1 text-xs text-gray-500">
                                Ingrese el correo electrónico personal del empleado
                            </p>
                        </div>

                        <div>
                            <label for="rol_usuario" class="block text-sm font-medium text-gray-700 mb-1">Rol en el Sistema *</label>
                            <select id="rol_usuario" name="rol_usuario" 
                                    x-bind:required="crearUsuario"
                                    class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow">
                                <option value="">Seleccione un rol</option>
                                @foreach($roles as $rol)
                                    <option value="{{ $rol->id }}">{{ $rol->nombre_rol }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Campo oculto para especificar que siempre será contraseña aleatoria --}}
                    <input type="hidden" name="tipo_password" value="aleatoria">

                    <div class="bg-gray-50 border border-gray-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-gray-800">Generación automática de contraseña</h3>
                                <div class="mt-2 text-sm text-gray-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>Se generará automáticamente una contraseña segura</li>
                                        <li>La contraseña se mostrará después de crear el usuario</li>
                                        <li>Se enviará un correo con las credenciales de acceso</li>
                                        <li>El usuario puede cambiar su contraseña desde el perfil</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Botones de acción --}}
        <div class="flex justify-end space-x-4">
            <a href="{{ route('personal.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                Cancelar
            </a>
            <button type="submit" 
                    class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded-md transition duration-200">
                Registrar Personal
            </button>
        </div>
    </form>

    <script>
        function formController() {
            return {
                crearUsuario: false,
                fileStatus: {
                    ine: '',
                    curp: '',
                    rfc: '',
                    nss: '',
                    licencia: '',
                    cv: '',
                    comprobante: ''
                },
                
                init() {
                    // Inicialización del componente
                    console.log('FormController inicializado');
                },
                
                toggleUsuario() {
                    this.crearUsuario = !this.crearUsuario;
                },

                handleFileInput(event, type) {
                    console.log(`handleFileInput llamado para tipo: ${type}`);
                    const file = event.target.files[0];
                    
                    if (file) {
                        console.log(`Archivo seleccionado: ${file.name}, tamaño: ${file.size} bytes, tipo: ${file.type}`);
                        
                        // Validar tamaño del archivo (10MB máximo)
                        const maxSize = 10 * 1024 * 1024; // 10MB en bytes
                        if (file.size > maxSize) {
                            alert('El archivo es demasiado grande. El tamaño máximo permitido es 10MB.');
                            event.target.value = '';
                            this.fileStatus[type] = '';
                            return;
                        }
                        
                        // Validar tipo de archivo según el campo
                        const allowedTypes = {
                            'ine': ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'],
                            'curp': ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'],
                            'rfc': ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'],
                            'nss': ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'],
                            'licencia': ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'],
                            'cv': ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
                            'comprobante': ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf']
                        };
                        
                        if (allowedTypes[type] && !allowedTypes[type].includes(file.type)) {
                            alert(`Tipo de archivo no válido para ${type}. Por favor selecciona un archivo válido.`);
                            event.target.value = '';
                            this.fileStatus[type] = '';
                            return;
                        }
                        
                        // Actualizar el estado del archivo
                        const fileSize = (file.size / 1024 / 1024).toFixed(2); // Convertir a MB
                        this.fileStatus[type] = `✓ ${file.name} (${fileSize} MB)`;
                        
                        console.log(`Estado del archivo actualizado: ${this.fileStatus[type]}`);
                        
                        // Cambiar el estilo del label para mostrar que se seleccionó un archivo
                        const label = event.target.parentElement.querySelector('label');
                        if (label) {
                            label.classList.add('border-green-400', 'bg-green-50');
                            label.classList.remove('border-gray-300');
                        }
                        
                    } else {
                        // Limpiar el estado si no hay archivo
                        this.fileStatus[type] = '';
                        console.log(`Archivo removido para tipo: ${type}`);
                        
                        // Restaurar el estilo original del label
                        const label = event.target.parentElement.querySelector('label');
                        if (label) {
                            label.classList.remove('border-green-400', 'bg-green-50');
                            label.classList.add('border-gray-300');
                        }
                    }
                }
            }
        }
    </script>
@endsection
