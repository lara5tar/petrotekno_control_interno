@extends('layouts.app')

@section('title', 'Detalles del Personal')

@section('header', 'Gestión de Personal')

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
                        <span class="text-gray-500 ml-1 md:ml-2">{{ $personal->nombre_completo }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Encabezado Principal -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <div class="h-16 w-16 rounded-full bg-petroyellow flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-petrodark" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $personal->nombre_completo }}</h1>
                        <div class="flex items-center space-x-4 mt-1">
                            <span class="text-sm text-gray-600">{{ $personal->categoria->nombre_categoria ?? 'Sin categoría' }}</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $personal->estatus === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($personal->estatus) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('personal.edit', $personal) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md text-sm transition duration-200 flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Editar</span>
                    </a>
                    <a href="{{ route('personal.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md text-sm transition duration-200">
                        Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Panel Izquierdo - Información Principal -->
        <div class="xl:col-span-2 space-y-6">
            <!-- Datos Principales -->
            <x-personal-info-card :personal="$personal" />

            <!-- Información de Contacto -->
            <x-personal-contact-card :personal="$personal" />

            <!-- Documentos -->
            @if($personal->documentos->count() > 0)
                <x-personal-documents-card :personal="$personal" />
            @endif
        </div>

        <!-- Panel Derecho -->
        <div class="space-y-6">
            <!-- Información Adicional -->
            <x-personal-additional-info :personal="$personal" />
            @if(!$personal->user)
                <div id="create-user-section" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Crear Usuario para {{ $personal->nombre_completo }}</h3>
                    <form id="createUserForm" class="space-y-4">
                        <div>
                            <label for="nombre_usuario" class="block text-sm font-medium text-gray-700">Usuario <span class="text-red-500">*</span></label>
                            <input type="text" id="nombre_usuario" name="nombre_usuario" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-petroyellow" />
                        </div>
                        <div>
                            <label for="email_user" class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                            <input type="email" id="email_user" name="email" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-petroyellow" />
                        </div>
                        <div>
                            <label for="password_user" class="block text-sm font-medium text-gray-700">Contraseña <span class="text-red-500">*</span></label>
                            <input type="password" id="password_user" name="password" minlength="8" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-petroyellow" />
                        </div>
                        <div>
                            <label for="rol_id" class="block text-sm font-medium text-gray-700">Rol <span class="text-red-500">*</span></label>
                            <select id="rol_id" name="rol_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-petroyellow">
                                <option value="">Cargando roles...</option>
                            </select>
                        </div>
                        <input type="hidden" name="personal_id" value="{{ $personal->id }}" />
                        <div class="flex justify-end">
                            <button type="submit"
                                class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-6 rounded-md">Crear Usuario</button>
                        </div>
                        <p id="createUserMessage" class="mt-2 text-sm"></p>
                    </form>
                </div>
            @endif

            <!-- Usuario Asociado -->
            @if($personal->user)
                <x-personal-user-card :user="$personal->user" />
            @endif

            <!-- Estadísticas Rápidas -->
            <x-personal-stats-card :personal="$personal" />
        </div>
    </div>

    <!-- Modal de Confirmación para Eliminación -->
    <x-confirm-modal 
        id="deleteModal" 
        title="Eliminar Personal" 
        message="¿Está seguro de que desea eliminar este personal? Esta acción no se puede deshacer."
        action="{{ route('personal.destroy', $personal) }}"
        method="DELETE"
    />
@endsection

@push('scripts')
<script>
// Funcionalidad para mostrar/ocultar información
function toggleSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        section.classList.toggle('hidden');
    }
}

// Confirmación para eliminar
function confirmDelete() {
    document.getElementById('deleteModal').classList.remove('hidden');
}

// Cerrar modal
function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Copiar texto al clipboard
function copyToClipboard(text, element) {
    navigator.clipboard.writeText(text).then(function() {
        // Mostrar feedback visual
        const originalText = element.textContent;
        element.textContent = '¡Copiado!';
        element.classList.add('text-green-600');
        
        setTimeout(() => {
            element.textContent = originalText;
            element.classList.remove('text-green-600');
        }, 2000);
    });
}

// Funcionalidad para tabs
function showTab(tabName) {
    // Ocultar todos los tabs
    document.querySelectorAll('[data-tab-content]').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Remover clase activa de todos los botones
    document.querySelectorAll('[data-tab-button]').forEach(button => {
        button.classList.remove('border-petroyellow', 'text-petroyellow');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Mostrar tab seleccionado
    const targetTab = document.querySelector(`[data-tab-content="${tabName}"]`);
    if (targetTab) {
        targetTab.classList.remove('hidden');
    }
    
    // Activar botón seleccionado
    const targetButton = document.querySelector(`[data-tab-button="${tabName}"]`);
    if (targetButton) {
        targetButton.classList.add('border-petroyellow', 'text-petroyellow');
        targetButton.classList.remove('border-transparent', 'text-gray-500');
    }
}

// Inicializar primer tab como activo
document.addEventListener('DOMContentLoaded', function() {
    showTab('info');
});

// AJAX para crear usuario
document.getElementById('createUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const messageElement = document.getElementById('createUserMessage');

    fetch('{{ route('user.store') }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageElement.textContent = 'Usuario creado exitosamente.';
            messageElement.classList.add('text-green-600');
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            messageElement.textContent = 'Error al crear usuario.';
            messageElement.classList.add('text-red-600');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        messageElement.textContent = 'Error al crear usuario.';
        messageElement.classList.add('text-red-600');
    });
});

// Cargar roles al seleccionar el campo de rol
document.getElementById('rol_id').addEventListener('focus', function() {
    fetchRoles();
});

function fetchRoles() {
    fetch('{{ route('roles.index') }}')
    .then(response => response.json())
    .then(data => {
        const rolSelect = document.getElementById('rol_id');
        rolSelect.innerHTML = '';
        data.forEach(role => {
            const option = document.createElement('option');
            option.value = role.id;
            option.textContent = role.name;
            rolSelect.appendChild(option);
        });
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endpush