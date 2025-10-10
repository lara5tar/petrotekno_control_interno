@extends('layouts.app')

@section('title', 'Mantenimientos')

@section('header', 'Gestión de Mantenimientos')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Mantenimientos']
    ]" />

    <!-- Encabezado con botones de agregar -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Listado de Mantenimientos</h2>
        <div>
            <a href="{{ route('mantenimientos.create') }}" 
               class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded flex items-center transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Agregar Mantenimiento
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

    <!-- Filtros y búsqueda -->
    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
        <form method="GET" action="{{ route('mantenimientos.index') }}" id="filtrosForm">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" 
                               id="search" 
                               name="buscar" 
                               value="{{ request('buscar') }}"
                               placeholder="Buscar por proveedor o descripción" 
                               class="pl-10 p-2 border border-gray-300 rounded-md w-full">
                    </div>
                </div>
                <div class="flex-1 md:flex-none md:w-48">
                    <label for="activo" class="block text-sm font-medium text-gray-700 mb-1">Activo</label>
                    <select id="activo" 
                            name="vehiculo_id"
                            class="p-2 border border-gray-300 rounded-md w-full">
                        <option value="">Todos los activos</option>
                        @foreach($vehiculosOptions as $activo)
                            <option value="{{ $activo->id }}" {{ request('vehiculo_id') == $activo->id ? 'selected' : '' }}>
                                {{ $activo->marca }} {{ $activo->modelo }} ({{ $activo->placas }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 md:flex-none md:w-48">
                    <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Servicio</label>
                    <select id="tipo" 
                            name="tipo_servicio"
                            class="p-2 border border-gray-300 rounded-md w-full">
                        <option value="">Todos los tipos</option>
                        @foreach($tiposServicioOptions as $tipo)
                            <option value="{{ $tipo->id }}" {{ request('tipo_servicio') == $tipo->id ? 'selected' : '' }}>
                                {{ $tipo->nombre_tipo_servicio }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 md:flex-none md:w-48">
                    <label for="fecha" class="block text-sm font-medium text-gray-700 mb-1">Fecha desde</label>
                    <input type="date" 
                           id="fecha"
                           name="fecha_desde" 
                           value="{{ request('fecha_desde') }}"
                           class="p-2 border border-gray-300 rounded-md w-full">
                </div>
                <div class="flex gap-2">
                    @if(request()->hasAny(['buscar', 'vehiculo_id', 'tipo_servicio', 'fecha_desde']))
                        <a href="{{ route('mantenimientos.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded transition duration-200">
                            Limpiar
                        </a>
                    @endif
                    
                    <!-- Botones de exportación -->
                    <button type="button" onclick="descargarReporte('excel')" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-3 rounded flex items-center gap-1 transition duration-200" title="Descargar Excel">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Excel
                    </button>
                    
                    <button type="button" onclick="descargarReporte('pdf')" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-3 rounded flex items-center gap-1 transition duration-200" title="Descargar PDF">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        PDF
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Información de registros -->
    @if($mantenimientos->count() > 0)
    <div class="mb-4">
        <p class="text-sm text-gray-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            {{ $mantenimientos->total() }} registros encontrados
        </p>
    </div>
    @endif

    <!-- Tabla de mantenimientos -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            @if($mantenimientos->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activo</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sistema</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proveedor</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kilometraje</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Costo</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($mantenimientos as $mantenimiento)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ str_pad($mantenimiento->id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($mantenimiento->vehiculo)
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $mantenimiento->vehiculo->marca }} {{ $mantenimiento->vehiculo->modelo }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $mantenimiento->vehiculo->placas }}</div>
                                    @else
                                        <span class="text-sm text-gray-400">Activo no disponible</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($mantenimiento->tipo_servicio === 'PREVENTIVO')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Preventivo</span>
                                    @elseif($mantenimiento->tipo_servicio === 'CORRECTIVO')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Correctivo</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($mantenimiento->tipo_servicio) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $mantenimiento->sistema_vehiculo ?: 'General' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $mantenimiento->proveedor ?: 'No especificado' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $mantenimiento->fecha_inicio ? \Carbon\Carbon::parse($mantenimiento->fecha_inicio)->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ number_format($mantenimiento->kilometraje_servicio) }} km
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($mantenimiento->costo)
                                        ${{ number_format($mantenimiento->costo, 2) }}
                                    @else
                                        <span class="text-gray-400">No especificado</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($mantenimiento->fecha_fin)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completado</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">En Proceso</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <a href="{{ route('mantenimientos.show', $mantenimiento->id) }}" class="text-blue-600 hover:text-blue-900" title="Ver detalles">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('mantenimientos.edit', $mantenimiento->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </a>
                                        <button data-mantenimiento-id="{{ $mantenimiento->id }}" 
                                                data-mantenimiento-descripcion="#{{ str_pad($mantenimiento->id, 4, '0', STR_PAD_LEFT) }} - {{ $mantenimiento->vehiculo ? $mantenimiento->vehiculo->marca . ' ' . $mantenimiento->vehiculo->modelo . ' (' . $mantenimiento->vehiculo->placas . ')' : 'Activo no disponible' }}"
                                                class="btn-eliminar-mantenimiento text-red-600 hover:text-red-900" 
                                                title="Eliminar mantenimiento">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.4 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4zM6.7 8.8c-.7.7-1.9.7-2.6 0-.7-.7-.7-1.9 0-2.6.7-.7 1.9-.7 2.6 0 .7.7.7 1.9 0 2.6z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No hay mantenimientos registrados</h3>
                    <p class="text-gray-500 mb-6">
                        @if(request()->hasAny(['buscar', 'vehiculo_id', 'tipo_servicio', 'fecha_desde']))
                            No se encontraron mantenimientos con los filtros aplicados.
                        @else
                            Comience registrando el primer mantenimiento de su flota vehicular.
                        @endif
                    </p>
                    <a href="{{ route('mantenimientos.create') }}" 
                       class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded inline-flex items-center transition duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Registrar Mantenimiento
                    </a>
                </div>
            @endif
        </div>
    </div>

    @if($mantenimientos->count() > 0)
    <!-- Paginación -->
    <div class="mt-6">
        {{ $mantenimientos->appends(request()->query())->links() }}
    </div>
    @endif

    <!-- Modal de confirmación para eliminar mantenimiento -->
    <x-delete-confirmation-modal 
        id="modal-eliminar-mantenimiento"
        entity="el mantenimiento"
        entityIdField="mantenimiento-id"
        entityDisplayField="mantenimiento-descripcion"
        additionalText="Esta acción eliminará el registro de mantenimiento permanentemente."
    />

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elementos del formulario de filtros
    const searchInput = document.getElementById('search');
    const activoSelect = document.getElementById('activo');
    const tipoSelect = document.getElementById('tipo');
    const fechaInput = document.getElementById('fecha');
    const form = document.getElementById('filtrosForm');
    
    // Verificar que todos los elementos existen
    if (!searchInput || !activoSelect || !tipoSelect || !fechaInput || !form) {
        console.error('Algunos elementos del formulario no se encontraron:', {
            searchInput: !!searchInput,
            activoSelect: !!activoSelect,
            tipoSelect: !!tipoSelect,
            fechaInput: !!fechaInput,
            form: !!form
        });
        return;
    }
    
    // Variable para prevenir envíos múltiples
    let isSubmitting = false;
    
    // Función para enviar el formulario automáticamente
    function autoSubmit() {
        if (isSubmitting) return;
        isSubmitting = true;
        
        // Mostrar indicador de carga
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Filtrando...';
        }
        
        form.submit();
    }
    
    // Event listeners para filtros automáticos
    if (activoSelect) {
        activoSelect.addEventListener('change', autoSubmit);
    }
    
    if (tipoSelect) {
        tipoSelect.addEventListener('change', autoSubmit);
    }
    
    if (fechaInput) {
        fechaInput.addEventListener('change', autoSubmit);
    }
    
    // Event listener para búsqueda con delay
    let searchTimeout;
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                autoSubmit();
            }, 500); // Esperar 500ms después de que el usuario deje de escribir
        });
    }
    
    // Prevenir envío múltiple del formulario
    form.addEventListener('submit', function() {
        if (isSubmitting) return;
        isSubmitting = true;
        
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Filtrando...';
        }
    });

    // Inicializar modal de eliminación para mantenimientos
    if (typeof window.initDeleteModal === 'function') {
        window.initDeleteModal({
            modalId: 'modal-eliminar-mantenimiento',
            entityIdField: 'mantenimiento-id',
            entityDisplayField: 'mantenimiento-descripcion',
            deleteButtonSelector: '.btn-eliminar-mantenimiento',
            baseUrl: '{{ url("mantenimientos") }}'
        });
        console.log('✅ Modal de eliminación inicializado para: modal-eliminar-mantenimiento');
    } else {
        console.error('❌ Función initDeleteModal no encontrada');
    }
});

function descargarReporte(tipo) {
    // Mostrar indicador de carga
    const tipoReporte = tipo === 'pdf' ? 'PDF' : 'Excel';
    
    // Obtener los parámetros de filtro actuales del formulario
    const filtrosForm = document.getElementById('filtrosForm');
    const formData = new FormData(filtrosForm);
    
    // Crear URL con parámetros
    let url;
    if (tipo === 'pdf') {
        url = '{{ route("mantenimientos.descargar-pdf") }}';
    } else {
        url = '{{ route("mantenimientos.descargar-excel") }}';
    }
    
    // Construir query string con los filtros actuales
    const params = new URLSearchParams();
    
    // Obtener valores específicos de los filtros
    const buscar = document.querySelector('input[name="buscar"]')?.value?.trim() || '';
    const vehiculoId = document.querySelector('select[name="vehiculo_id"]')?.value || '';
    const tipoServicio = document.querySelector('select[name="tipo_servicio"]')?.value || '';
    const fechaDesde = document.querySelector('input[name="fecha_desde"]')?.value || '';
    
    // Agregar parámetros solo si tienen valor real (no vacío)
    if (buscar && buscar !== '') params.append('buscar', buscar);
    if (vehiculoId && vehiculoId !== '') params.append('vehiculo_id', vehiculoId);
    if (tipoServicio && tipoServicio !== '') params.append('tipo_servicio', tipoServicio);
    if (fechaDesde && fechaDesde !== '') params.append('fecha_desde', fechaDesde);
    
    // Crear URL completa
    const urlConParametros = url + '?' + params.toString();
    
    // Usar window.open para descargar sin afectar la página actual
    const ventanaDescarga = window.open(urlConParametros, '_blank');
    
    // Cerrar la ventana después de un breve momento (la descarga ya habrá iniciado)
    if (ventanaDescarga) {
        setTimeout(() => {
            ventanaDescarga.close();
        }, 1000);
    }
}
</script>
@endpush
