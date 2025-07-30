@extends('layouts.app')

@section('title', 'Editar Personal')

@section('header', 'Editar Personal')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Personal', 'url' => route('personal.index')],
        ['label' => $personal->nombre_completo, 'url' => route('personal.show', $personal->id)],
        ['label' => 'Editar']
    ]" />

    <!-- Encabezado -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Editar Personal</h2>
            <p class="text-gray-600">{{ $personal->nombre_completo }} - ID: #{{ $personal->id }}</p>
        </div>
        @hasPermission('ver_personal')
        <a href="{{ route('personal.show', $personal->id) }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Volver al detalle
        </a>
        @endhasPermission
    </div>

    <!-- Formulario -->
    <div class="bg-white rounded-lg shadow p-6" x-data="formController()" x-init="$nextTick(() => { crearUsuario = false; passwordType = 'random'; })" x-cloak>
         <form action="{{ route('personal.update', $personal->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
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
                        <x-form-input 
                            name="nombre_completo" 
                            label="Nombre Completo" 
                            required="true"
                            value="{{ old('nombre_completo', $personal->nombre_completo) }}" />
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
                                    <option value="{{ $categoria->id }}" {{ old('categoria_personal_id', $personal->categoria_id) == $categoria->id ? 'selected' : '' }}>

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
                                <option value="activo" {{ old('estatus', $personal->estatus) == 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ old('estatus', $personal->estatus) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('estatus') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Sección de Gestión de Usuario -->
                @if($personal->usuario)
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-3 0a5 5 0 11-10 0 5 5 0 0110 0z" clip-rule="evenodd" />
                            </svg>
                            Usuario del Sistema
                        </h3>

                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                            <div class="flex">
                                <svg class="h-5 w-5 text-green-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <div class="text-sm text-green-800">
                                    <p class="font-medium">Usuario Existente:</p>
                                    <p>Este personal ya tiene un usuario del sistema asociado: <strong>{{ $personal->usuario->email }}</strong></p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <x-form-input 
                                name="email_usuario" 
                                label="Email del Usuario" 
                                type="email" 
                                value="{{ old('email_usuario', $personal->usuario->email) }}" />
                            <p class="mt-1 text-xs text-gray-500">
                                Puedes actualizar el email del usuario. Los cambios se reflejarán en su cuenta del sistema.
                            </p>
                        </div>
                    </div>
                @else
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-3 0a5 5 0 11-10 0 5 5 0 0110 0z" clip-rule="evenodd" />
                            </svg>
                            Crear Usuario del Sistema
                        </h3>

                        <!-- Toggle para crear usuario -->
                        <div class="mb-6">
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       id="crear_usuario" 
                                       name="crear_usuario" 
                                       x-model="crearUsuario"
                                       class="h-4 w-4 text-petroyellow focus:ring-petroyellow border-gray-300 rounded" />
                                <label for="crear_usuario" class="ml-3 text-sm font-medium text-gray-700">
                                    Crear usuario del sistema para este personal
                                </label>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Al activar esta opción, se creará automáticamente un usuario para que el personal pueda acceder al sistema.
                            </p>
                        </div>

                        <!-- Campo de email (solo visible si se activa el toggle) -->
                        <div x-show="crearUsuario" x-transition class="space-y-4">
                            <div>
                                <x-form-input 
                                    name="email_usuario" 
                                    label="Email del Usuario" 
                                    type="email" 
                                    placeholder="correo@petrotekno.com"
                                    x-bind:required="crearUsuario" />
                                <p class="mt-1 text-xs text-gray-500">
                                    Este será el email para acceder al sistema y donde se enviará la contraseña temporal.
                                </p>
                            </div>
                            
                            <!-- Campo de contraseña -->
                            <div class="form-group">
                                <label for="password_usuario" class="block text-sm font-medium text-gray-700 mb-2">
                                    Contraseña
                                </label>
                                <div class="space-y-3">
                                    <!-- Opción de contraseña personalizada -->
                                    <div class="flex items-center">
                                        <input type="radio" 
                                               id="password_custom" 
                                               name="password_type" 
                                               value="custom"
                                               x-model="$data.passwordType"
                                               class="h-4 w-4 text-petroyellow focus:ring-petroyellow border-gray-300" />
                                        <label for="password_custom" class="ml-3 text-sm font-medium text-gray-700">
                                            Establecer contraseña personalizada
                                        </label>
                                    </div>
                                    
                                    <!-- Campo de contraseña personalizada -->
                                    <div x-show="$data.passwordType === 'custom'" x-transition class="ml-7">
                                        <input type="password" 
                                               name="password_usuario" 
                                               id="password_usuario"
                                               value="{{ old('password_usuario') }}"
                                               placeholder="Ingrese la contraseña"
                                               x-bind:required="$data.crearUsuario && $data.passwordType === 'custom'"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('password_usuario') border-red-500 @enderror" />
                                        @error('password_usuario') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                        <p class="mt-1 text-xs text-gray-500">
                                            Mínimo 8 caracteres, debe incluir mayúsculas, minúsculas y números.
                                        </p>
                                    </div>

                                    <!-- Opción de contraseña aleatoria -->
                                    <div class="flex items-center">
                                        <input type="radio" 
                                               id="password_random" 
                                               name="password_type" 
                                               value="random"
                                               x-model="$data.passwordType"
                                               checked
                                               class="h-4 w-4 text-petroyellow focus:ring-petroyellow border-gray-300" />
                                        <label for="password_random" class="ml-3 text-sm font-medium text-gray-700">
                                            Generar contraseña aleatoria (Recomendado)
                                        </label>
                                    </div>
                                    
                                    <!-- Información sobre contraseña aleatoria -->
                                    <div x-show="$data.passwordType === 'random'" x-transition class="ml-7">
                                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                            <div class="flex">
                                                <svg class="h-5 w-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                                </svg>
                                                <div class="text-sm text-blue-800">
                                                    <p class="font-medium">Contraseña Segura Automática</p>
                                                    <p>Se generará una contraseña segura y se enviará al correo electrónico del usuario.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Sección de Documentos -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h4 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" clip-rule="evenodd" />
                        </svg>
                        Documentos del Personal
                    </h4>

                    <!-- Documentos del Personal -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- 1. Identificación INE -->
                        <div class="space-y-3">
                            @php
                                $identificacionDoc = $personal->documentos->where('tipo_documento_id', 8)->first();
                            @endphp
                            <label class="block text-sm font-medium text-gray-700">
                                Identificación (INE)
                                @if($identificacionDoc)
                                    <span class="text-xs text-green-600 ml-2">(Documento cargado)</span>
                                @endif
                            </label>
                            
                            <!-- Mostrar documento existente si existe -->
                            @if(isset($documentosPorTipo['identificacion']))
                                @php $doc = $documentosPorTipo['identificacion']; @endphp
                                <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-md">
                                    <p class="text-sm text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        {{ $doc->tipoDocumento->descripcion }}
                                    </p>
                                    @if($doc->numero_documento)
                                        <p class="text-xs text-green-600 mt-1">
                                            Número: {{ $doc->numero_documento }}
                                        </p>
                                    @endif
                                    @if($doc->fecha_vencimiento)
                                        <p class="text-xs text-green-600 mt-1">
                                            Vence: {{ \Carbon\Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y') }}
                                        </p>
                                    @endif
                                    <div class="mt-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-paperclip mr-1"></i>
                                            Archivo adjunto
                                        </span>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="flex items-center space-x-3">
                                <input type="text" 
                                       name="no_identificacion" 
                                       value="{{ old('no_identificacion', $identificacionDoc ? $identificacionDoc->descripcion : $personal->no_identificacion) }}"
                                       placeholder="Número de INE" 
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('no_identificacion') border-red-500 @enderror" />
                                <div class="flex-shrink-0 flex space-x-2">
                                    @if($identificacionDoc)
                                        <button type="button"
                                                onclick="viewDocument({{ $identificacionDoc->id }})"
                                                class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Ver
                                        </button>
                                    @endif
                                    <input type="file" 
                                           id="identificacion_file" 
                                           name="identificacion_file" 
                                           class="hidden" 
                                           @change="handleFileInput($event, 'identificacion')" />
                                    <label for="identificacion_file" 
                                           class="cursor-pointer inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        Adjuntar
                                    </label>
                                </div>
                            </div>
                            @error('no_identificacion') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            @error('identificacion_file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="text-xs text-gray-500" x-text="fileStatus.identificacion || ''">
                            </p>
                            @if($doc = $personal->documentos->where('tipo_documento_id', 8)->first())
                                <div class="text-xs text-gray-600">
                                    Último documento: {{ $doc->created_at->format('d/m/Y H:i') }}
                                </div>
                            @endif
                        </div>

                        <!-- 2. CURP -->
                        <div class="space-y-3">
                            @php
                                $curpDoc = $personal->documentos->where('tipo_documento_id', 9)->first();
                            @endphp
                            <label class="block text-sm font-medium text-gray-700">
                                CURP <span class="text-xs text-gray-500 ml-1">(Opcional)</span>
                                @if($curpDoc)
                                    <span class="text-xs text-green-600 ml-2">(Documento cargado)</span>
                                @endif
                            </label>
                            
                            <!-- Mostrar documento existente si existe -->
                            @if(isset($documentosPorTipo['curp']))
                                @php $doc = $documentosPorTipo['curp']; @endphp
                                <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-md">
                                    <p class="text-sm text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        {{ $doc->tipoDocumento->descripcion }}
                                    </p>
                                    @if($doc->numero_documento)
                                        <p class="text-xs text-green-600 mt-1">
                                            Número: {{ $doc->numero_documento }}
                                        </p>
                                    @endif
                                    <div class="mt-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-paperclip mr-1"></i>
                                            Archivo adjunto
                                        </span>
                                    </div>
                                </div>
                            @endif
                            <div class="flex items-center space-x-3">
                                <input type="text" 
                                       name="curp_numero" 
                                       value="{{ old('curp_numero', $curpDoc ? $curpDoc->descripcion : $personal->curp_numero) }}"
                                       placeholder="Ingrese CURP" 
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('curp_numero') border-red-500 @enderror" />
                                <div class="flex-shrink-0 flex space-x-2">
                                    @if($curpDoc)
                                        <button type="button"
                                                onclick="viewDocument({{ $curpDoc->id }})"
                                                class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Ver
                                        </button>
                                    @endif
                                    <input type="file" 
                                   id="curp_file" 
                                   name="curp_file" 
                                   class="hidden" 
                                   @change="handleFileInput($event, 'curp')" />
                                    <label for="curp_file" 
                                           class="cursor-pointer inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        Adjuntar
                                    </label>
                                </div>
                            </div>
                            @error('curp_numero') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            @error('curp_file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="text-xs text-gray-500" x-text="fileStatus.curp || ''">
                            </p>
                            @if($doc = $personal->documentos->where('tipo_documento_id', 9)->first())
                                <div class="text-xs text-gray-600">
                                    Último documento: {{ $doc->created_at->format('d/m/Y H:i') }}
                                </div>
                            @endif
                        </div>
                        <!-- RFC -->
                        <div class="space-y-3">
                            @php
                                $rfcDoc = $personal->documentos->where('tipo_documento_id', 10)->first();
                            @endphp
                            <label class="block text-sm font-medium text-gray-700">
                                RFC
                                @if($rfcDoc)
                                    <span class="text-xs text-green-600 ml-2">(Documento cargado)</span>
                                @endif
                            </label>
                            
                            <!-- Mostrar documento existente si existe -->
                            @if($rfcDoc)
                                <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-md">
                                    <p class="text-sm text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Documento registrado: {{ $rfcDoc->descripcion ?? 'Sin número' }}
                                    </p>
                                    @if($rfcDoc->fecha_vencimiento)
                                        <p class="text-xs text-green-600 mt-1">
                                            Vence: {{ \Carbon\Carbon::parse($rfcDoc->fecha_vencimiento)->format('d/m/Y') }}
                                        </p>
                                    @endif
                                </div>
                            @endif
                            
                            <div class="flex items-center space-x-3">
                                <input type="text" 
                                       name="rfc" 
                                       value="{{ old('rfc', $rfcDoc ? $rfcDoc->descripcion : $personal->rfc) }}"
                                       placeholder="Ingrese RFC" 
                                       
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('rfc') border-red-500 @enderror" />
                            <div class="flex-shrink-0 flex space-x-2">
                                @if($rfcDoc)
                                    <button type="button"
                                            onclick="viewDocument({{ $rfcDoc->id }})"
                                            class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Ver
                                    </button>
                                @endif
                                <input type="file" 
                                       id="rfc_file" 
                                       name="rfc_file" 
                                       class="hidden" 
                                       @change="handleFileInput($event, 'rfc')" />
                                <label for="rfc_file" 
                                       class="cursor-pointer inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                    Adjuntar
                                </label>
                            </div>
                            @error('rfc') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            @error('rfc_file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="text-xs text-gray-500" x-text="fileStatus.rfc || ''">
                            </p>
                            @if($doc = $personal->documentos->where('tipo_documento_id', 10)->first())
                                <div class="text-xs text-gray-600">
                                    Último documento: {{ $doc->created_at->format('d/m/Y H:i') }}
                                </div>
                            @endif
                        </div>

                        <!-- 4. NSS -->
                        <div class="space-y-3">
                            @php
                                $nssDoc = $personal->documentos->where('tipo_documento_id', 28)->first();
                            @endphp
                            <label class="block text-sm font-medium text-gray-700">
                                NSS
                                @if($nssDoc)
                                    <span class="text-xs text-green-600 ml-2">(Documento cargado)</span>
                                @endif
                            </label>
                            <div class="flex items-center space-x-3">
                                <input type="text" 
                                       name="nss" 
                                       value="{{ old('nss', $nssDoc ? $nssDoc->descripcion : $personal->nss) }}"
                                       placeholder="Número de Seguro Social" 
                                       
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('nss') border-red-500 @enderror" />
                                <div class="flex-shrink-0 flex space-x-2">
                                    @if($nssDoc)
                                        <button type="button"
                                                onclick="viewDocument({{ $nssDoc->id }})"
                                                class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Ver
                                        </button>
                                    @endif
                                    <input type="file" 
                                           id="nss_file" 
                                           name="nss_file" 
                                           class="hidden" 
                                           @change="handleFileInput($event, 'nss')" />
                                    <label for="nss_file" 
                                           class="cursor-pointer inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        Adjuntar
                                    </label>
                                </div>
                            </div>
                            @error('nss') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            @error('nss_file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="text-xs text-gray-500" x-text="fileStatus.nss || ''">
                            </p>
                            @if($doc = $personal->documentos->where('tipo_documento_id', 28)->first())
                                <div class="text-xs text-gray-600">
                                    Último documento: {{ $doc->created_at->format('d/m/Y H:i') }}
                                </div>
                            @endif
                        </div>

                        <!-- 5. Licencia de Manejo -->
                        <div class="space-y-3">
                            @php
                                $licenciaDoc = $personal->documentos->where('tipo_documento_id', 7)->first();
                            @endphp
                            <label class="block text-sm font-medium text-gray-700">
                                Licencia de Manejo <span class="text-xs text-gray-500 ml-1">(Opcional)</span>
                                @if($licenciaDoc)
                                    <span class="text-xs text-green-600 ml-2">(Documento cargado)</span>
                                @endif
                            </label>
                            
                            <!-- Mostrar documento existente si existe -->
                            @if($licenciaDoc)
                                <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-md">
                                    <p class="text-sm text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Documento registrado: {{ $licenciaDoc->descripcion ?? 'Sin número' }}
                                    </p>
                                    @if($licenciaDoc->fecha_vencimiento)
                                        <p class="text-xs text-green-600 mt-1">
                                            Vence: {{ \Carbon\Carbon::parse($licenciaDoc->fecha_vencimiento)->format('d/m/Y') }}
                                        </p>
                                    @endif
                                </div>
                            @endif
                            
                        <div class="flex items-center space-x-3">
                            <input type="text" 
                                   name="no_licencia" 
                                   value="{{ old('no_licencia', $licenciaDoc ? $licenciaDoc->descripcion : $personal->no_licencia) }}"
                                   placeholder="Número de Licencia" 
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('no_licencia') border-red-500 @enderror" />
                            <div class="flex-shrink-0 flex space-x-2">
                                @if($licenciaDoc)
                                    <button type="button"
                                            onclick="viewDocument({{ $licenciaDoc->id }})"
                                            class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Ver
                                    </button>
                                @endif
                                <input type="file" 
                                       id="licencia_file" 
                                       name="licencia_file" 
                                       class="hidden" 
                                       @change="handleFileInput($event, 'licencia')" />
                                <label for="licencia_file" 
                                       class="cursor-pointer inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                    Adjuntar
                                </label>
                            </div>
                            @error('no_licencia') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            @error('licencia_file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="text-xs text-gray-500" x-text="fileStatus.licencia || ''">
                            </p>
                            @if($licenciaDoc)
                                <div class="text-xs text-gray-600">
                                    Último documento: {{ $licenciaDoc->created_at->format('d/m/Y H:i') }}
                                </div>
                            @endif
                        </div>
                        </div>
                    </div>

                    <!-- Comprobante de Domicilio -->
                    <div class="mt-6 space-y-3">
                        <label class="block text-sm font-medium text-gray-700">
                            Comprobante de Domicilio
                            @if($personal->documentos->where('tipo_documento_id', 11)->first())
                                <span class="text-xs text-green-600 ml-2">(Documento cargado)</span>
                            @endif
                        </label>
                        <textarea name="direccion" 
                                rows="2" 
                                placeholder="Dirección completa" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('direccion') border-red-500 @enderror">{{ old('direccion', $personal->direccion) }}</textarea>
                        @error('direccion') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        @error('comprobante_file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        <div class="flex items-center">
                            <input type="file" 
                                   id="comprobante_file" 
                                   name="comprobante_file" 
                                   class="hidden" 
                                   @change="handleFileInput($event, 'comprobante')" />
                            <label for="comprobante_file" 
                                   class="cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                                Adjuntar Comprobante
                            </label>
                        </div>
                        <p class="text-xs text-gray-500" x-text="fileStatus.comprobante || ''">
                        </p>
                        @if($doc = $personal->documentos->where('tipo_documento_id', 11)->first())
                            <div class="text-xs text-gray-600">
                                Último documento: {{ $doc->created_at->format('d/m/Y H:i') }}
                            </div>
                        @endif
                    </div>

                    <!-- CV Profesional -->
                    <div class="mt-6 space-y-3">
                        <label class="block text-sm font-medium text-gray-700">
                            CV Profesional
                            @if($personal->documentos->where('tipo_documento_id', 31)->first())
                                <span class="text-xs text-green-600 ml-2">(Documento cargado)</span>
                            @endif
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-md p-6 text-center hover:border-petroyellow transition-colors">
                            <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <input type="file" 
                                   id="cv_file" 
                                   name="cv_file" 
                                   class="hidden" 
                                   @change="handleFileInput($event, 'cv')" />
                            <label for="cv_file" 
                                   class="cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                                Seleccionar CV
                            </label>
                            <p class="mt-2 text-xs text-gray-500" x-show="!fileStatus.cv"></p>
                            <p class="mt-2 text-sm text-petroyellow font-medium" x-show="fileStatus.cv" x-text="fileStatus.cv">
                            </p>
                            @error('cv_file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            @if($doc = $personal->documentos->where('tipo_documento_id', 31)->first())
                                <div class="text-xs text-gray-600 mt-2">
                                    Último documento: {{ $doc->created_at->format('d/m/Y H:i') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Información de Registro -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h4 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                        </svg>
                        Información de Registro
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">
                                Fecha de Registro
                            </label>
                            <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-sm text-gray-900">
                                {{ $personal->created_at->format('d M Y, H:i') }}
                            </div>
                            <p class="text-xs text-gray-500">Fecha en que se creó este registro</p>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">
                                Última Actualización
                            </label>
                            <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-sm text-gray-900">
                                {{ $personal->updated_at->format('d M Y, H:i') }}
                            </div>
                            <p class="text-xs text-gray-500">Fecha de la última modificación</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="mt-8 flex justify-center space-x-4">
                @hasPermission('ver_personal')
                <a href="{{ route('personal.show', $personal->id) }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                    Cancelar
                </a>
                @endhasPermission
                @hasPermission('editar_personal')
                <button type="submit" 
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-petrodark bg-petroyellow hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                    Actualizar Personal
                </button>
                @endhasPermission
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script src="//unpkg.com/alpinejs" defer></script>
<script>
    // Funciones para manejar documentos
    function viewDocument(documentId) {
        window.open('{{ route("documentos.file", ":id") }}'.replace(':id', documentId), '_blank');
    }

    function downloadDocument(documentId) {
        window.location.href = `/documentos/${documentId}/descargar`;
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('formController', () => ({
            crearUsuario: false,
            fileStatus: {
                identificacion: '',
                curp: '',
                rfc: '',
                nss: '',
                licencia: '',
                comprobante: '',
                cv: ''
            },

            
            handleFileInput(event, type) {
                const file = event.target.files[0];
                if (!file) {
                    this.fileStatus[type] = '';
                    return;
                }
                this.fileStatus[type] = `Archivo seleccionado: ${file.name}`;
            
            }
        }));
    });
</script>
@endpush
