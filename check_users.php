<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Usuarios en la base de datos:\n";
foreach (App\Models\User::all() as $user) {
    echo $user->email . ' - ' . $user->name . "\n";
}
