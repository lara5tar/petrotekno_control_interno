# Creación de Usuario del Sistema - Guía de Integración Frontend

## Resumen de Funcionalidad

Se ha implementado una nueva funcionalidad que permite crear usuarios del sistema automáticamente al crear registros de personal. Esta integración permite:

1. **Creación opcional de usuario**: Checkbox para habilitar/deshabilitar la creación de usuario
2. **Configuración de credenciales**: Email, rol y tipo de contraseña
3. **Opciones de contraseña**: Aleatoria (enviada por email) o manual
4. **Validación frontend y backend**: Validación completa en tiempo real

## Estructura del Frontend

### Campos del Formulario

```html
<!-- Checkbox principal para habilitar creación de usuario -->
<input type="checkbox" name="crear_usuario" value="1" id="crear_usuario">

<!-- Campos de usuario (ocultos por defecto) -->
<div id="campos_usuario" style="display: none;">
    <!-- Email del usuario -->
    <input type="email" name="email_usuario" id="correo" required>
    
    <!-- Rol del usuario -->
    <select name="rol_usuario" id="rol_id" required>
        <option value="">Seleccione un rol</option>
        @foreach($roles as $rol)
            <option value="{{ $rol->id }}">{{ $rol->nombre_rol }}</option>
        @endforeach
    </select>
    
    <!-- Tipo de contraseña -->
    <input type="radio" name="tipo_password" value="aleatoria" id="password_aleatorio" checked>
    <input type="radio" name="tipo_password" value="manual" id="password_manual_radio">
    
    <!-- Campos de contraseña manual (ocultos por defecto) -->
    <div id="campos_password_manual" style="display: none;">
        <input type="password" name="password_manual" id="password">
        <input type="password" name="password_manual_confirmation" id="password_confirmation">
    </div>
</div>
```

### JavaScript de Validación

```javascript
// Variables principales
const crearUsuarioCheckbox = document.getElementById('crear_usuario');
const camposUsuario = document.getElementById('campos_usuario');
const passwordAleatorioRadio = document.getElementById('password_aleatorio');
const passwordManualRadio = document.getElementById('password_manual_radio');
const camposPasswordManual = document.getElementById('campos_password_manual');

// Funciones de control
function toggleCamposUsuario() {
    if (crearUsuarioCheckbox.checked) {
        camposUsuario.style.display = 'block';
        // Hacer campos requeridos
    } else {
        camposUsuario.style.display = 'none';
        // Quitar requeridos y limpiar valores
    }
}

function togglePasswordManual() {
    if (passwordManualRadio.checked) {
        camposPasswordManual.style.display = 'block';
        // Hacer contraseñas requeridas
    } else {
        camposPasswordManual.style.display = 'none';
        // Quitar requeridos
    }
}
```

## Validaciones Backend

### Reglas de Validación

```php
// En CreatePersonalRequest.php
'crear_usuario' => 'nullable|boolean',
'email_usuario' => [
    'required_if:crear_usuario,1',
    'nullable',
    'email',
    'max:255',
    'unique:users,email'
],
'rol_usuario' => [
    'required_if:crear_usuario,1',
    'nullable',
    'integer',
    'exists:roles,id'
],
'tipo_password' => [
    'required_if:crear_usuario,1',
    Rule::in(['aleatoria', 'manual'])
],
'password_manual' => [
    'required_if:tipo_password,manual',
    'nullable',
    'string',
    'min:8',
    'confirmed'
],
'password_manual_confirmation' => [
    'required_if:tipo_password,manual',
    'nullable',
    'string'
]
```

### Mensajes de Error Personalizados

```php
'email_usuario.required_if' => 'El email es obligatorio cuando se crea un usuario',
'email_usuario.unique' => 'El email ya está registrado',
'rol_usuario.required_if' => 'Debe seleccionar un rol cuando se crea un usuario',
'tipo_password.required_if' => 'Debe seleccionar el tipo de contraseña',
'password_manual.required_if' => 'La contraseña es obligatoria cuando se selecciona tipo manual',
'password_manual.confirmed' => 'La confirmación de contraseña no coincide'
```

## Controlador y Lógica de Negocio

### Flujo de Creación

```php
// En PersonalManagementController@storeWeb
if (!empty($validatedData['crear_usuario']) && $validatedData['crear_usuario']) {
    $usuarioService = new UsuarioService();
    
    $datosUsuario = [
        'email' => $validatedData['email_usuario'],
        'rol_id' => $validatedData['rol_usuario'],
        'tipo_password' => $validatedData['tipo_password'],
        'password_manual' => $validatedData['password_manual'] ?? null,
        'password_manual_confirmation' => $validatedData['password_manual_confirmation'] ?? null,
    ];

    $usuario = $usuarioService->crearUsuarioParaPersonal($personal, $datosUsuario);
}
```

### Servicio UsuarioService

```php
class UsuarioService
{
    /**
     * Crear un usuario del sistema para un personal
     */
    public function crearUsuarioParaPersonal(Personal $personal, array $datosUsuario): User
    {
        // Validaciones
        // Generación de contraseña
        // Creación del usuario
        // Envío de email (si es aleatoria)
        
        return $usuario;
    }
    
    /**
     * Generar contraseña aleatoria segura
     */
    private function generarPasswordAleatoria(): string
    {
        // 12 caracteres con mayúsculas, minúsculas, números y símbolos
    }
    
    /**
     * Enviar contraseña por email
     */
    private function enviarPasswordPorEmail(User $usuario, string $password, Personal $personal): void
    {
        // Template: emails.nuevo-usuario
    }
}
```

## Template de Email

### Archivo: resources/views/emails/nuevo-usuario.blade.php

```html
<!DOCTYPE html>
<html>
<head>
    <title>Acceso al Sistema</title>
    <style>
        /* Estilos profesionales con colores corporativos */
    </style>
</head>
<body>
    <div class="container">
        <h1>{{ $sistema }}</h1>
        <p>Hola <strong>{{ $nombre }}</strong>,</p>
        
        <div class="credentials">
            <h3>📋 Credenciales de Acceso</h3>
            <div>Email: {{ $email }}</div>
            <div>Contraseña: {{ $password }}</div>
            <div>Rol: {{ $rol }}</div>
        </div>
        
        <div class="security-note">
            <h4>🔒 Importante - Seguridad</h4>
            <ul>
                <li>Cambia tu contraseña inmediatamente</li>
                <li>No compartas estas credenciales</li>
                <li>Elimina este email una vez guardada la contraseña</li>
            </ul>
        </div>
        
        <a href="{{ $url_login }}">🚀 Acceder al Sistema</a>
    </div>
</body>
</html>
```

## Roles Disponibles

Los roles se cargan dinámicamente desde la base de datos:

- **Admin (ID: 1)**: Acceso completo al sistema
- **Supervisor (ID: 2)**: Acceso limitado de gestión  
- **Operador (ID: 3)**: Acceso básico de consulta

## Respuestas del Sistema

### Éxito con Contraseña Aleatoria
```
"Personal creado exitosamente. Usuario creado exitosamente. Se ha enviado un email con las credenciales de acceso"
```

### Éxito con Contraseña Manual
```
"Personal creado exitosamente. Usuario creado exitosamente con contraseña manual"
```

### Error de Validación
```php
// Respuesta JSON con errores específicos por campo
{
    "errors": {
        "email_usuario": ["El email ya está registrado"],
        "password_manual": ["La confirmación de contraseña no coincide"]
    }
}
```

## Consideraciones de Seguridad

1. **Contraseñas aleatorias**: 12 caracteres con mayúsculas, minúsculas, números y símbolos
2. **Validación de email**: Formato válido y unicidad en la base de datos
3. **Confirmación de contraseña**: Requerida para contraseñas manuales
4. **Logs de auditoría**: Todas las acciones se registran en LogAccion
5. **Transacciones de BD**: Rollback automático en caso de error

## Testing

### Validación Frontend
- Campos se muestran/ocultan correctamente
- Validación en tiempo real
- Mensajes de error claros

### Validación Backend  
- Reglas de validación correctas
- Unicidad de email
- Generación segura de contraseñas
- Envío de emails

### Pruebas de Integración
- Creación completa de personal + usuario
- Rollback en caso de errores
- Logs de auditoría correctos

## Próximas Mejoras

1. **Validación de email en tiempo real**: AJAX para verificar disponibilidad
2. **Generador visual de contraseñas**: Mostrar fortaleza de contraseña
3. **Plantillas de email personalizables**: Admin panel para modificar templates
4. **Historial de envíos**: Tracking de emails enviados
5. **Reenvío de credenciales**: Funcionalidad para reenviar contraseñas
