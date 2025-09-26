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
        // Primero, limpiar registros con placas duplicadas vacías
        // Mantener solo el primer registro con placas vacías y eliminar los duplicados
        $allEmptyPlates = DB::table('vehiculos')
            ->select('id')
            ->where('placas', '')
            ->orderBy('id')
            ->get();
        
        if ($allEmptyPlates->count() > 1) {
            $duplicateIds = $allEmptyPlates->skip(1)->pluck('id');
            DB::table('vehiculos')->whereIn('id', $duplicateIds)->delete();
        }

        Schema::table('vehiculos', function (Blueprint $table) {
            // Eliminar constraint de unicidad de n_serie
            $table->dropUnique(['n_serie']);
            
            // Eliminar constraint de unicidad de placas
            $table->dropUnique(['placas']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Primero, limpiar registros con placas duplicadas vacías antes de restaurar restricciones
        $allEmptyPlates = DB::table('vehiculos')
            ->select('id')
            ->where('placas', '')
            ->orderBy('id')
            ->get();
        
        if ($allEmptyPlates->count() > 1) {
            $duplicateIds = $allEmptyPlates->skip(1)->pluck('id');
            DB::table('vehiculos')->whereIn('id', $duplicateIds)->delete();
        }

        Schema::table('vehiculos', function (Blueprint $table) {
            // Verificar si las restricciones únicas ya existen antes de crearlas
            $indexes = DB::select("SHOW INDEX FROM vehiculos WHERE Key_name IN ('vehiculos_n_serie_unique', 'vehiculos_placas_unique')");
            
            $hasNSerieUnique = collect($indexes)->contains('Key_name', 'vehiculos_n_serie_unique');
            $hasPlacasUnique = collect($indexes)->contains('Key_name', 'vehiculos_placas_unique');
            
            // Restaurar constraint de unicidad de n_serie solo si no existe
            if (!$hasNSerieUnique) {
                $table->unique('n_serie');
            }
            
            // Restaurar constraint de unicidad de placas solo si no existe
            if (!$hasPlacasUnique) {
                $table->unique('placas');
            }
        });
    }
};