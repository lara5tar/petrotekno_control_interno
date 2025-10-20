@extends('layouts.app')

@section('title', 'Editar Personal')

@section('header', 'Gestión de Personal')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Personal', 'url' => route('personal.index')],
        ['label' => 'Editar Personal']
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
        <h2 class="text-2xl font-bold text-gray-800">Editar Personal: {{ $personal->nombre_completo }}</h2>
        <a href="{{ route('personal.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Volver al Listado
        </a>
    </div>

    {{-- Formulario principal --}}
    <form action="{{ route('personal.update', $personal->id) }}" method="POST" enctype="multipart/form-data" x-data="formController()" class="space-y-6">
        @csrf
        @method('PUT')

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
                           value="{{ old('nombre_completo', $personal->nombre_completo) }}"
                           placeholder="Ej: Juan Carlos Pérez García"
                           x-ref="nombreCompleto"
                           class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('nombre_completo') border-red-500 @enderror">
                    @error('nombre_completo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="categoria_id" class="block text-sm font-medium text-gray-700 mb-1">Puesto *</label>
                    <select id="categoria_id" name="categoria_id" required 
                            class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('categoria_id') border-red-500 @enderror">
                        <option value="">Seleccione un puesto</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}" {{ old('categoria_id', $personal->categoria_id) == $categoria->id ? 'selected' : '' }}>
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
                        <option value="activo" {{ old('estatus', $personal->estatus) == 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ old('estatus', $personal->estatus) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
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
                            <input type="text" name="ine" placeholder="Número de INE" 
                                   value="{{ old('ine', $personal->ine ?? '') }}"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow">
                            <div class="relative">
                                @if($personal->url_ine)
                                    <div class="mb-2 p-2 bg-green-50 border border-green-200 rounded-md">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-green-700">✓ Archivo actual: INE</span>
                                            <a href="{{ asset('storage/' . $personal->url_ine) }}" target="_blank" 
                                               class="text-blue-600 hover:text-blue-800 text-sm underline">Ver archivo</a>
                                        </div>
                                    </div>
                                @endif
                                <input type="file" name="archivo_ine" accept=".pdf,.jpg,.jpeg,.png" 
                                       class="hidden" id="archivo_ine" x-on:change="handleFileInput($event, 'ine')">
                                <label for="archivo_ine" 
                                       class="w-full p-2 border-2 border-dashed border-gray-300 rounded-md text-center cursor-pointer hover:border-petroyellow transition duration-200 flex flex-col items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <span class="text-sm text-gray-500">{{ $personal->url_ine ? 'Cambiar archivo' : 'Clic para subir o arrastrar y soltar' }}</span>
                                    <span class="text-xs text-gray-400">PDF, PNG, JPG (MAX. 10MB)</span>
                                </label>
                                <div x-show="fileStatus.ine" x-text="fileStatus.ine" class="mt-1 text-sm text-green-600"></div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">CURP</label>
                        <div class="space-y-2">
                            <input type="text" name="curp_numero" placeholder="Ej: PEGJ801015HDFXXX01" 
                                   value="{{ old('curp_numero', $personal->curp_numero ?? '') }}"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow">
                            <div class="relative">
                                @if($personal->url_curp)
                                    <div class="mb-2 p-2 bg-green-50 border border-green-200 rounded-md">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-green-700">✓ Archivo actual: CURP</span>
                                            <a href="{{ asset('storage/' . $personal->url_curp) }}" target="_blank" 
                                               class="text-blue-600 hover:text-blue-800 text-sm underline">Ver archivo</a>
                                        </div>
                                    </div>
                                @endif
                                <input type="file" name="archivo_curp" accept=".pdf,.jpg,.jpeg,.png" 
                                       class="hidden" id="archivo_curp" x-on:change="handleFileInput($event, 'curp')">
                                <label for="archivo_curp" 
                                       class="w-full p-2 border-2 border-dashed border-gray-300 rounded-md text-center cursor-pointer hover:border-petroyellow transition duration-200 flex flex-col items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <span class="text-sm text-gray-500">{{ $personal->url_curp ? 'Cambiar archivo' : 'Clic para subir o arrastrar y soltar' }}</span>
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
                                   value="{{ old('rfc', $personal->rfc ?? '') }}"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow">
                            <div class="relative">
                                @if($personal->url_rfc)
                                    <div class="mb-2 p-2 bg-green-50 border border-green-200 rounded-md">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-green-700">✓ Archivo actual: RFC</span>
                                            <a href="{{ asset('storage/' . $personal->url_rfc) }}" target="_blank" 
                                               class="text-blue-600 hover:text-blue-800 text-sm underline">Ver archivo</a>
                                        </div>
                                    </div>
                                @endif
                                <input type="file" name="archivo_rfc" accept=".pdf,.jpg,.jpeg,.png" 
                                       class="hidden" id="archivo_rfc" x-on:change="handleFileInput($event, 'rfc')">
                                <label for="archivo_rfc" 
                                       class="w-full p-2 border-2 border-dashed border-gray-300 rounded-md text-center cursor-pointer hover:border-petroyellow transition duration-200 flex flex-col items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <span class="text-sm text-gray-500">{{ $personal->url_rfc ? 'Cambiar archivo' : 'Clic para subir o arrastrar y soltar' }}</span>
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
                                   value="{{ old('nss', $personal->nss ?? '') }}"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow">
                            <div class="relative">
                                @if($personal->url_nss)
                                    <div class="mb-2 p-2 bg-green-50 border border-green-200 rounded-md">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-green-700">✓ Archivo actual: NSS</span>
                                            <a href="{{ asset('storage/' . $personal->url_nss) }}" target="_blank" 
                                               class="text-blue-600 hover:text-blue-800 text-sm underline">Ver archivo</a>
                                        </div>
                                    </div>
                                @endif
                                <input type="file" name="archivo_nss" accept=".pdf,.jpg,.jpeg,.png" 
                                       class="hidden" id="archivo_nss" x-on:change="handleFileInput($event, 'nss')">
                                <label for="archivo_nss" 
                                       class="w-full p-2 border-2 border-dashed border-gray-300 rounded-md text-center cursor-pointer hover:border-petroyellow transition duration-200 flex flex-col items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <span class="text-sm text-gray-500">{{ $personal->url_nss ? 'Cambiar archivo' : 'Clic para subir o arrastrar y soltar' }}</span>
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
                            <input type="text" name="no_licencia" placeholder="Número de Licencia" 
                                   value="{{ old('no_licencia', $personal->no_licencia ?? '') }}"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow">
                            <div class="relative">
                                @if($personal->url_licencia)
                                    <div class="mb-2 p-2 bg-green-50 border border-green-200 rounded-md">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-green-700">✓ Archivo actual: Licencia</span>
                                            <a href="{{ asset('storage/' . $personal->url_licencia) }}" target="_blank" 
                                               class="text-blue-600 hover:text-blue-800 text-sm underline">Ver archivo</a>
                                        </div>
                                    </div>
                                @endif
                                <input type="file" name="archivo_licencia" accept=".pdf,.jpg,.jpeg,.png" 
                                       class="hidden" id="archivo_licencia" x-on:change="handleFileInput($event, 'licencia')">
                                <label for="archivo_licencia" 
                                       class="w-full p-2 border-2 border-dashed border-gray-300 rounded-md text-center cursor-pointer hover:border-petroyellow transition duration-200 flex flex-col items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <span class="text-sm text-gray-500">{{ $personal->url_licencia ? 'Cambiar archivo' : 'Clic para subir o arrastrar y soltar' }}</span>
                                    <span class="text-xs text-gray-400">PDF, PNG, JPG (MAX. 10MB)</span>
                                </label>
                                <div x-show="fileStatus.licencia" x-text="fileStatus.licencia" class="mt-1 text-sm text-green-600"></div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">CV Profesional</label>
                        <div class="relative">
                            @if($personal->url_cv)
                                <div class="mb-2 p-2 bg-green-50 border border-green-200 rounded-md">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-green-700">✓ Archivo actual: CV</span>
                                        <a href="{{ asset('storage/' . $personal->url_cv) }}" target="_blank" 
                                           class="text-blue-600 hover:text-blue-800 text-sm underline">Ver archivo</a>
                                    </div>
                                </div>
                            @endif
                            <input type="file" name="archivo_cv" accept=".pdf,.doc,.docx" 
                                   class="hidden" id="archivo_cv" x-on:change="handleFileInput($event, 'cv')">
                            <label for="archivo_cv" 
                                   class="w-full p-2 border-2 border-dashed border-gray-300 rounded-md text-center cursor-pointer hover:border-petroyellow transition duration-200 flex flex-col items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <span class="text-sm text-gray-500">{{ $personal->url_cv ? 'Cambiar archivo' : 'Clic para subir o arrastrar y soltar' }}</span>
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
                        <label for="direccion" class="block text-sm font-medium text-gray-700 mb-1">Dirección completa</label>
                        <textarea id="direccion" name="direccion" rows="3" 
                                  placeholder="Calle, número, colonia, ciudad, estado, código postal..."
                                  class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow">{{ old('direccion', $personal->direccion ?? '') }}</textarea>
                    </div>
                    <div>

                        <div class="relative">
                            @if($personal->url_comprobante_domicilio)
                                <div class="mb-2 p-2 bg-green-50 border border-green-200 rounded-md">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-green-700">✓ Archivo actual: Comprobante de Domicilio</span>
                                        <a href="{{ asset('storage/' . $personal->url_comprobante_domicilio) }}" target="_blank" 
                                           class="text-blue-600 hover:text-blue-800 text-sm underline">Ver archivo</a>
                                    </div>
                                </div>
                            @endif
                            <input type="file" name="archivo_comprobante_domicilio" accept=".pdf,.jpg,.jpeg,.png" 
                                   class="hidden" id="archivo_comprobante_domicilio" x-on:change="handleFileInput($event, 'comprobante')">
                            <label for="archivo_comprobante_domicilio" 
                                   class="w-full p-2 border-2 border-dashed border-gray-300 rounded-md text-center cursor-pointer hover:border-petroyellow transition duration-200 flex flex-col items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <span class="text-sm text-gray-500">{{ $personal->url_comprobante_domicilio ? 'Cambiar archivo' : 'Clic para subir comprobante de domicilio' }}</span>
                                <span class="text-xs text-gray-400">PDF, PNG, JPG (MAX. 10MB)</span>
                            </label>
                            <div x-show="fileStatus.comprobante" x-text="fileStatus.comprobante" class="mt-1 text-sm text-green-600"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

         {{-- Información de Usuario del Sistema --}}
         <div class="bg-white border border-gray-200 rounded-lg p-6">
             <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                     <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
                 </svg>
                 Información de Usuario del Sistema
             </h3>
             
             @if($personal->usuario)
                 {{-- Mostrar información del usuario existente --}}
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                     <div>
                         <label class="block text-sm font-medium text-gray-700 mb-2">Correo Electrónico</label>
                         <div class="bg-gray-50 border border-gray-200 px-4 py-3 rounded-lg text-sm text-gray-800">
                             {{ $personal->usuario->email }}
                         </div>
                     </div>
                     <div>
                         <label class="block text-sm font-medium text-gray-700 mb-2">Rol</label>
                         <div class="bg-gray-50 border border-gray-200 px-4 py-3 rounded-lg text-sm text-gray-800">
                             <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                 {{ $personal->usuario->role === 'admin' ? 'bg-red-100 text-red-800' : '' }}
                                 {{ $personal->usuario->role === 'encargado' ? 'bg-blue-100 text-blue-800' : '' }}
                                 {{ $personal->usuario->role === 'operador' ? 'bg-green-100 text-green-800' : '' }}">
                                 {{ ucfirst($personal->usuario->role) }}
                             </span>
                         </div>
                     </div>
                     <div>
                         <label class="block text-sm font-medium text-gray-700 mb-2">Estado de la Cuenta</label>
                         <div class="bg-gray-50 border border-gray-200 px-4 py-3 rounded-lg text-sm text-gray-800">
                             @if($personal->usuario->is_active)
                                 <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                     <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                         <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                     </svg>
                                     Activo
                                 </span>
                             @else
                                 <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                     <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                         <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                     </svg>
                                     Inactivo
                                 </span>
                             @endif
                         </div>
                     </div>
                     <div>
                         <label class="block text-sm font-medium text-gray-700 mb-2">Usuario Creado el</label>
                         <div class="bg-gray-50 border border-gray-200 px-4 py-3 rounded-lg text-sm text-gray-800">
                             {{ $personal->usuario->created_at ? $personal->usuario->created_at->format('d/m/Y H:i') : 'No disponible' }}
                         </div>
                     </div>
                 </div>
             @else
                 {{-- Formulario para crear usuario si no tiene --}}
                 <div class="space-y-4">
                     <div class="flex items-center">
                         <input type="checkbox" id="crear_usuario_nuevo" name="crear_usuario" value="1" 
                                x-model="crearUsuario" 
                                class="h-4 w-4 text-petroyellow focus:ring-petroyellow border-gray-300 rounded">
                         <label for="crear_usuario_nuevo" class="ml-2 block text-sm text-gray-900 font-medium">
                             Crear usuario para acceso al sistema
                         </label>
                     </div>

                     <div x-show="crearUsuario" x-transition class="space-y-4 pl-6 border-l-2 border-petroyellow">
                         <div class="bg-gray-50 border border-gray-200 rounded-md p-3">
                             <div class="flex">
                                 <div class="flex-shrink-0">
                                     <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                         <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                     </svg>
                                 </div>
                                 <div class="ml-3">
                                     <p class="text-sm text-gray-700">
                                         Al marcar esta opción se creará una cuenta de usuario para que el personal tenga acceso al sistema. 
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
                                        value="{{ old('email_usuario') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-petroyellow focus:border-petroyellow"
                                        x-bind:required="crearUsuario">
                             </div>

                             <div>
                                 <label for="rol_usuario" class="block text-sm font-medium text-gray-700 mb-1">Rol en el Sistema *</label>
                                 <select id="rol_usuario" name="rol_usuario" 
                                         x-bind:required="crearUsuario"
                                         class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-petroyellow focus:border-petroyellow">
                                     <option value="">Seleccione un rol</option>
                                     <option value="admin" {{ old('rol_usuario') == 'admin' ? 'selected' : '' }}>Administrador</option>
                                     <option value="encargado" {{ old('rol_usuario') == 'encargado' ? 'selected' : '' }}>Encargado</option>
                                     <option value="operador" {{ old('rol_usuario') == 'operador' ? 'selected' : '' }}>Operador</option>
                                 </select>
                             </div>
                         </div>

                         {{-- Campo oculto para especificar que siempre será contraseña aleatoria --}}
                         <input type="hidden" name="tipo_password" value="aleatoria">

                         <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                             <div class="flex">
                                 <div class="flex-shrink-0">
                                     <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                         <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd" />
                                     </svg>
                                 </div>
                                 <div class="ml-3">
                                     <h3 class="text-sm font-medium text-blue-800">Generación automática de contraseña</h3>
                                     <div class="mt-2 text-sm text-blue-700">
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

                     {{-- Mensaje cuando no está marcado el checkbox --}}
                     <div x-show="!crearUsuario" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                         <div class="flex items-center">
                             <svg class="w-5 h-5 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                             </svg>
                             <div>
                                 <p class="text-sm font-medium text-yellow-800">Este personal no tiene usuario del sistema</p>
                                 <p class="text-xs text-yellow-700 mt-1">Marque la casilla de arriba para crear un usuario y darle acceso al sistema</p>
                             </div>
                         </div>
                     </div>
                 </div>
             @endif
         </div>

         {{-- Información de Auditoría --}}
         <div class="bg-white border border-gray-200 rounded-lg p-6">
             <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                     <path fill-rule="evenodd" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" clip-rule="evenodd" />
                 </svg>
                 Información de Auditoría
             </h3>
             
             <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                 <div>
                     <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Creación</label>
                     <div class="bg-gray-50 border border-gray-200 px-4 py-3 rounded-lg text-sm text-gray-800">
                         {{ $personal->created_at ? $personal->created_at->format('d/m/Y H:i') : 'No disponible' }}
                     </div>
                 </div>
                 <div>
                     <label class="block text-sm font-medium text-gray-700 mb-2">Última Actualización</label>
                     <div class="bg-gray-50 border border-gray-200 px-4 py-3 rounded-lg text-sm text-gray-800">
                         {{ $personal->updated_at ? $personal->updated_at->format('d/m/Y H:i') : 'No disponible' }}
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
                 Actualizar Personal
             </button>
         </div>
     </form>

@endsection

@push('scripts')
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
            
            handleFileInput(event, type) {
                const file = event.target.files[0];
                if (file) {
                    // Validar tamaño del archivo (10MB máximo)
                    const maxSize = 10 * 1024 * 1024; // 10MB en bytes
                    if (file.size > maxSize) {
                        alert('El archivo es demasiado grande. El tamaño máximo permitido es 10MB.');
                        event.target.value = '';
                        this.fileStatus[type] = '';
                        return;
                    }
                    
                    // Validar tipo de archivo
                    const allowedTypes = {
                        'ine': ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'],
                        'curp': ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'],
                        'rfc': ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'],
                        'nss': ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'],
                        'licencia': ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'],
                        'cv': ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
                        'comprobante': ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf']
                    };
                    
                    if (!allowedTypes[type].includes(file.type)) {
                        alert('Tipo de archivo no permitido. Por favor selecciona un archivo válido.');
                        event.target.value = '';
                        this.fileStatus[type] = '';
                        return;
                    }
                    
                    this.fileStatus[type] = `✓ ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
                } else {
                    this.fileStatus[type] = '';
                }
            },
            
            generateEmail() {
                const nombreCompleto = this.$refs.nombreCompleto?.value || '';
                if (nombreCompleto && this.crearUsuario) {
                    const email = nombreCompleto
                        .toLowerCase()
                        .replace(/\s+/g, '.')
                        .replace(/[áéíóúü]/g, char => ({
                            'á': 'a', 'é': 'e', 'í': 'i', 'ó': 'o', 'ú': 'u', 'ü': 'u'
                        }[char]))
                        ;
                    
                    const emailInput = document.getElementById('email_usuario');
                    if (emailInput) {
                        emailInput.value = email;
                    }
                }
            }
        }
    }
</script>
@endpush
