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
    <div class="bg-white rounded-lg shadow p-6" x-data="formController()">
        <form action="{{ route('personal.store') }}" method="POST" enctype="multipart/form-data" @submit.prevent="submitForm">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-6">
                    <!-- Columna Izquierda: Información Personal -->
                <div class="space-y-6">
                    <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                        Información Personal
                    </h3>                    <x-form-input name="nombre_completo" label="Nombre Completo" required />
                    <x-form-input name="email" label="Email de Contacto" type="email" placeholder="correo@ejemplo.com" />
                    <x-form-input name="curp" label="CURP" required pattern="[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9]{2}" maxlength="18" placeholder="Ingrese CURP" />

                    <!-- 1. Identificación INE -->
                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700">
                            Identificación (INE) <span class="text-red-500">*</span>
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
                                       class="cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                    Adjuntar
                                </label>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500" x-text="fileStatus.identificacion || 'Formatos: PDF, JPG, PNG (máx. 5MB)'"></p>
                    </div>

                    <!-- 2. RFC -->
                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700">RFC <span class="text-red-500">*</span></label>
                        <div class="flex items-center space-x-3">
                            <input type="text" 
                                   name="rfc" 
                                   placeholder="Ingrese RFC" 
                                   maxlength="13"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                            <div class="flex-shrink-0">
                                <input type="file" 
                                       id="rfc_file" 
                                       name="rfc_file" 
                                       accept=".pdf,.jpg,.jpeg,.png" 
                                       class="hidden" 
                                       @change="handleFileInput($event, 'rfc')" />
                                <label for="rfc_file" 
                                       class="cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                    Adjuntar
                                </label>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500" x-text="fileStatus.rfc || 'Formatos: PDF, JPG, PNG (máx. 5MB)'"></p>
                    </div>

                    <!-- 3. NSS -->
                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700">NSS <span class="text-red-500">*</span></label>
                        <div class="flex items-center space-x-3">
                            <input type="text" 
                                   name="nss" 
                                   placeholder="Número de Seguro Social" 
                                   maxlength="11"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                            <div class="flex-shrink-0">
                                <input type="file" 
                                       id="nss_file" 
                                       name="nss_file" 
                                       accept=".pdf,.jpg,.jpeg,.png" 
                                       class="hidden" 
                                       @change="handleFileInput($event, 'nss')" />
                                <label for="nss_file" 
                                       class="cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                    Adjuntar
                                </label>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500" x-text="fileStatus.nss || 'Formatos: PDF, JPG, PNG (máx. 5MB)'"></p>
                    </div>

                    <!-- 4. Comprobante de Domicilio -->
                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700">
                            Comprobante de Domicilio <span class="text-red-500">*</span>
                        </label>
                        <textarea name="direccion" 
                                rows="2" 
                                placeholder="Dirección completa" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow">{{ old('direccion') }}</textarea>
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
                        <p class="text-xs text-gray-500" x-text="fileStatus.comprobante || 'Formatos: PDF, JPG, PNG (máx. 5MB)'"></p>
                    </div>

                    <!-- 5. Licencia de Manejo -->
                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700">
                            Licencia de Manejo <span class="text-red-500">*</span>
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
                                       class="cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                    Adjuntar
                                </label>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500" x-text="fileStatus.licencia || 'Formatos: PDF, JPG, PNG (máx. 5MB)'"></p>
                    </div>

                    <!-- 6. CV Profesional -->
                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700">
                            CV Profesional <span class="text-red-500">*</span>
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

                <!-- Columna Derecha: Información Laboral y de Usuario -->
                <div class="space-y-6">
                    <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
                        </svg>
                        Información Laboral y de Usuario
                    </h3>

                    <div>
                        <label for="categoria_personal_id" class="block text-sm font-medium text-gray-700 mb-2">Categoría <span class="text-red-500">*</span></label>
                        <select id="categoria_personal_id" name="categoria_personal_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('categoria_personal_id') border-red-500 @enderror">
                            <option value="">Seleccionar categoría</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}" {{ old('categoria_personal_id') == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nombre_categoria }}
                                </option>
                            @endforeach
                        </select>
                        @error('categoria_personal_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Estos campos se han eliminado -->

                    <div>
                        <label for="estatus" class="block text-sm font-medium text-gray-700 mb-2">Estatus <span class="text-red-500">*</span></label>
                        <select id="estatus" name="estatus" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('estatus') border-red-500 @enderror">
                            <option value="activo" {{ old('estatus', 'activo') == 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="inactivo" {{ old('estatus') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        @error('estatus') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Sección de Usuario -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-md font-semibold text-gray-800">Cuenta de Usuario</h4>
                            <label class="inline-flex items-center cursor-pointer">
                                <span class="mr-3 text-sm font-medium text-gray-900">Crear Usuario</span>
                                <div class="relative">
                                    <input type="checkbox" 
                                           name="crear_usuario" 
                                           value="1" 
                                           x-model="crearUsuario" 
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-petroyellow/50 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-petroyellow"></div>
                                </div>
                            </label>
                        </div>

                        <div x-show="crearUsuario" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform -translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             x-transition:leave="transition ease-in duration-300"
                             x-transition:leave-start="opacity-100 transform translate-y-0"
                             x-transition:leave-end="opacity-0 transform -translate-y-2"
                             class="space-y-4">
                            
                            <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-blue-700">
                                            Se creará una cuenta de usuario para este personal. El nombre de usuario se generará automáticamente y la contraseña inicial será el CURP.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label for="rol_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Rol del Usuario <span class="text-red-500">*</span>
                                </label>
                                <select id="rol_id" 
                                        name="rol_id" 
                                        x-bind:required="crearUsuario"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow disabled:bg-gray-100 disabled:cursor-not-allowed"
                                        x-bind:disabled="!crearUsuario">
                                    <option value="">Seleccionar Rol</option>
                                    @foreach(\App\Models\Role::all() as $role)
                                        <option value="{{ $role->id }}">{{ $role->nombre_rol }}</option>
                                    @endforeach
                                </select>
                                @error('rol_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="mt-8 flex justify-end space-x-4">
                <a href="{{ route('personal.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-petrodark bg-petroyellow hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                    Guardar Personal
                </button>
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
                rfc: '',
                nss: '',
                licencia: '',
                comprobante: '',
                cv: ''
            },
            
            init() {
                this.crearUsuario = {{ old('crear_usuario') ? 'true' : 'false' }};
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
            },
            
            submitForm(event) {
                const form = event.target;
                
                if (this.crearUsuario) {
                    const rolId = form.querySelector('#rol_id').value;
                    if (!rolId) {
                        event.preventDefault();
                        alert('Por favor seleccione un rol para el usuario.');
                        return;
                    }
                }
                
                form.submit();
            }
        }));
    });
</script>
@endpush
