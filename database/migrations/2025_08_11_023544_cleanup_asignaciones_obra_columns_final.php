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
        // Usar SQL directo para eliminar las columnas sin importar las claves foráneas
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Eliminar columnas si existen
        $columns = collect(DB::select("SHOW COLUMNS FROM asignaciones_obra"))->pluck('Field');
        
        if ($columns->contains('encargado_id')) {
            DB::statement('ALTER TABLE asignaciones_obra DROP COLUMN encargado_id');
        }
        
        if ($columns->contains('operador_id')) {
            DB::statement('ALTER TABLE asignaciones_obra DROP COLUMN operador_id');
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asignaciones_obra', function (Blueprint $table) {
            // Restaurar las columnas
            $table->unsignedBigInteger('operador_id')->nullable()->after('vehiculo_id');
            $table->unsignedBigInteger('encargado_id')->nullable()->after('operador_id');
            
            // Restaurar claves foráneas
            $table->foreign('operador_id')->references('id')->on('personal')->onDelete('set null');
            $table->foreign('encargado_id')->references('id')->on('users')->onDelete('set null');
        });
    }
};
