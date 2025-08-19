<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersonalAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('👤 Configurando registro de personal para el administrador...');

        // Crear categorías de personal si no existen
        $categorias = [
            ['nombre_categoria' => 'Administrador'],
            ['nombre_categoria' => 'Operador'],
            ['nombre_categoria' => 'Supervisor'],
            ['nombre_categoria' => 'Gerente'],
            ['nombre_categoria' => 'Técnico'],
        ];

        $categoriasCreadas = 0;
        foreach ($categorias as $categoria) {
            $existe = DB::table('categorias_personal')->where('nombre_categoria', $categoria['nombre_categoria'])->exists();
            if (!$existe) {
                DB::table('categorias_personal')->insert([
                    'nombre_categoria' => $categoria['nombre_categoria'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $categoriasCreadas++;
            }
        }

        $this->command->info("✅ Categorías de personal creadas: {$categoriasCreadas}");

        // Obtener ID de la categoría Administrador
        $categoriaAdmin = DB::table('categorias_personal')->where('nombre_categoria', 'Administrador')->first();
        
        if (!$categoriaAdmin) {
            $this->command->error('❌ No se pudo crear la categoría Administrador');
            return;
        }

        // Crear registro de personal para el administrador
        $personalExiste = DB::table('personal')
            ->where('nombre_completo', 'Administrador Sistema')
            ->exists();

        if (!$personalExiste) {
            $personalId = DB::table('personal')->insertGetId([
                'nombre_completo' => 'Administrador Sistema',
                'estatus' => 'activo',
                'categoria_id' => $categoriaAdmin->id,
                'curp_numero' => 'ADMIN800101H01', // CURP más corto
                'rfc' => 'ADMIN801XXX',
                'nss' => '9999999999',
                'direccion' => 'Oficinas Centrales',
                'ine' => 'ADMIN001',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info("✅ Registro de personal creado con ID: {$personalId}");
        } else {
            $personal = DB::table('personal')->where('nombre_completo', 'Administrador Sistema')->first();
            $personalId = $personal->id;
            $this->command->info("✅ Registro de personal ya existe con ID: {$personalId}");
        }

        // Actualizar usuario admin para asociarlo con el personal
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        
        if ($admin) {
            $admin->update([
                'personal_id' => $personalId,
            ]);
            
            $this->command->info("✅ Usuario admin actualizado y asociado con personal ID: {$personalId}");
        } else {
            // Crear usuario si no existe
            $admin = User::create([
                'email' => 'admin@petrotekno.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'personal_id' => $personalId,
                'rol_id' => 1, // Rol de Administrador
            ]);
            
            $this->command->info("✅ Usuario admin creado y asociado con personal ID: {$personalId}");
        }

        $this->command->info("🎯 CONFIGURACIÓN PERSONAL COMPLETA:");
        $this->command->info("👤 Personal: Administrador Sistema");
        $this->command->info("🏷️ Categoría: Administrador");
        $this->command->info("📧 Email: admin@petrotekno.com");
        $this->command->info("🔑 Password: password");
        $this->command->info("🔗 Usuario asociado correctamente con registro de personal");
        $this->command->info("✨ El administrador ya puede acceder a todas las funciones del sistema");
    }
}
