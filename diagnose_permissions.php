<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Role;

echo "=== DIAGNÓSTICO COMPLETO DE PERMISOS ===\n\n";

// 1. Verificar roles disponibles
echo "1. ROLES DISPONIBLES:\n";
$roles = Role::all();
if ($roles->count() > 0) {
    foreach ($roles as $role) {
        echo "   - ID: {$role->id}, Nombre: {$role->nombre_rol}\n";
    }
} else {
    echo "   ❌ No hay roles en el sistema\n";
}

echo "\n";

// 2. Verificar usuario admin
echo "2. USUARIO ADMIN:\n";
$adminUser = User::where('email', 'admin@petrotekno.com')->first();

if ($adminUser) {
    echo "   ✅ Usuario encontrado:\n";
    echo "      ID: {$adminUser->id}\n";
    echo "      Email: {$adminUser->email}\n";
    echo "      Rol ID: " . ($adminUser->rol_id ?? 'NULL') . "\n";
    
    if ($adminUser->rol_id) {
        $rol = Role::find($adminUser->rol_id);
        if ($rol) {
            echo "      Rol: {$rol->nombre_rol}\n";
            
            // Verificar permisos del rol
            if (method_exists($rol, 'permisos')) {
                $permisos = $rol->permisos;
                echo "      Permisos: " . $permisos->count() . " permisos\n";
                if ($permisos->count() > 0) {
                    foreach ($permisos->take(5) as $permiso) {
                        echo "         - {$permiso->nombre_permiso}\n";
                    }
                    if ($permisos->count() > 5) {
                        echo "         - ... y " . ($permisos->count() - 5) . " más\n";
                    }
                }
            } else {
                echo "      ❌ El rol no tiene relación con permisos\n";
            }
        } else {
            echo "      ❌ Rol no encontrado en la BD\n";
        }
    } else {
        echo "      ❌ Usuario sin rol asignado\n";
    }
} else {
    echo "   ❌ Usuario admin no encontrado\n";
}

echo "\n";

// 3. Verificar si existe rol de administrador
echo "3. ROL DE ADMINISTRADOR:\n";
$adminRole = Role::where('nombre_rol', 'LIKE', '%admin%')->orWhere('nombre_rol', 'LIKE', '%Administrador%')->first();
if ($adminRole) {
    echo "   ✅ Rol admin encontrado: {$adminRole->nombre_rol} (ID: {$adminRole->id})\n";
} else {
    echo "   ❌ No hay rol de administrador\n";
}

echo "\n=== FIN DEL DIAGNÓSTICO ===\n";
