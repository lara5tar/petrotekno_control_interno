@extends('layouts.app')

@section('title', 'Alertas de Asignaciones')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">🚨 Alertas de Asignaciones</h3>
                    <p class="card-text text-muted">
                        Asignaciones que llevan más de {{ $resumen['criterio_dias'] }} días activas y requieren atención
                    </p>
                </div>

                <div class="card-body">
                    <!-- Resumen de alertas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h4>{{ $resumen['nivel_critico'] }}</h4>
                                    <small>Críticas (+60 días)</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning">
                                <div class="card-body text-center">
                                    <h4>{{ $resumen['nivel_alto'] }}</h4>
                                    <small>Altas (45-60 días)</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h4>{{ $resumen['nivel_medio'] }}</h4>
                                    <small>Medias (30-45 días)</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-secondary text-white">
                                <div class="card-body text-center">
                                    <h4>{{ $resumen['total_alertas'] }}</h4>
                                    <small>Total Alertas</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($alertas->count() > 0)
                        <!-- Tabla de alertas -->
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nivel</th>
                                        <th>Vehículo</th>
                                        <th>Operador</th>
                                        <th>Obra</th>
                                        <th>Fecha Asignación</th>
                                        <th>Días Activa</th>
                                        <th>Km Inicial</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($alertas as $alerta)
                                        <tr>
                                            <td>{{ $alerta['id'] }}</td>
                                            <td>
                                                @switch($alerta['nivel_alerta'])
                                                    @case('critico')
                                                        <span class="badge badge-danger">Crítico</span>
                                                        @break
                                                    @case('alto')
                                                        <span class="badge badge-warning">Alto</span>
                                                        @break
                                                    @case('medio')
                                                        <span class="badge badge-info">Medio</span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>{{ $alerta['vehiculo'] }}</td>
                                            <td>{{ $alerta['operador'] }}</td>
                                            <td>{{ $alerta['obra'] }}</td>
                                            <td>{{ $alerta['fecha_asignacion'] }}</td>
                                            <td>
                                                <strong>{{ $alerta['dias_activa'] }}</strong> días
                                            </td>
                                            <td>{{ number_format($alerta['kilometraje_inicial']) }} km</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('asignaciones.show', $alerta['id']) }}" 
                                                       class="btn btn-sm btn-info" title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('asignaciones.edit', $alerta['id']) }}" 
                                                       class="btn btn-sm btn-warning" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-success" 
                                                            data-toggle="modal" 
                                                            data-target="#liberarModal{{ $alerta['id'] }}"
                                                            title="Liberar asignación">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </div>

                                                <!-- Modal para liberar asignación -->
                                                <div class="modal fade" id="liberarModal{{ $alerta['id'] }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form method="POST" action="{{ route('asignaciones.liberar', $alerta['id']) }}">
                                                                @csrf
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Liberar Asignación #{{ $alerta['id'] }}</h5>
                                                                    <button type="button" class="close" data-dismiss="modal">
                                                                        <span>&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <label for="kilometraje_final">Kilometraje Final *</label>
                                                                        <input type="number" 
                                                                               class="form-control" 
                                                                               name="kilometraje_final" 
                                                                               min="{{ $alerta['kilometraje_inicial'] }}" 
                                                                               required>
                                                                        <small class="form-text text-muted">
                                                                            Debe ser mayor a {{ number_format($alerta['kilometraje_inicial']) }} km
                                                                        </small>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="observaciones_liberacion">Observaciones de Liberación</label>
                                                                        <textarea class="form-control" 
                                                                                  name="observaciones_liberacion" 
                                                                                  rows="3" 
                                                                                  placeholder="Motivo de la liberación..."></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                                    <button type="submit" class="btn btn-success">Liberar Asignación</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-success text-center">
                            <h5>✅ ¡Excelente!</h5>
                            <p>No hay asignaciones que requieran atención en este momento.</p>
                            <small>Todas las asignaciones están dentro del rango normal (menos de {{ $resumen['criterio_dias'] }} días).</small>
                        </div>
                    @endif

                    <!-- Botones de acción -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <a href="{{ route('asignaciones.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver a Asignaciones
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="button" class="btn btn-primary" onclick="window.location.reload()">
                                <i class="fas fa-sync"></i> Actualizar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script para auto-refresh cada 5 minutos -->
<script>
    // Auto-refresh cada 5 minutos para mantener las alertas actualizadas
    setTimeout(function() {
        window.location.reload();
    }, 300000); // 5 minutos

    // Confirmación antes de liberar
    document.querySelectorAll('[data-target^="#liberarModal"]').forEach(function(button) {
        button.addEventListener('click', function() {
            const modal = document.querySelector(this.getAttribute('data-target'));
            const form = modal.querySelector('form');
            
            form.addEventListener('submit', function(e) {
                if (!confirm('¿Está seguro de que desea liberar esta asignación?')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endsection
