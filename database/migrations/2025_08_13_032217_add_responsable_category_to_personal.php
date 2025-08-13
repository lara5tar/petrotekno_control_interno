<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Verificar si la categoría "responsable" ya existe
        $exists = DB::table('categorias_personal')
            ->where('nombre_categoria', 'Responsable')
            ->exists();
            
        if (!$exists) {
            // Agregar la categoría "responsable" a la tabla categorias_personal
            DB::table('categorias_personal')->insert([
                'nombre_categoria' => 'Responsable',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Registrar en el log la creación de la nueva categoría
            DB::table('log_acciones')->insert([
                'usuario_id' => 1, // Usuario administrador o sistema
                'accion' => 'crear_categoria_personal',
                'tabla_afectada' => 'categorias_personal',
                'detalles' => 'Se agregó la categoría "Responsable" de personal por migración',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar la categoría "responsable" si existe
        DB::table('categorias_personal')
            ->where('nombre_categoria', 'Responsable')
            ->delete();
    }
};
