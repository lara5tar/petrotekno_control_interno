<?php

namespace Database\Seeders;

use App\Models\CategoriaPersonal;
use App\Models\Personal;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear personal administrador
        $categoriaAdmin = CategoriaPersonal::where('nombre_categoria', 'Administrador')->first();
        $personal = Personal::create([
            'nombre_completo' => 'Administrador del Sistema',
            'estatus' => 'activo',
            'categoria_id' => $categoriaAdmin->id,
        ]);

        // Crear usuario administrador
        $adminRole = Role::where('nombre_rol', 'Admin')->first();
        User::create([
            'nombre_usuario' => 'admin',
            'email' => 'admin@petrotekno.com',
            'password' => Hash::make('password123'),
            'rol_id' => $adminRole->id,
            'personal_id' => $personal->id,
        ]);

        // Crear usuario supervisor de ejemplo
        $categoriaSuper = CategoriaPersonal::where('nombre_categoria', 'Supervisor')->first();
        $personalSuper = Personal::create([
            'nombre_completo' => 'Juan Pérez Supervisor',
            'estatus' => 'activo',
            'categoria_id' => $categoriaSuper->id,
        ]);

        $supervisorRole = Role::where('nombre_rol', 'Supervisor')->first();
        User::create([
            'nombre_usuario' => 'supervisor',
            'email' => 'supervisor@petrotekno.com',
            'password' => Hash::make('password123'),
            'rol_id' => $supervisorRole->id,
            'personal_id' => $personalSuper->id,
        ]);

        // Crear usuario operador de ejemplo
        $categoriaOper = CategoriaPersonal::where('nombre_categoria', 'Operador')->first();
        $personalOper = Personal::create([
            'nombre_completo' => 'Carlos García Operador',
            'estatus' => 'activo',
            'categoria_id' => $categoriaOper->id,
        ]);

        $operadorRole = Role::where('nombre_rol', 'Operador')->first();
        User::create([
            'nombre_usuario' => 'operador',
            'email' => 'operador@petrotekno.com',
            'password' => Hash::make('password123'),
            'rol_id' => $operadorRole->id,
            'personal_id' => $personalOper->id,
        ]);
    }
}
