<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Verificar si ya existe un usuario
$existingUsers = User::count();
echo "Usuarios existentes: $existingUsers\n";

if ($existingUsers === 0) {
    // Crear usuario de prueba
    $user = User::create([
        'name' => 'Admin Petrotekno',
        'email' => 'admin@petrotekno.com',
        'password' => Hash::make('password123'),
        'email_verified_at' => now()
    ]);
    
    echo "✅ Usuario de prueba creado:\n";
    echo "Email: admin@petrotekno.com\n";
    echo "Password: password123\n";
} else {
    // Mostrar usuarios existentes
    $users = User::select('id', 'name', 'email')->get();
    echo "✅ Usuarios disponibles:\n";
    foreach ($users as $user) {
        echo "ID: {$user->id}, Email: {$user->email}, Nombre: {$user->name}\n";
    }
}
