@extends('layouts.app')

@section('title', 'Crear Personal Completo')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-user-plus me-2"></i>
                        Crear Personal Completo
                    </h3>
                    <a href="{{ route('personal.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Volver
                    </a>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle me-1"></i> Errores de validación:</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('personal.complete.store') }}" method="POST" enctype="multipart/form-data" id="personalCompleteForm">
                        @csrf

                        <!-- Datos Básicos del Personal -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="fas fa-user me-2"></i>
                                    Datos Básicos del Personal
                                </h5>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nombre_completo" class="form-label">
                                    <i class="fas fa-user me-1"></i>
                                    Nombre Completo <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('nombre_completo') is-invalid @enderror" 
                                       id="nombre_completo" 
                                       name="nombre_completo" 
                                       value="{{ old('nombre_completo') }}" 
                                       required>
                                @error('nombre_completo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="estatus" class="form-label">
                                    <i class="fas fa-toggle-on me-1"></i>
                                    Estatus <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('estatus') is-invalid @enderror" 
                                        id="estatus" 
                                        name="estatus" 
                                        required>
                                    <option value="">Seleccionar estatus</option>
                                    <option value="activo" {{ old('estatus') == 'activo' ? 'selected' : '' }}>Activo</option>
                                    <option value="inactivo" {{ old('estatus') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                    <option value="suspendido" {{ old('estatus') == 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                                    <option value="vacaciones" {{ old('estatus') == 'vacaciones' ? 'selected' : '' }}>Vacaciones</option>
                                </select>
                                @error('estatus')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="categoria_personal_id" class="form-label">
                                    <i class="fas fa-tags me-1"></i>
                                    Categoría <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('categoria_personal_id') is-invalid @enderror" 
                                        id="categoria_personal_id" 
                                        name="categoria_personal_id" 
                                        required>
                                    <option value="">Seleccionar categoría</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id }}" 
                                                {{ old('categoria_personal_id') == $categoria->id ? 'selected' : '' }}>
                                            {{ $categoria->nombre_categoria }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('categoria_personal_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Documentos -->
                        <div class="row mb-4 mt-5">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="fas fa-file-alt me-2"></i>
                                    Documentos del Personal
                                </h5>
                                <p class="text-muted small mb-3">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Sube los documentos disponibles. Los archivos deben ser PDF, JPG, PNG o DOC (máximo 10MB cada uno).
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <!-- Identificación Oficial -->
                            <div class="col-md-6 mb-3">
                                <label for="documento_identificacion" class="form-label">
                                    <i class="fas fa-id-card me-1"></i>
                                    Identificación Oficial (INE/IFE)
                                </label>
                                <input type="file" 
                                       class="form-control @error('documento_identificacion') is-invalid @enderror" 
                                       id="documento_identificacion" 
                                       name="documento_identificacion" 
                                       accept=".pdf,.jpg,.jpeg,.png">
                                @error('documento_identificacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                <label for="descripcion_identificacion" class="form-label mt-2">
                                    <i class="fas fa-comment me-1"></i>
                                    Descripción del documento
                                </label>
                                <input type="text" 
                                       class="form-control @error('descripcion_identificacion') is-invalid @enderror" 
                                       id="descripcion_identificacion" 
                                       name="descripcion_identificacion" 
                                       value="{{ old('descripcion_identificacion') }}"
                                       placeholder="Ej: INE vigente, ambos lados">
                                @error('descripcion_identificacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                <label for="fecha_vencimiento_identificacion" class="form-label mt-2">
                                    <i class="fas fa-calendar me-1"></i>
                                    Fecha de Vencimiento
                                </label>
                                <input type="date" 
                                       class="form-control @error('fecha_vencimiento_identificacion') is-invalid @enderror" 
                                       id="fecha_vencimiento_identificacion" 
                                       name="fecha_vencimiento_identificacion" 
                                       value="{{ old('fecha_vencimiento_identificacion') }}" 
                                       min="{{ date('Y-m-d') }}">
                                @error('fecha_vencimiento_identificacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- CURP -->
                            <div class="col-md-6 mb-3">
                                <label for="documento_curp" class="form-label">
                                    <i class="fas fa-file-text me-1"></i>
                                    CURP
                                </label>
                                <input type="file" 
                                       class="form-control @error('documento_curp') is-invalid @enderror" 
                                       id="documento_curp" 
                                       name="documento_curp" 
                                       accept=".pdf,.jpg,.jpeg,.png">
                                @error('documento_curp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                <label for="descripcion_curp" class="form-label mt-2">
                                    <i class="fas fa-comment me-1"></i>
                                    Descripción del documento
                                </label>
                                <input type="text" 
                                       class="form-control @error('descripcion_curp') is-invalid @enderror" 
                                       id="descripcion_curp" 
                                       name="descripcion_curp" 
                                       value="{{ old('descripcion_curp') }}"
                                       placeholder="Ej: CURP original">
                                @error('descripcion_curp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <!-- RFC -->
                            <div class="col-md-6 mb-3">
                                <label for="documento_rfc" class="form-label">
                                    <i class="fas fa-file-invoice me-1"></i>
                                    RFC
                                </label>
                                <input type="file" 
                                       class="form-control @error('documento_rfc') is-invalid @enderror" 
                                       id="documento_rfc" 
                                       name="documento_rfc" 
                                       accept=".pdf,.jpg,.jpeg,.png">
                                @error('documento_rfc')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                <label for="descripcion_rfc" class="form-label mt-2">
                                    <i class="fas fa-comment me-1"></i>
                                    Descripción del documento
                                </label>
                                <input type="text" 
                                       class="form-control @error('descripcion_rfc') is-invalid @enderror" 
                                       id="descripcion_rfc" 
                                       name="descripcion_rfc" 
                                       value="{{ old('descripcion_rfc') }}"
                                       placeholder="Ej: RFC con homoclave">
                                @error('descripcion_rfc')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- NSS -->
                            <div class="col-md-6 mb-3">
                                <label for="documento_nss" class="form-label">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    NSS (Número de Seguridad Social)
                                </label>
                                <input type="file" 
                                       class="form-control @error('documento_nss') is-invalid @enderror" 
                                       id="documento_nss" 
                                       name="documento_nss" 
                                       accept=".pdf,.jpg,.jpeg,.png">
                                @error('documento_nss')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                <label for="descripcion_nss" class="form-label mt-2">
                                    <i class="fas fa-comment me-1"></i>
                                    Descripción del documento
                                </label>
                                <input type="text" 
                                       class="form-control @error('descripcion_nss') is-invalid @enderror" 
                                       id="descripcion_nss" 
                                       name="descripcion_nss" 
                                       value="{{ old('descripcion_nss') }}"
                                       placeholder="Ej: Comprobante IMSS vigente">
                                @error('descripcion_nss')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <!-- Licencia de Conducir -->
                            <div class="col-md-6 mb-3">
                                <label for="documento_licencia" class="form-label">
                                    <i class="fas fa-car me-1"></i>
                                    Licencia de Conducir
                                </label>
                                <input type="file" 
                                       class="form-control @error('documento_licencia') is-invalid @enderror" 
                                       id="documento_licencia" 
                                       name="documento_licencia" 
                                       accept=".pdf,.jpg,.jpeg,.png">
                                @error('documento_licencia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                <label for="descripcion_licencia" class="form-label mt-2">
                                    <i class="fas fa-comment me-1"></i>
                                    Descripción del documento
                                </label>
                                <input type="text" 
                                       class="form-control @error('descripcion_licencia') is-invalid @enderror" 
                                       id="descripcion_licencia" 
                                       name="descripcion_licencia" 
                                       value="{{ old('descripcion_licencia') }}"
                                       placeholder="Ej: Licencia tipo A, ambos lados">
                                @error('descripcion_licencia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                <label for="fecha_vencimiento_licencia" class="form-label mt-2">
                                    <i class="fas fa-calendar me-1"></i>
                                    Fecha de Vencimiento
                                </label>
                                <input type="date" 
                                       class="form-control @error('fecha_vencimiento_licencia') is-invalid @enderror" 
                                       id="fecha_vencimiento_licencia" 
                                       name="fecha_vencimiento_licencia" 
                                       value="{{ old('fecha_vencimiento_licencia') }}" 
                                       min="{{ date('Y-m-d') }}">
                                @error('fecha_vencimiento_licencia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- CV Profesional -->
                            <div class="col-md-6 mb-3">
                                <label for="documento_cv" class="form-label">
                                    <i class="fas fa-file-pdf me-1"></i>
                                    CV Profesional
                                </label>
                                <input type="file" 
                                       class="form-control @error('documento_cv') is-invalid @enderror" 
                                       id="documento_cv" 
                                       name="documento_cv" 
                                       accept=".pdf,.doc,.docx">
                                @error('documento_cv')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                <label for="descripcion_cv" class="form-label mt-2">
                                    <i class="fas fa-comment me-1"></i>
                                    Descripción del documento
                                </label>
                                <input type="text" 
                                       class="form-control @error('descripcion_cv') is-invalid @enderror" 
                                       id="descripcion_cv" 
                                       name="descripcion_cv" 
                                       value="{{ old('descripcion_cv') }}"
                                       placeholder="Ej: CV actualizado con experiencia">
                                @error('descripcion_cv')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <!-- Comprobante de Domicilio -->
                            <div class="col-md-6 mb-3">
                                <label for="documento_domicilio" class="form-label">
                                    <i class="fas fa-home me-1"></i>
                                    Comprobante de Domicilio
                                </label>
                                <input type="file" 
                                       class="form-control @error('documento_domicilio') is-invalid @enderror" 
                                       id="documento_domicilio" 
                                       name="documento_domicilio" 
                                       accept=".pdf,.jpg,.jpeg,.png">
                                @error('documento_domicilio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                <label for="descripcion_domicilio" class="form-label mt-2">
                                    <i class="fas fa-comment me-1"></i>
                                    Descripción del documento
                                </label>
                                <input type="text" 
                                       class="form-control @error('descripcion_domicilio') is-invalid @enderror" 
                                       id="descripcion_domicilio" 
                                       name="descripcion_domicilio" 
                                       value="{{ old('descripcion_domicilio') }}"
                                       placeholder="Ej: Recibo de luz no mayor a 3 meses">
                                @error('descripcion_domicilio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Usuario del Sistema -->
                        <div class="row mb-4 mt-5">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="fas fa-user-cog me-2"></i>
                                    Usuario del Sistema (Opcional)
                                </h5>
                                <p class="text-muted small mb-3">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Si deseas crear un usuario para que esta persona pueda acceder al sistema, marca la casilla y completa los datos.
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="crear_usuario" 
                                           name="crear_usuario" 
                                           value="1" 
                                           {{ old('crear_usuario') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="crear_usuario">
                                        <i class="fas fa-user-plus me-1"></i>
                                        Crear usuario para el sistema
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="usuario_fields" class="d-none">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-1"></i>
                                        Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="rol_id" class="form-label">
                                        <i class="fas fa-user-tag me-1"></i>
                                        Rol <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('rol_id') is-invalid @enderror" 
                                            id="rol_id" 
                                            name="rol_id">
                                        <option value="">Seleccionar rol</option>
                                        @foreach($roles as $rol)
                                            <option value="{{ $rol->id }}" 
                                                    {{ old('rol_id') == $rol->id ? 'selected' : '' }}>
                                                {{ $rol->nombre_rol }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('rol_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="password" class="form-label">
                                        <i class="fas fa-lock me-1"></i>
                                        Contraseña <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label">
                                        <i class="fas fa-lock me-1"></i>
                                        Confirmar Contraseña <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation">
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('personal.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i>
                                        Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="fas fa-save me-1"></i>
                                        Crear Personal Completo
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Mostrar/ocultar campos de usuario
    $('#crear_usuario').change(function() {
        if ($(this).is(':checked')) {
            $('#usuario_fields').removeClass('d-none');
            $('#email, #password, #password_confirmation, #rol_id').prop('required', true);
        } else {
            $('#usuario_fields').addClass('d-none');
            $('#email, #password, #password_confirmation, #rol_id').prop('required', false);
        }
    });

    // Verificar estado inicial
    if ($('#crear_usuario').is(':checked')) {
        $('#usuario_fields').removeClass('d-none');
        $('#email, #password, #password_confirmation, #rol_id').prop('required', true);
    }

    // Validación del formulario
    $('#personalCompleteForm').on('submit', function(e) {
        let isValid = true;
        let errorMessage = '';

        // Validar que al menos un documento esté seleccionado
        const documentos = [
            '#documento_identificacion',
            '#documento_curp',
            '#documento_rfc',
            '#documento_nss',
            '#documento_licencia',
            '#documento_cv',
            '#documento_domicilio'
        ];

        let tieneDocumento = false;
        documentos.forEach(function(selector) {
            if ($(selector)[0].files.length > 0) {
                tieneDocumento = true;
            }
        });

        if (!tieneDocumento) {
            isValid = false;
            errorMessage += 'Debe subir al menos un documento.\n';
        }

        // Validar campos de usuario si está marcado
        if ($('#crear_usuario').is(':checked')) {
            if (!$('#email').val()) {
                isValid = false;
                errorMessage += 'El email es requerido cuando se crea un usuario.\n';
            }
            if (!$('#password').val()) {
                isValid = false;
                errorMessage += 'La contraseña es requerida cuando se crea un usuario.\n';
            }
            if ($('#password').val() !== $('#password_confirmation').val()) {
                isValid = false;
                errorMessage += 'Las contraseñas no coinciden.\n';
            }
            if (!$('#rol_id').val()) {
                isValid = false;
                errorMessage += 'El rol es requerido cuando se crea un usuario.\n';
            }
        }

        if (!isValid) {
            e.preventDefault();
            alert('Errores de validación:\n\n' + errorMessage);
            return false;
        }

        // Deshabilitar botón de envío para evitar doble envío
        $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Creando...');
    });

    // Validación de tamaño de archivo
    $('input[type="file"]').change(function() {
        const file = this.files[0];
        if (file && file.size > 10 * 1024 * 1024) { // 10MB
            alert('El archivo es demasiado grande. El tamaño máximo permitido es 10MB.');
            $(this).val('');
        }
    });
});
</script>
@endpush
@endsection