@extends('layouts.app')

@section('title', 'Editar Personal')

@section('header', 'Editar Personal')

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
                        <a href="{{ route('personal.show', $personal->id) }}" class="text-gray-700 hover:text-petroyellow ml-1 md:ml-2">{{ $personal->nombre_completo }}</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-500 ml-1 md:ml-2">Editar</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Encabezado -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Editar Personal</h2>
            <p class="text-gray-600">{{ $personal->nombre_completo }} - ID: #{{ $personal->id }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('personal.show', $personal->id) }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Cancelar
            </a>
        </div>
    </div>

    <!-- Alertas de sesión -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Formulario -->
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('personal.update', $personal->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
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
                               value="{{ old('nombre_completo', $personal->nombre_completo) }}"
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
                        <input type="text" 
                               id="curp" 
                               name="curp" 
                               value="{{ old('curp', $personal->curp) }}"
                               pattern="[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9]{2}"
                               maxlength="18"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow {{ $errors->has('curp') ? 'border-red-500' : '' }}">
                        @error('curp')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Formato: ABCD123456HMNPRS99</p>
                    </div>

                    <!-- RFC -->
                    <div>
                        <label for="rfc" class="block text-sm font-medium text-gray-700 mb-2">
                            RFC
                        </label>
                        <input type="text" 
                               id="rfc" 
                               name="rfc" 
                               value="{{ old('rfc', $personal->rfc) }}"
                               maxlength="13"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow {{ $errors->has('rfc') ? 'border-red-500' : '' }}">
                        @error('rfc')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- NSS -->
                    <div>
                        <label for="nss" class="block text-sm font-medium text-gray-700 mb-2">
                            NSS (Número de Seguridad Social)
                        </label>
                        <input type="text" 
                               id="nss" 
                               name="nss" 
                               value="{{ old('nss', $personal->nss) }}"
                               maxlength="11"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow {{ $errors->has('nss') ? 'border-red-500' : '' }}">
                        @error('nss')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Dirección -->
                    <div>
                        <label for="direccion" class="block text-sm font-medium text-gray-700 mb-2">
                            Dirección
                        </label>
                        <textarea id="direccion" 
                                  name="direccion" 
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow {{ $errors->has('direccion') ? 'border-red-500' : '' }}">{{ old('direccion', $personal->direccion) }}</textarea>
                        @error('direccion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
                                <option value="{{ $categoria->id }}" {{ old('categoria_personal_id', $personal->categoria_personal_id) == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nombre_categoria }}
                                </option>
                            @endforeach
                        </select>
                        @error('categoria_personal_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Puesto -->
                    <div>
                        <label for="puesto" class="block text-sm font-medium text-gray-700 mb-2">
                            Puesto
                        </label>
                        <input type="text" 
                               id="puesto" 
                               name="puesto" 
                               value="{{ old('puesto', $personal->puesto) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow {{ $errors->has('puesto') ? 'border-red-500' : '' }}">
                        @error('puesto')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">
                            Teléfono
                        </label>
                        <input type="tel" 
                               id="telefono" 
                               name="telefono" 
                               value="{{ old('telefono', $personal->telefono) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow {{ $errors->has('telefono') ? 'border-red-500' : '' }}">
                        @error('telefono')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $personal->email) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow {{ $errors->has('email') ? 'border-red-500' : '' }}">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fecha de Ingreso -->
                    <div>
                        <label for="fecha_ingreso" class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha de Ingreso
                        </label>
                        <input type="date" 
                               id="fecha_ingreso" 
                               name="fecha_ingreso" 
                               value="{{ old('fecha_ingreso', $personal->fecha_ingreso ? $personal->fecha_ingreso->format('Y-m-d') : '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow {{ $errors->has('fecha_ingreso') ? 'border-red-500' : '' }}">
                        @error('fecha_ingreso')
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
                            <option value="activo" {{ old('estatus', $personal->estatus) == 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="inactivo" {{ old('estatus', $personal->estatus) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        @error('estatus')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Usuario Asignado -->
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Usuario Asignado (Opcional)
                        </label>
                        <select id="user_id" 
                                name="user_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow {{ $errors->has('user_id') ? 'border-red-500' : '' }}">
                            <option value="">Sin usuario asignado</option>
                            @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->id }}" {{ old('user_id', $personal->user_id) == $usuario->id ? 'selected' : '' }}>
                                    {{ $usuario->nombre_usuario }} ({{ $usuario->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Notas adicionales -->
            <div class="mt-6">
                <label for="notas" class="block text-sm font-medium text-gray-700 mb-2">
                    Notas Adicionales
                </label>
                <textarea id="notas" 
                          name="notas" 
                          rows="4"
                          placeholder="Información adicional sobre el personal..."
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow {{ $errors->has('notas') ? 'border-red-500' : '' }}">{{ old('notas', $personal->notas) }}</textarea>
                @error('notas')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Información de modificación -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h4 class="text-sm font-medium text-gray-900 mb-2">Información de Registro</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                    <div>
                        <span class="font-medium">Fecha de registro:</span>
                        {{ $personal->created_at->format('d M Y, H:i') }}
                    </div>
                    <div>
                        <span class="font-medium">Última actualización:</span>
                        {{ $personal->updated_at->format('d M Y, H:i') }}
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="mt-8 flex justify-end space-x-4">
                <a href="{{ route('personal.show', $personal->id) }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-6 rounded-md transition duration-200">
                    Cancelar
                </a>
                <button type="submit" 
                        class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-6 rounded-md transition duration-200">
                    Actualizar Personal
                </button>
            </div>
        </form>
    </div>
@endsection

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
