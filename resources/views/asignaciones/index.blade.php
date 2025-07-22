@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>{{ __('Gestión de Asignaciones') }}</h4>
                    <a href="{{ route('asignaciones.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nueva Asignación
                    </a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Filtros de búsqueda -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <select class="form-select" id="filtro-vehiculo">
                                <option value="">Todos los vehículos</option>
                                @foreach($vehiculos ?? [] as $vehiculo)
                                    <option value="{{ $vehiculo->id }}">{{ $vehiculo->nombre_completo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="filtro-obra">
                                <option value="">Todas las obras</option>
                                @foreach($obras ?? [] as $obra)
                                    <option value="{{ $obra->id }}">{{ $obra->nombre_obra }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="filtro-estado">
                                <option value="">Todos los estados</option>
                                <option value="activa">Activas</option>
                                <option value="liberada">Liberadas</option>
                            </select>
                        </div>
                    </div>

                    <!-- Tabla de asignaciones -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="asignaciones-table">
                            <thead class="table-dark">
                                <tr>
                                    <th>Vehículo</th>
                                    <th>Operador</th>
                                    <th>Obra</th>
                                    <th>Fecha Asignación</th>
                                    <th>Estado</th>
                                    <th>Km Inicial</th>
                                    <th>Km Final</th>
                                    <th>Km Recorrido</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($asignaciones ?? [] as $asignacion)
                                    <tr>
                                        <td>
                                            <strong>{{ $asignacion->vehiculo->nombre_completo ?? 'N/A' }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $asignacion->vehiculo->placas ?? 'Sin placas' }}</small>
                                        </td>
                                        <td>{{ $asignacion->personal->nombre_completo ?? 'N/A' }}</td>
                                        <td>{{ $asignacion->obra->nombre_obra ?? 'N/A' }}</td>
                                        <td>{{ $asignacion->fecha_asignacion ? \Carbon\Carbon::parse($asignacion->fecha_asignacion)->format('d/m/Y H:i') : 'N/A' }}</td>
                                        <td>
                                            @if($asignacion->esta_activa)
                                                <span class="badge bg-success">Activa</span>
                                            @else
                                                <span class="badge bg-secondary">Liberada</span>
                                                <br>
                                                <small class="text-muted">
                                                    {{ $asignacion->fecha_liberacion ? \Carbon\Carbon::parse($asignacion->fecha_liberacion)->format('d/m/Y H:i') : '' }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>{{ number_format($asignacion->kilometraje_inicial ?? 0) }} km</td>
                                        <td>{{ $asignacion->kilometraje_final ? number_format($asignacion->kilometraje_final) . ' km' : '-' }}</td>
                                        <td>{{ $asignacion->kilometraje_recorrido ? number_format($asignacion->kilometraje_recorrido) . ' km' : '-' }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('asignaciones.show', $asignacion->id) }}" class="btn btn-sm btn-outline-info" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                @if($asignacion->esta_activa)
                                                    <a href="{{ route('asignaciones.edit', $asignacion->id) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    <button type="button" class="btn btn-sm btn-outline-success" 
                                                            onclick="liberarAsignacion({{ $asignacion->id }})" title="Liberar">
                                                        <i class="fas fa-unlock"></i>
                                                    </button>
                                                    
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            onclick="eliminarAsignacion({{ $asignacion->id }})" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p>No hay asignaciones registradas</p>
                                            <a href="{{ route('asignaciones.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Crear primera asignación
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if(isset($asignaciones) && method_exists($asignaciones, 'links'))
                        <div class="d-flex justify-content-center">
                            {{ $asignaciones->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para liberar asignación -->
<div class="modal fade" id="liberarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Liberar Asignación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="liberarForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="kilometraje_final" class="form-label">Kilometraje Final *</label>
                        <input type="number" class="form-control" id="kilometraje_final" name="kilometraje_final" required>
                        <small class="form-text text-muted">Debe ser mayor al kilometraje inicial</small>
                    </div>
                    <div class="mb-3">
                        <label for="observaciones_liberacion" class="form-label">Observaciones de Liberación</label>
                        <textarea class="form-control" id="observaciones_liberacion" name="observaciones_liberacion" rows="3" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Liberar Asignación</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function liberarAsignacion(id) {
        const form = document.getElementById('liberarForm');
        form.action = `{{ route('asignaciones.index') }}/${id}/liberar`;
        
        // Limpiar formulario
        document.getElementById('kilometraje_final').value = '';
        document.getElementById('observaciones_liberacion').value = '';
        
        // Mostrar modal
        new bootstrap.Modal(document.getElementById('liberarModal')).show();
    }

    function eliminarAsignacion(id) {
        if (confirm('¿Estás seguro de que deseas eliminar esta asignación?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route('asignaciones.index') }}/${id}`;
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            
            form.appendChild(methodInput);
            form.appendChild(csrfInput);
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Filtros de búsqueda
    document.addEventListener('DOMContentLoaded', function() {
        const filtroVehiculo = document.getElementById('filtro-vehiculo');
        const filtroObra = document.getElementById('filtro-obra');
        const filtroEstado = document.getElementById('filtro-estado');
        const tabla = document.getElementById('asignaciones-table');

        function filtrarTabla() {
            const filas = tabla.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            
            for (let fila of filas) {
                let mostrar = true;
                
                // Aquí puedes implementar la lógica de filtrado
                // Por simplicidad, solo mostramos todas las filas
                
                fila.style.display = mostrar ? '' : 'none';
            }
        }

        filtroVehiculo.addEventListener('change', filtrarTabla);
        filtroObra.addEventListener('change', filtrarTabla);
        filtroEstado.addEventListener('change', filtrarTabla);
    });
</script>
@endpush
