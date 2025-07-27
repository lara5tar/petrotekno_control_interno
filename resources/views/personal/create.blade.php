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
    <div class="bg-white rounded-lg shadow p-6" x-data="formController()">
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
                        <x-form-input name="nombre_completo" label="Nombre Completo" required />
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

                <!-- Sección de Crear Usuario -->
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
                                   value="1"
                                   x-model="crearUsuario"
                                   {{ old('crear_usuario') ? 'checked' : '' }}
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
                    </div>
                </div>
                
                <!-- Sección de Documentos -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h4 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" clip-rule="evenodd" />
                        </svg>
                        Documentos del Personal
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- 1. Identificación INE -->
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700">
                                Identificación (INE)
                            </label>
                            <div class="flex items-center space-x-3">
                                <input type="text" 
                                       name="no_identificacion" 
                                       placeholder="Número de INE" 
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                                <div class="flex-shrink-0">
                                    <input type="file" 
                                           id="identificacion_file" 
                                           name="identificacion_file" 
                                           accept=".pdf,.jpg,.jpeg,.png" 
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
                            <p class="text-xs text-gray-500" x-text="fileStatus.identificacion || 'PDF, JPG, PNG (máx. 5MB)'"></p>
                        </div>

                        <!-- 2. CURP -->
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700">CURP</label>
                            <div class="flex items-center space-x-3">
                                <input type="text" 
                                       name="curp_numero" 
                                       placeholder="CURP de 18 caracteres" 
                                       maxlength="18"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                                <div class="flex-shrink-0">
                                    <input type="file" 
                                           id="curp_file" 
                                           name="curp_file" 
                                           accept=".pdf,.jpg,.jpeg,.png" 
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
                            <p class="text-xs text-gray-500" x-text="fileStatus.curp || 'PDF, JPG, PNG (máx. 5MB)'"></p>
                        </div>

                        <!-- 3. RFC -->
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700">RFC</label>
                            <div class="flex items-center space-x-3">
                                <input type="text" 
                                       name="rfc" 
                                       value="{{ old('rfc') }}"
                                       placeholder="Ingrese RFC" 
                                       maxlength="13"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('rfc') border-red-500 @enderror" />
                                <div class="flex-shrink-0">
                                    <input type="file" 
                                           id="rfc_file" 
                                           name="rfc_file" 
                                           accept=".pdf,.jpg,.jpeg,.png" 
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
                            </div>
                            @error('rfc') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="text-xs text-gray-500" x-text="fileStatus.rfc || 'PDF, JPG, PNG (máx. 5MB)'"></p>
                        </div>

                        <!-- 4. NSS -->
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700">NSS</label>
                            <div class="flex items-center space-x-3">
                                <input type="text" 
                                       name="nss" 
                                       value="{{ old('nss') }}"
                                       placeholder="Número de Seguro Social" 
                                       maxlength="11"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('nss') border-red-500 @enderror" />
                                <div class="flex-shrink-0">
                                    <input type="file" 
                                           id="nss_file" 
                                           name="nss_file" 
                                           accept=".pdf,.jpg,.jpeg,.png" 
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
                            <p class="text-xs text-gray-500" x-text="fileStatus.nss || 'PDF, JPG, PNG (máx. 5MB)'"></p>
                        </div>

                        <!-- 5. Licencia de Manejo -->
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700">
                                Licencia de Manejo
                            </label>
                            <div class="flex items-center space-x-3">
                                <input type="text" 
                                       name="no_licencia" 
                                       placeholder="Número de Licencia" 
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                                <div class="flex-shrink-0">
                                    <input type="file" 
                                           id="licencia_file" 
                                           name="licencia_file" 
                                           accept=".pdf,.jpg,.jpeg,.png" 
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
                            </div>
                            <p class="text-xs text-gray-500" x-text="fileStatus.licencia || 'PDF, JPG, PNG (máx. 5MB)'"></p>
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
                                   name="comprobante_file" 
                                   accept=".pdf,.jpg,.jpeg,.png" 
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
                        <p class="text-xs text-gray-500" x-text="fileStatus.comprobante || 'PDF, JPG, PNG (máx. 5MB)'"></p>
                    </div>

                    <!-- CV Profesional -->
                    <div class="mt-6 space-y-3">
                        <label class="block text-sm font-medium text-gray-700">
                            CV Profesional
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-md p-6 text-center hover:border-petroyellow transition-colors">
                            <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <input type="file" 
                                   id="cv_file" 
                                   name="cv_file" 
                                   accept=".pdf,.doc,.docx" 
                                   class="hidden" 
                                   @change="handleFileInput($event, 'cv')" />
                            <label for="cv_file" 
                                   class="cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                                Seleccionar CV
                            </label>
                            <p class="mt-2 text-xs text-gray-500" x-show="!fileStatus.cv">
                                PDF, DOC, DOCX (máx. 10MB)
                            </p>
                            <p class="mt-2 text-sm text-petroyellow font-medium" x-show="fileStatus.cv" x-text="fileStatus.cv">
                            </p>
                        </div>
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

@push('scripts')
<script src="//unpkg.com/alpinejs" defer></script>
<script>
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
            
            init() {
                // Inicializar el estado del checkbox basado en old values
                this.crearUsuario = document.getElementById('crear_usuario').checked;
            },
            
            handleFileInput(event, type) {
                const file = event.target.files[0];
                if (!file) {
                    this.fileStatus[type] = '';
                    return;
                }

                // Validar tamaño (10MB para CV, 5MB para otros)
                const maxSize = type === 'cv' ? 10 * 1024 * 1024 : 5 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert(`El archivo es demasiado grande. Máximo ${maxSize / 1024 / 1024}MB`);
                    event.target.value = '';
                    this.fileStatus[type] = '';
                    return;
                }

                // Validar tipo de archivo
                const allowedTypes = type === 'cv' 
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

