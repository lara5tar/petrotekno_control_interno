<?php

use App\Models\User;

// Verificar si el usuario de test existe
$user = User::where('email', 'test@petrotekno.com')->first();

if (!$user) {
    $user = new User();
    $user->email = 'test@petrotekno.com';
    $user->password = bcrypt('test123');
    $user->rol_id = 1; // Admin role
    $user->save();
    echo "Usuario de test creado: test@petrotekno.com / test123\n";
} else {
    echo "Usuario de test ya existe\n";
}

echo "Email: " . $user->email . "\n";
echo "Rol ID: " . $user->rol_id . "\n";
