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
        // Eliminar cualquier constraint único problemático que pueda existir
        try {
            Schema::table('vehiculos', function (Blueprint $table) {
                // Verificar si existen los constraints antes de eliminarlos
                $indexes = DB::select("SHOW INDEX FROM vehiculos WHERE Key_name IN ('vehiculos_n_serie_unique', 'vehiculos_placas_unique')");
                
                $hasNSerieUnique = collect($indexes)->contains('Key_name', 'vehiculos_n_serie_unique');
                $hasPlacasUnique = collect($indexes)->contains('Key_name', 'vehiculos_placas_unique');
                
                if ($hasNSerieUnique) {
                    $table->dropUnique(['n_serie']);
                }
                
                if ($hasPlacasUnique) {
                    $table->dropUnique(['placas']);
                }
            });
        } catch (\Exception $e) {
            // Si no existen los constraints, continúa sin problemas
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // En el rollback, no intentamos restaurar los constraints únicos
        // ya que pueden causar problemas con datos existentes
        // Los constraints se pueden agregar manualmente si es necesario
    }
};