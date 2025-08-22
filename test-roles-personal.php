<?php

require_once __DIR__ . '/vendor/autoload.php';

// Configurar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Role;
use App\Models\Personal;
use App\Models\User;

echo "🧪 Probando sistema de roles en personal...\n\n";

try {
    // 1. Verificar que existen roles en el sistema
    $roles = Role::all();
    echo "🔐 Roles disponibles en el sistema: {$roles->count()}\n";
    
    foreach ($roles as $rol) {
        echo "   - {$rol->nombre_rol} (ID: {$rol->id})\n";
    }
    echo "\n";
    
    // 2. Verificar personal con usuario asociado
    $personalConUsuario = Personal::with(['usuario.rol'])->whereHas('usuario')->get();
    echo "👥 Personal con usuario asociado: {$personalConUsuario->count()}\n\n";
    
    foreach ($personalConUsuario as $persona) {
        echo "📋 {$persona->nombre_completo} (ID: {$persona->id})\n";
        if ($persona->usuario) {
            echo "   Usuario: {$persona->usuario->email}\n";
            echo "   Rol ID: " . ($persona->usuario->rol_id ?? 'No asignado') . "\n";
            echo "   Rol: " . ($persona->usuario->rol?->nombre_rol ?? 'No asignado') . "\n";
        } else {
            echo "   Sin usuario asociado\n";
        }
        echo "\n";
    }
    
    // 3. Si no hay personal con usuario, crear uno de prueba
    if ($personalConUsuario->count() === 0) {
        echo "ℹ️ No hay personal con usuario. Creando uno de prueba...\n";
        
        $personal = Personal::first();
        if (!$personal) {
            echo "❌ No hay personal en el sistema\n";
            exit(1);
        }
        
        $primerRol = Role::first();
        if (!$primerRol) {
            echo "❌ No hay roles en el sistema\n";
            exit(1);
        }
        
        // Crear usuario de prueba
        $usuario = User::create([
            'name' => $personal->nombre_completo,
            'email' => 'test_' . strtolower(str_replace(' ', '_', $personal->nombre_completo)) . '@petrotekno.com',
            'password' => bcrypt('password123'),
            'personal_id' => $personal->id,
            'rol_id' => $primerRol->id,
        ]);
        
        echo "✅ Usuario creado:\n";
        echo "   Personal: {$personal->nombre_completo}\n";
        echo "   Email: {$usuario->email}\n";
        echo "   Rol: {$primerRol->nombre_rol}\n\n";
    }
    
    // 4. Probar la carga de datos para el formulario
    $personalPrueba = Personal::with(['usuario.rol'])->whereHas('usuario')->first();
    
    if ($personalPrueba) {
        echo "🔍 Datos para el formulario (Personal ID: {$personalPrueba->id}):\n";
        echo "   Nombre: {$personalPrueba->nombre_completo}\n";
        echo "   Email usuario: " . ($personalPrueba->usuario?->email ?? 'No tiene usuario') . "\n";
        echo "   Rol ID actual: " . ($personalPrueba->usuario?->rol_id ?? 'No asignado') . "\n";
        echo "   Rol nombre: " . ($personalPrueba->usuario?->rol?->nombre_rol ?? 'No asignado') . "\n\n";
        
        echo "✅ Los datos están listos para el formulario de edición\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 Archivo: " . $e->getFile() . " línea " . $e->getLine() . "\n";
}
