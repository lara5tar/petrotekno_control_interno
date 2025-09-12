<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Role;
use App\Models\Personal;
use Illuminate\Support\Facades\Hash;

$role = Role::where('nombre_rol', 'Admin')->first();
$personal = Personal::first();

if ($role && $personal) {
    $user = User::create([
        'email' => 'admin2@petrotekno.com',
        'password' => Hash::make('admin123'),
        'rol_id' => $role->id,
        'personal_id' => $personal->id
    ]);
    
    echo "Usuario creado exitosamente: {$user->email}\n";
    echo "Rol: {$role->nombre_rol}\n";
    echo "Personal ID: {$personal->id}\n";
} else {
    echo "No se encontró el rol Admin o el personal\n";
}