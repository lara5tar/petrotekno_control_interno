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
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Información Personal</h3>
                    <p class="text-sm text-gray-500">Datos básicos del empleado</p>
                </div>
            </div>

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
                    <label for="categoria_id" class="block text-sm font-medium text-gray-700 mb-1">Categoría *</label>
                    <select id="categoria_id" name="categoria_id" required 
                            class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('categoria_id') border-red-500 @enderror">
                        <option value="">Seleccione una categoría</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->nombre_categoria }}
                            </option>
                        @endforeach
                    </select>
                    @error('categoria_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="estatus" class="block text-sm font-medium text-gray-700 mb-1">Estatus *</label>
                    <select id="estatus" name="estatus" required 
                            class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('estatus') border-red-500 @enderror">
                        <option value="">Seleccione un estatus</option>
                        <option value="activo" {{ old('estatus') == 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ old('estatus') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                    @error('estatus')
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
            <div class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Identificación (INE)</label>
                        <div class="space-y-2">
                            <input type="text" name="numero_ine" placeholder="Número de INE" 
                                   value="{{ old('numero_ine') }}"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow">
                            <div class="relative">
                                <input type="file" name="archivo_ine" accept=".pdf,.jpg,.jpeg,.png" 
                                       class="hidden" id="archivo_ine" x-on:change="handleFileInput($event, 'ine')">
                                <label for="archivo_ine" 
                                       class="w-full p-2 border-2 border-dashed border-gray-300 rounded-md text-center cursor-pointer hover:border-petroyellow transition duration-200 flex flex-col items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <span class="text-sm text-gray-500">Clic para subir o arrastrar y soltar</span>
                                    <span class="text-xs text-gray-400">PDF, PNG, JPG (MAX. 10MB)</span>
                                </label>
                                <div x-show="fileStatus.ine" x-text="fileStatus.ine" class="mt-1 text-sm text-green-600"></div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">CURP</label>
                        <div class="space-y-2">
                            <input type="text" name="curp" placeholder="Ej: PEGJ801015HDFXXX01" 
                                   value="{{ old('curp') }}"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow">
                            <div class="relative">
                                <input type="file" name="archivo_curp" accept=".pdf,.jpg,.jpeg,.png" 
                                       class="hidden" id="archivo_curp" x-on:change="handleFileInput($event, 'curp')">
                                <label for="archivo_curp" 
                                       class="w-full p-2 border-2 border-dashed border-gray-300 rounded-md text-center cursor-pointer hover:border-petroyellow transition duration-200 flex flex-col items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <span class="text-sm text-gray-500">Clic para subir o arrastrar y soltar</span>
                                    <span class="text-xs text-gray-400">PDF, PNG, JPG (MAX. 10MB)</span>
                                </label>
                                <div x-show="fileStatus.curp" x-text="fileStatus.curp" class="mt-1 text-sm text-green-600"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Documentos Fiscales y Laborales --}}
            <div class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">RFC</label>
                        <div class="space-y-2">
                            <input type="text" name="rfc" placeholder="Ej: PEGJ801015ABC" 
                                   value="{{ old('rfc') }}"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow">
                            <div class="relative">
                                <input type="file" name="archivo_rfc" accept=".pdf,.jpg,.jpeg,.png" 
                                       class="hidden" id="archivo_rfc" x-on:change="handleFileInput($event, 'rfc')">
                                <label for="archivo_rfc" 
                                       class="w-full p-2 border-2 border-dashed border-gray-300 rounded-md text-center cursor-pointer hover:border-petroyellow transition duration-200 flex flex-col items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <span class="text-sm text-gray-500">Clic para subir o arrastrar y soltar</span>
                                    <span class="text-xs text-gray-400">PDF, PNG, JPG (MAX. 10MB)</span>
                                </label>
                                <div x-show="fileStatus.rfc" x-text="fileStatus.rfc" class="mt-1 text-sm text-green-600"></div>
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
                                <input type="file" name="archivo_nss" accept=".pdf,.jpg,.jpeg,.png" 
                                       class="hidden" id="archivo_nss" x-on:change="handleFileInput($event, 'nss')">
                                <label for="archivo_nss" 
                                       class="w-full p-2 border-2 border-dashed border-gray-300 rounded-md text-center cursor-pointer hover:border-petroyellow transition duration-200 flex flex-col items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <span class="text-sm text-gray-500">Clic para subir o arrastrar y soltar</span>
                                    <span class="text-xs text-gray-400">PDF, PNG, JPG (MAX. 10MB)</span>
                                </label>
                                <div x-show="fileStatus.nss" x-text="fileStatus.nss" class="mt-1 text-sm text-green-600"></div>
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
                            <input type="text" name="numero_licencia" placeholder="Número de Licencia" 
                                   value="{{ old('numero_licencia') }}"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow">
                            <div class="relative">
                                <input type="file" name="archivo_licencia" accept=".pdf,.jpg,.jpeg,.png" 
                                       class="hidden" id="archivo_licencia" x-on:change="handleFileInput($event, 'licencia')">
                                <label for="archivo_licencia" 
                                       class="w-full p-2 border-2 border-dashed border-gray-300 rounded-md text-center cursor-pointer hover:border-petroyellow transition duration-200 flex flex-col items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <span class="text-sm text-gray-500">Clic para subir o arrastrar y soltar</span>
                                    <span class="text-xs text-gray-400">PDF, PNG, JPG (MAX. 10MB)</span>
                                </label>
                                <div x-show="fileStatus.licencia" x-text="fileStatus.licencia" class="mt-1 text-sm text-green-600"></div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">CV Profesional</label>
                        <div class="relative">
                            <input type="file" name="archivo_cv" accept=".pdf,.doc,.docx" 
                                   class="hidden" id="archivo_cv" x-on:change="handleFileInput($event, 'cv')">
                            <label for="archivo_cv" 
                                   class="w-full p-2 border-2 border-dashed border-gray-300 rounded-md text-center cursor-pointer hover:border-petroyellow transition duration-200 flex flex-col items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <span class="text-sm text-gray-500">Clic para subir o arrastrar y soltar</span>
                                <span class="text-xs text-gray-400">PDF, DOC, DOCX (MAX. 10MB)</span>
                            </label>
                            <div x-show="fileStatus.cv" x-text="fileStatus.cv" class="mt-1 text-sm text-green-600"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Comprobante de Domicilio --}}
            <div>
                <div class="space-y-4">
                    <div>
                        <label for="direccion_completa" class="block text-sm font-medium text-gray-700 mb-1">Dirección completa</label>
                        <textarea id="direccion_completa" name="direccion_completa" rows="3" 
                                  placeholder="Calle, número, colonia, ciudad, estado, código postal..."
                                  class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow">{{ old('direccion_completa') }}</textarea>
                    </div>
                    <div>

                        <div class="relative">
                            <input type="file" name="archivo_comprobante_domicilio" accept=".pdf,.jpg,.jpeg,.png" 
                                   class="hidden" id="archivo_comprobante_domicilio" x-on:change="handleFileInput($event, 'comprobante')">
                            <label for="archivo_comprobante_domicilio" 
                                   class="w-full p-2 border-2 border-dashed border-gray-300 rounded-md text-center cursor-pointer hover:border-petroyellow transition duration-200 flex flex-col items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <span class="text-sm text-gray-500">Clic para subir comprobante de domicilio</span>
                                <span class="text-xs text-gray-400">PDF, PNG, JPG (MAX. 10MB)</span>
                            </label>
                            <div x-show="fileStatus.comprobante" x-text="fileStatus.comprobante" class="mt-1 text-sm text-green-600"></div>
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
                                <p class="text-sm text-blue-700">
                                    Al marcar esta opción se creará que el personal tenga acceso al sistema. 
                                    Se generarán credenciales de acceso automáticamente.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico *</label>
                            <input type="email" id="email" name="email" 
                                   x-bind:readonly="!crearUsuario"
                                   placeholder="correo@ejemplo.com"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow bg-gray-50">
                            <p class="mt-1 text-xs text-gray-500">
                                Se generará automáticamente basado en el nombre del empleado
                            </p>
                        </div>

                        <div>
                            <label for="rol_id" class="block text-sm font-medium text-gray-700 mb-1">Rol en el Sistema *</label>
                            <select id="rol_id" name="rol_id" 
                                    x-bind:required="crearUsuario"
                                    class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow">
                                <option value="">Seleccione un rol</option>
                                @foreach($roles as $rol)
                                    <option value="{{ $rol->id }}">{{ $rol->nombre_rol }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Configuración de Contraseña</label>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input type="radio" id="password_random" name="password_type" value="random" 
                                       x-model="passwordType" checked
                                       class="h-4 w-4 text-petroyellow focus:ring-petroyellow border-gray-300">
                                <label for="password_random" class="ml-2 block text-sm text-gray-900">
                                    <span class="font-medium">Generar contraseña aleatoria</span>
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded ml-2">Recomendado</span>
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input type="radio" id="password_manual" name="password_type" value="manual" 
                                       x-model="passwordType"
                                       class="h-4 w-4 text-petroyellow focus:ring-petroyellow border-gray-300">
                                <label for="password_manual" class="ml-2 block text-sm text-gray-900">
                                    Crear contraseña manualmente
                                </label>
                            </div>

                            <div x-show="passwordType === 'manual'" x-transition class="pl-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                                        <input type="password" id="password" name="password" 
                                               placeholder="Contraseña segura"
                                               class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow">
                                    </div>
                                    <div>
                                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña</label>
                                        <input type="password" id="password_confirmation" name="password_confirmation" 
                                               placeholder="Confirmar contraseña"
                                               class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow">
                                    </div>
                                </div>
                            </div>

                            <div x-show="passwordType === 'random'" x-transition class="pl-6">
                                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-yellow-800">Información importante sobre el acceso</h3>
                                            <div class="mt-2 text-sm text-yellow-700">
                                                <ul class="list-disc pl-5 space-y-1">
                                                    <li>El usuario podrá acceder al sistema con las credenciales creadas</li>
                                                    <li>Las primeras dependeencias del rol asignado</li>
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
@endsection
