<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaPersonalSeeder extends Seeder
{
    /**
     * Crear las categorías básicas del personal del sistema.
     * - Admin: Administrador del sistema con acceso completo
     * - Operador: Personal operativo con permisos limitados
     * - Responsable de obra: Encargado de supervisión de obras
     */
    public function run(): void
    {
        $this->command->info('🏷️ Creando categorías del personal...');

        // Verificar si ya existen categorías para evitar duplicados
        $existingCategories = DB::table('categorias_personal')->count();
        
        if ($existingCategories > 0) {
            $this->command->warn('⚠️ Las categorías del personal ya existen, omitiendo...');
            return;
        }

        $categorias = [
            [
                'nombre_categoria' => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_categoria' => 'Operador',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_categoria' => 'Responsable de obra',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('categorias_personal')->insert($categorias);

        $this->command->info('✅ Categorías del personal creadas exitosamente:');
        $this->command->info('   🔸 Admin: Acceso completo al sistema');
        $this->command->info('   🔸 Responsable de obra: Supervisión de obras');
        $this->command->info('   🔸 Operador: Operaciones básicas');
    }
}
