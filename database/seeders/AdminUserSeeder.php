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
        // Crear solo el personal y usuario administrador
        if (!User::where('email', 'admin@petrotekno.com')->exists()) {
            // Buscar la categoría de Administrador
            $categoriaAdmin = CategoriaPersonal::where('nombre_categoria', 'Administrador')->first();
            
            if (!$categoriaAdmin) {
                // Si no existe, crear la categoría
                $categoriaAdmin = CategoriaPersonal::create([
                    'nombre_categoria' => 'Administrador',
                    'descripcion' => 'Administrador del sistema con acceso completo'
                ]);
            }

            // Crear personal administrador (solo con campos que existen en la tabla)
            $personal = Personal::create([
                'nombre_completo' => 'Administrador Sistema',
                'estatus' => 'activo',
                'categoria_id' => $categoriaAdmin->id,
            ]);

            // Buscar el rol de Admin
            $adminRole = Role::where('nombre_rol', 'Admin')->first();
            
            if (!$adminRole) {
                throw new \Exception('El rol Admin no existe. Asegúrate de ejecutar RoleSeeder primero.');
            }

            // Crear usuario administrador
            $adminUser = User::create([
                'email' => 'admin@petrotekno.com',
                'password' => Hash::make('password'),
                'rol_id' => $adminRole->id,
                'personal_id' => $personal->id,
            ]);

            $this->command->info('✅ Usuario administrador creado exitosamente:');
            $this->command->info('   Email: admin@petrotekno.com');
            $this->command->info('   Password: password');
            $this->command->info('   Personal: ' . $personal->nombre_completo);
        } else {
            $this->command->info('ℹ️ Usuario administrador ya existe, omitiendo creación.');
        }
    }
}
