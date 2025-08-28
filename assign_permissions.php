<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

$user = User::where('email', 'admin@petrotekno.com')->first();

if (!$user) {
    echo "Usuario no encontrado\n";
    exit(1);
}

// Crear o encontrar el rol de administrador
$adminRole = Role::firstOrCreate(
    ['nombre_rol' => 'Administrador'],
    ['descripcion' => 'Administrador del sistema']
);

// Asignar el rol al usuario
$user->rol_id = $adminRole->id;
$user->save();

echo "Rol de administrador asignado al usuario admin\n";

// Crear los permisos necesarios
$permissions = ['ver_catalogos', 'crear_catalogos', 'editar_catalogos', 'eliminar_catalogos'];

foreach ($permissions as $permName) {
    $permission = Permission::firstOrCreate(['nombre_permiso' => $permName]);
    
    // Verificar si el rol ya tiene el permiso
    if (!$adminRole->permisos()->where('nombre_permiso', $permName)->exists()) {
        $adminRole->permisos()->attach($permission->id);
        echo "Permiso '{$permName}' asignado al rol\n";
    } else {
        echo "Permiso '{$permName}' ya existe en el rol\n";
    }
}

echo "Permisos procesados para el rol de administrador\n";