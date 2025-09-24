<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
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
        Schema::table('vehiculos', function (Blueprint $table) {
            // Restaurar constraint de unicidad de n_serie
            $table->unique('n_serie');
            
            // Restaurar constraint de unicidad de placas
            $table->unique('placas');
        });
    }
};