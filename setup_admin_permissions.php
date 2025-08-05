<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

echo "=== CONFIGURACIÓN DE ROLES Y PERMISOS ===\n\n";

try {
    // 1. Crear permisos básicos
    echo "1. Creando permisos básicos...\n";
    
    $permisos = [
        // Permisos de vehículos
        'ver_vehiculos',
        'crear_vehiculos',
        'editar_vehiculos',
        'eliminar_vehiculos',
        
        // Permisos de personal
        'ver_personal',
        'crear_personal',
        'editar_personal',
        'eliminar_personal',
        
        // Permisos de obras
        'ver_obras',
        'crear_obras',
        'actualizar_obras',
        'eliminar_obras',
        'restaurar_obras',
        
        // Permisos de asignaciones
        'ver_asignaciones',
        'crear_asignaciones',
        'editar_asignaciones',
        'liberar_asignaciones',
        'eliminar_asignaciones',
        
        // Permisos de documentos
        'ver_documentos',
        'crear_documentos',
        'editar_documentos',
        'eliminar_documentos',
        
        // Permisos administrativos
        'administrar_sistema',
        'ver_reportes',
        'gestionar_usuarios'
    ];
    
    foreach ($permisos as $nombrePermiso) {
        $permiso = Permission::firstOrCreate([
            'nombre_permiso' => $nombrePermiso
        ], [
            'descripcion' => "Permiso para $nombrePermiso"
        ]);
        echo "   ✅ Permiso: $nombrePermiso\n";
    }
    
    echo "\n2. Creando rol de Administrador...\n";
    
    // 2. Crear rol de administrador
    $adminRole = Role::firstOrCreate([
        'nombre_rol' => 'Administrador'
    ], [
        'descripcion' => 'Administrador del sistema con acceso completo'
    ]);
    
    echo "   ✅ Rol creado: {$adminRole->nombre_rol} (ID: {$adminRole->id})\n";
    
    // 3. Asignar todos los permisos al rol de administrador
    echo "\n3. Asignando permisos al rol de administrador...\n";
    
    $todosLosPermisos = Permission::all();
    $adminRole->permisos()->sync($todosLosPermisos->pluck('id'));
    
    echo "   ✅ {$todosLosPermisos->count()} permisos asignados al rol de administrador\n";
    
    // 4. Asignar rol de administrador al usuario admin
    echo "\n4. Asignando rol al usuario admin...\n";
    
    $adminUser = User::where('email', 'admin@petrotekno.com')->first();
    if ($adminUser) {
        $adminUser->update(['rol_id' => $adminRole->id]);
        echo "   ✅ Rol de administrador asignado al usuario admin\n";
        
        // 5. Verificar que el email esté verificado
        if (!$adminUser->email_verified_at) {
            $adminUser->update(['email_verified_at' => now()]);
            echo "   ✅ Email del usuario admin verificado\n";
        }
        
    } else {
        echo "   ❌ Usuario admin no encontrado\n";
    }
    
    echo "\n5. Verificación final...\n";
    
    // 6. Verificar que todo funcione
    $adminUser->load('rol.permisos');
    echo "   Usuario: {$adminUser->email}\n";
    echo "   Rol: {$adminUser->rol->nombre_rol}\n";
    echo "   Permisos: {$adminUser->rol->permisos->count()}\n";
    
    // Verificar algunos permisos específicos
    $permisosAVerificar = ['ver_vehiculos', 'crear_vehiculos', 'administrar_sistema'];
    foreach ($permisosAVerificar as $permiso) {
        $tiene = $adminUser->hasPermission($permiso);
        echo "   Permiso '$permiso': " . ($tiene ? '✅ SÍ' : '❌ NO') . "\n";
    }
    
    echo "\n✅ CONFIGURACIÓN COMPLETADA EXITOSAMENTE\n";
    echo "\nEl usuario admin@petrotekno.com ahora tiene:\n";
    echo "- Rol de Administrador asignado\n";
    echo "- Todos los permisos del sistema\n";
    echo "- Email verificado\n";
    echo "- Acceso completo a todas las funcionalidades\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
}

echo "\n=== FIN DE CONFIGURACIÓN ===\n";
