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
        // Verificar si las columnas existen antes de eliminarlas
        if (DB::getDriverName() === 'sqlite') {
            // Para SQLite, usar Schema::hasColumn
            $combustibleColumns = [
                'combustible_inicial',
                'combustible_final', 
                'combustible_suministrado',
                'costo_combustible',
                'historial_combustible'
            ];
            
            Schema::table('asignaciones_obra', function (Blueprint $table) use ($combustibleColumns) {
                foreach ($combustibleColumns as $column) {
                    if (Schema::hasColumn('asignaciones_obra', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        } else {
            // Para MySQL, usar el método original
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            
            // Eliminar todas las columnas relacionadas con combustible
            $columns = collect(DB::select("SHOW COLUMNS FROM asignaciones_obra"))->pluck('Field');
            
            $combustibleColumns = [
                'combustible_inicial',
                'combustible_final', 
                'combustible_suministrado',
                'costo_combustible',
                'historial_combustible'
            ];
            
            foreach ($combustibleColumns as $column) {
                if ($columns->contains($column)) {
                    DB::statement("ALTER TABLE asignaciones_obra DROP COLUMN {$column}");
                }
            }
            
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asignaciones_obra', function (Blueprint $table) {
            // Restaurar campos de combustible
            $table->decimal('combustible_inicial', 8, 2)->nullable();
            $table->decimal('combustible_final', 8, 2)->nullable();
            $table->decimal('combustible_suministrado', 8, 2)->nullable()->default(0);
            $table->decimal('costo_combustible', 10, 2)->nullable()->default(0);
            $table->json('historial_combustible')->nullable();
        });
    }
};
