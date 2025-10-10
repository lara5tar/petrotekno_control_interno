@extends('layouts.app')

@section('title', 'Obras por Operador')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-2">📊 Obras por Operador</h1>
                    <p class="text-muted mb-0">Historial de asignaciones de obras a operadores</p>
                </div>
                <div>
                    <a href="{{ route('operadores.filtrar-por-obra') }}" class="btn btn-outline-primary">
                        <i class="fas fa-filter"></i> Filtrar por Obra
                    </a>
                </div>
            </div>

            <!-- Filtros y búsqueda -->
            <div class="bg-white rounded-lg shadow-md p-4 mb-4">
                <form method="GET" action="{{ route('operadores.obras-por-operador') }}" id="filtrosForm">
                    <div class="flex flex-wrap gap-4 items-end">
                        <div class="flex-1 md:flex-none md:w-64">
                            <label for="buscar" class="block text-sm font-medium text-gray-700 mb-1">Buscar Operador</label>
                            <input type="text" id="buscar" name="buscar" value="{{ request('buscar') }}" 
                                   placeholder="Nombre, número empleado o teléfono..." 
                                   class="p-2 border border-gray-300 rounded-md w-full">
                        </div>
                        <div class="flex-1 md:flex-none md:w-48">
                            <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                            <select id="estado" name="estado" class="p-2 border border-gray-300 rounded-md w-full">
                                <option value="">Todos los estados</option>
                                @if(isset($estadosOptions))
                                    @foreach($estadosOptions as $estado)
                                        <option value="{{ $estado }}" {{ request('estado') === $estado ? 'selected' : '' }}>
                                            {{ ucfirst($estado) }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="flex-1 md:flex-none md:w-64">
                            <label for="obra_id" class="block text-sm font-medium text-gray-700 mb-1">Obra Específica</label>
                            <select id="obra_id" name="obra_id" class="p-2 border border-gray-300 rounded-md w-full">
                                <option value="">Todas las obras</option>
                                @if(isset($obrasOptions))
                                    @foreach($obrasOptions as $obra)
                                        <option value="{{ $obra->id }}" {{ request('obra_id') == $obra->id ? 'selected' : '' }}>
                                            {{ $obra->nombre_obra }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="solo_activos" name="solo_activos" value="true" 
                                   {{ request('solo_activos') === 'true' ? 'checked' : '' }}
                                   class="mr-2">
                            <label for="solo_activos" class="text-sm font-medium text-gray-700">Solo Activos</label>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition duration-200">
                                Filtrar
                            </button>
                            @if(request()->hasAny(['buscar', 'estado', 'obra_id', 'solo_activos']))
                                <a href="{{ route('operadores.obras-por-operador') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded transition duration-200">
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
            @if($operadoresConObras->count() > 0)
            <div class="mb-4">
                <p class="text-sm text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    {{ $operadoresConObras->count() }} operadores encontrados
                </p>
            </div>
            @endif

            <!-- Estadísticas generales -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Total Operadores</h5>
                                    <h2 class="mb-0">{{ $estadisticas['total_operadores'] ?? 0 }}</h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Total Asignaciones</h5>
                                    <h2 class="mb-0">{{ $estadisticas['total_asignaciones'] ?? 0 }}</h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-tasks fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Promedio por Operador</h5>
                                    <h2 class="mb-0">{{ $estadisticas['promedio_asignaciones'] ?? 0 }}</h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-chart-line fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Operadores Activos</h5>
                                    <h2 class="mb-0">{{ $estadisticas['operadores_activos'] ?? 0 }}</h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-user-check fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de operadores -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">👨‍💼 Operadores con Historial de Obras</h5>
                </div>
                <div class="card-body">
                    @if($operadoresConObras->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Operador</th>
                                        <th>Categoría</th>
                                        <th>Estado</th>
                                        <th>Total Asignaciones</th>
                                        <th>Vehículo Actual</th>
                                        <th>Última Actividad</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($operadoresConObras as $operador)
                                        @php
                                            $vehiculoActual = $operador->vehiculoActual();
                                            $ultimaAsignacion = $operador->historialOperadorVehiculo()
                                                ->whereNotNull('obra_id')
                                                ->latest('fecha_asignacion')
                                                ->first();
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-2">
                                                        {{ substr($operador->nombre_completo, 0, 2) }}
                                                    </div>
                                                    <div>
                                                        <strong>{{ $operador->nombre_completo }}</strong>
                                                        <br>
                                                        <small class="text-muted">ID: {{ $operador->id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $operador->categoria->nombre_categoria ?? 'Sin categoría' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($operador->estatus === 'activo')
                                                    <span class="badge bg-success">Activo</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($operador->estatus) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-primary fs-6">
                                                    {{ $operador->total_asignaciones_obra }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($vehiculoActual)
                                                    <small>
                                                        <strong>{{ $vehiculoActual->marca }} {{ $vehiculoActual->modelo }}</strong><br>
                                                        <span class="text-muted">{{ $vehiculoActual->placas }}</span>
                                                    </small>
                                                @else
                                                    <span class="text-muted">Sin vehículo asignado</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($ultimaAsignacion)
                                                    <small>
                                                        {{ $ultimaAsignacion->fecha_asignacion->format('d/m/Y') }}<br>
                                                        <span class="text-muted">{{ $ultimaAsignacion->fecha_asignacion->diffForHumans() }}</span>
                                                    </small>
                                                @else
                                                    <span class="text-muted">Sin actividad</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('operadores.obras-operador.show', $operador) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="Ver detalle de obras">
                                                    <i class="fas fa-eye"></i> Ver Obras
                                                </a>
                                                <a href="{{ route('personal.show', $operador) }}" 
                                                   class="btn btn-sm btn-outline-secondary" title="Ver perfil completo">
                                                    <i class="fas fa-user"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay operadores con historial de obras</h5>
                            <p class="text-muted">Los operadores aparecerán aquí cuando se asignen a obras con vehículos.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    background-color: #6c757d;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}
</style>

<script>
function descargarReporte(tipo) {
    // Mostrar indicador de carga
    const tipoReporte = tipo === 'pdf' ? 'PDF' : 'Excel';
    
    // Obtener los parámetros de filtro actuales del formulario
    const filtrosForm = document.getElementById('filtrosForm');
    const formData = new FormData(filtrosForm);
    
    // Crear URL con parámetros
    let url;
    if (tipo === 'pdf') {
        url = '{{ route("operadores.obras-por-operador.descargar-pdf") }}';
    } else {
        url = '{{ route("operadores.obras-por-operador.descargar-excel") }}';
    }
    
    // Construir query string con los filtros actuales
    const params = new URLSearchParams();
    
    // Obtener valores específicos de los filtros
    const buscar = document.querySelector('input[name="buscar"]')?.value?.trim() || '';
    const estado = document.querySelector('select[name="estado"]')?.value || '';
    const obraId = document.querySelector('select[name="obra_id"]')?.value || '';
    const soloActivos = document.querySelector('input[name="solo_activos"]')?.checked || false;
    
    // Agregar parámetros solo si tienen valor real (no vacío)
    if (buscar && buscar !== '') params.append('buscar', buscar);
    if (estado && estado !== '') params.append('estado', estado);
    if (obraId && obraId !== '') params.append('obra_id', obraId);
    if (soloActivos) params.append('solo_activos', 'true');
    
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
@endsection
