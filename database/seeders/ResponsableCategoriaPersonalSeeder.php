<?php

namespace Database\Seeders;

use App\Models\CategoriaPersonal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResponsableCategoriaPersonalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si la categoría "responsable" ya existe
        $exists = CategoriaPersonal::where('nombre_categoria', 'Responsable')->exists();
            
        if (!$exists) {
            // Agregar la categoría "responsable" a la tabla categorias_personal
            CategoriaPersonal::create([
                'nombre_categoria' => 'Responsable',
            ]);
            
            $this->command->info('Categoría "Responsable" agregada exitosamente.');
            
            // Registrar en el log la creación de la nueva categoría
            DB::table('log_acciones')->insert([
                'usuario_id' => 1, // Usuario administrador o sistema
                'accion' => 'crear_categoria_personal',
                'tabla_afectada' => 'categorias_personal',
                'detalles' => 'Se agregó la categoría "Responsable" de personal por seeder',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $this->command->info('La categoría "Responsable" ya existe en la base de datos.');
        }
    }
}
