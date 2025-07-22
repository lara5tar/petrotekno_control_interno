<?php

use App\Models\CategoriaPersonal;
use App\Models\Permission;
use App\Models\Personal;
use App\Models\Role;
use App\Models\User;

require_once 'bootstrap/app.php';

// Crear permisos necesarios
$permisos = [
    'ver_kilometrajes',
    'crear_kilometrajes',
    'editar_kilometrajes',
    'eliminar_kilometrajes',
];

foreach ($permisos as $permiso) {
    Permission::firstOrCreate(['nombre_permiso' => $permiso]);
}

// Crear rol con todos los permisos
$role = Role::firstOrCreate(['nombre_rol' => 'Test Role']);
$role->permisos()->sync(Permission::whereIn('nombre_permiso', $permisos)->pluck('id'));

// Crear categoría de personal y personal
$categoria = CategoriaPersonal::factory()->create();
$personal = Personal::factory()->create(['categoria_id' => $categoria->id]);

// Crear usuario autenticado con rol y permisos
$user = User::factory()->create([
    'personal_id' => $personal->id,
    'rol_id' => $role->id,
]);

// Test the permissions
echo "Rol ID: " . $user->rol_id . "\n";
echo "Rol: " . $user->rol->nombre_rol . "\n";
echo "Permisos del rol: " . $user->rol->permisos->pluck('nombre_permiso')->implode(', ') . "\n";
echo "¿Tiene permiso 'ver_kilometrajes'? " . ($user->hasPermission('ver_kilometrajes') ? 'SÍ' : 'NO') . "\n";
