<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "=== VERIFICACIÓN DEL USUARIO ADMIN ===\n\n";

// Buscar usuario admin
$adminUser = User::where('email', 'admin@petrotekno.com')->first();

if ($adminUser) {
    echo "✅ Usuario admin encontrado:\n";
    echo "   ID: " . $adminUser->id . "\n";
    echo "   Email: " . $adminUser->email . "\n";
    echo "   Username: " . $adminUser->username . "\n";
    echo "   Email Verified: " . ($adminUser->email_verified_at ? 'Sí' : 'No') . "\n";
    echo "   Created: " . $adminUser->created_at . "\n";
    echo "   Updated: " . $adminUser->updated_at . "\n\n";
    
    // Verificar si tiene roles/permisos
    if (method_exists($adminUser, 'roles')) {
        echo "Roles del usuario:\n";
        foreach ($adminUser->roles as $role) {
            echo "   - " . $role->name . "\n";
        }
    } else {
        echo "⚠️  No hay sistema de roles implementado\n";
    }
    
    if (method_exists($adminUser, 'permissions')) {
        echo "\nPermisos del usuario:\n";
        foreach ($adminUser->permissions as $permission) {
            echo "   - " . $permission->name . "\n";
        }
    } else {
        echo "⚠️  No hay sistema de permisos implementado\n";
    }
    
} else {
    echo "❌ Usuario admin NO encontrado\n";
    echo "Buscando otros usuarios...\n\n";
    
    $users = User::all();
    foreach ($users as $user) {
        echo "Usuario: " . $user->email . " (ID: " . $user->id . ")\n";
    }
}

echo "\n=== FIN DE VERIFICACIÓN ===\n";
