<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;

// Find the admin user
$user = User::where('email', 'admin@petrotekno.com')->first();

if (!$user) {
    echo "Admin user not found\n";
    exit(1);
}

echo "User found: {$user->email}\n";
echo "User role: " . ($user->rol ? $user->rol->nombre_rol : 'No role') . "\n";

// Find or create admin_sistema permission
$permission = Permission::where('nombre_permiso', 'admin_sistema')->first();

if (!$permission) {
    echo "Creating admin_sistema permission...\n";
    $permission = Permission::create([
        'nombre_permiso' => 'admin_sistema',
        'descripcion' => 'Administrador del sistema'
    ]);
    echo "Permission created with ID: {$permission->id}\n";
} else {
    echo "Permission found with ID: {$permission->id}\n";
}

// Attach permission to user's role
if ($user->rol) {
    $user->rol->permisos()->syncWithoutDetaching([$permission->id]);
    echo "Permission granted to role: {$user->rol->nombre_rol}\n";
    
    // Verify the permission was granted
    $hasPermission = $user->hasPermission('admin_sistema');
    echo "User now has admin_sistema permission: " . ($hasPermission ? 'YES' : 'NO') . "\n";
} else {
    echo "User has no role assigned\n";
}