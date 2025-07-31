<?php

/*
 * Test básico para validar la funcionalidad de creación de usuario
 * en el formulario de personal
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Personal;
use App\Models\User;
use App\Models\Role;
use App\Services\UsuarioService;

echo "=== TEST DE CREACIÓN DE USUARIO PARA PERSONAL ===\n\n";

// Simular datos de un personal
$personalData = [
    'nombre_completo' => 'Usuario de Prueba',
    'estatus' => 'activo',
    'categoria_id' => 1, // Asumiendo que existe
];

// Datos de usuario
$datosUsuario = [
    'email' => 'test.usuario@petrotekno.com',
    'rol_id' => 3, // Operador
    'tipo_password' => 'manual',
    'password_manual' => 'test12345',
    'password_manual_confirmation' => 'test12345',
];

echo "1. Verificando roles disponibles...\n";
$roles = Role::all();
foreach ($roles as $role) {
    echo "   - ID: {$role->id} | Nombre: {$role->nombre_rol}\n";
}

echo "\n2. Validando datos de usuario...\n";
$usuarioService = new UsuarioService();
$errores = $usuarioService->validarDatosUsuario($datosUsuario);

if (empty($errores)) {
    echo "   ✅ Validación exitosa\n";
} else {
    echo "   ❌ Errores encontrados:\n";
    foreach ($errores as $error) {
        echo "     - {$error}\n";
    }
}

echo "\n3. Verificando que el email no esté en uso...\n";
$emailEnUso = User::where('email', $datosUsuario['email'])->exists();
if ($emailEnUso) {
    echo "   ⚠️ El email ya está en uso\n";
} else {
    echo "   ✅ Email disponible\n";
}

echo "\n4. Verificando configuración de mail...\n";
echo "   - Mail driver: " . config('mail.default') . "\n";
echo "   - Mail host: " . config('mail.mailers.smtp.host') . "\n";
echo "   - Mail port: " . config('mail.mailers.smtp.port') . "\n";

echo "\n=== FIN DEL TEST ===\n";
