@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Editar Mantenimiento #{{ $mantenimiento->id }}</h4>
                    <div class="btn-group" role="group">
                        <a href="{{ route('mantenimientos.show', $mantenimiento->id) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> Ver Detalles
                        </a>
                        <a href="{{ route('mantenimientos.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al Listado
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('mantenimientos.update', $mantenimiento->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Vehículo -->
                        <div class="form-group row">
                            <label for="vehiculo_id" class="col-md-4 col-form-label text-md-right">
                                Vehículo <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <select id="vehiculo_id" 
                                        class="form-control @error('vehiculo_id') is-invalid @enderror" 
                                        name="vehiculo_id" required>
                                    <option value="">Seleccione un vehículo</option>
                                    @foreach($vehiculosOptions as $vehiculo)
                                        <option value="{{ $vehiculo->id }}" 
                                                {{ (old('vehiculo_id', $mantenimiento->vehiculo_id) == $vehiculo->id) ? 'selected' : '' }}>
                                            {{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->placas }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('vehiculo_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Tipo de Servicio -->
                        <div class="form-group row">
                            <label for="tipo_servicio_id" class="col-md-4 col-form-label text-md-right">
                                Tipo de Servicio <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <select id="tipo_servicio_id" 
                                        class="form-control @error('tipo_servicio_id') is-invalid @enderror" 
                                        name="tipo_servicio_id" required>
                                    <option value="">Seleccione un tipo de servicio</option>
                                    @foreach($tiposServicioOptions as $tipo)
                                        <option value="{{ $tipo->id }}" 
                                                {{ (old('tipo_servicio_id', $mantenimiento->tipo_servicio_id) == $tipo->id) ? 'selected' : '' }}>
                                            {{ $tipo->nombre_tipo_servicio }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipo_servicio_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Proveedor -->
                        <div class="form-group row">
                            <label for="proveedor" class="col-md-4 col-form-label text-md-right">Proveedor</label>
                            <div class="col-md-6">
                                <input id="proveedor" 
                                       type="text" 
                                       class="form-control @error('proveedor') is-invalid @enderror" 
                                       name="proveedor" 
                                       value="{{ old('proveedor', $mantenimiento->proveedor) }}" 
                                       placeholder="Nombre del proveedor o taller">
                                @error('proveedor')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="form-group row">
                            <label for="descripcion" class="col-md-4 col-form-label text-md-right">Descripción</label>
                            <div class="col-md-6">
                                <textarea id="descripcion" 
                                          class="form-control @error('descripcion') is-invalid @enderror" 
                                          name="descripcion" 
                                          rows="3" 
                                          placeholder="Detalles del mantenimiento realizado">{{ old('descripcion', $mantenimiento->descripcion) }}</textarea>
                                @error('descripcion')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Fecha de Inicio -->
                        <div class="form-group row">
                            <label for="fecha_inicio" class="col-md-4 col-form-label text-md-right">
                                Fecha de Inicio <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <input id="fecha_inicio" 
                                       type="date" 
                                       class="form-control @error('fecha_inicio') is-invalid @enderror" 
                                       name="fecha_inicio" 
                                       value="{{ old('fecha_inicio', $mantenimiento->fecha_inicio ? $mantenimiento->fecha_inicio->format('Y-m-d') : '') }}" 
                                       required>
                                @error('fecha_inicio')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Fecha de Fin -->
                        <div class="form-group row">
                            <label for="fecha_fin" class="col-md-4 col-form-label text-md-right">Fecha de Fin</label>
                            <div class="col-md-6">
                                <input id="fecha_fin" 
                                       type="date" 
                                       class="form-control @error('fecha_fin') is-invalid @enderror" 
                                       name="fecha_fin" 
                                       value="{{ old('fecha_fin', $mantenimiento->fecha_fin ? $mantenimiento->fecha_fin->format('Y-m-d') : '') }}">
                                <small class="form-text text-muted">
                                    Dejar vacío si el mantenimiento está en proceso
                                </small>
                                @error('fecha_fin')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Kilometraje del Servicio -->
                        <div class="form-group row">
                            <label for="kilometraje_servicio" class="col-md-4 col-form-label text-md-right">
                                Kilometraje del Servicio <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <input id="kilometraje_servicio" 
                                       type="number" 
                                       class="form-control @error('kilometraje_servicio') is-invalid @enderror" 
                                       name="kilometraje_servicio" 
                                       value="{{ old('kilometraje_servicio', $mantenimiento->kilometraje_servicio) }}" 
                                       min="0" 
                                       step="1"
                                       placeholder="Kilometraje cuando se realizó el servicio" 
                                       required>
                                @error('kilometraje_servicio')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Costo -->
                        <div class="form-group row">
                            <label for="costo" class="col-md-4 col-form-label text-md-right">Costo</label>
                            <div class="col-md-6">
                                <input id="costo" 
                                       type="number" 
                                       class="form-control @error('costo') is-invalid @enderror" 
                                       name="costo" 
                                       value="{{ old('costo', $mantenimiento->costo) }}" 
                                       min="0" 
                                       step="0.01"
                                       placeholder="0.00">
                                @error('costo')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Información del Mantenimiento Actual -->
                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="alert alert-info">
                                    <strong>Información del mantenimiento:</strong><br>
                                    <small>
                                        Creado: {{ $mantenimiento->created_at ? $mantenimiento->created_at->format('d/m/Y H:i:s') : 'No disponible' }}<br>
                                        Última modificación: {{ $mantenimiento->updated_at ? $mantenimiento->updated_at->format('d/m/Y H:i:s') : 'No disponible' }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Actualizar Mantenimiento
                                </button>
                                <a href="{{ route('mantenimientos.show', $mantenimiento->id) }}" class="btn btn-info ml-2">
                                    <i class="fas fa-eye"></i> Ver Detalles
                                </a>
                                <a href="{{ route('mantenimientos.index') }}" class="btn btn-secondary ml-2">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
