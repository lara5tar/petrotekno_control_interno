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
        Schema::table('vehiculos', function (Blueprint $table) {
            // Hacer nullable las columnas placas y n_serie para permitir tipos de activo sin estos campos
            $table->string('placas', 20)->nullable()->change();
            $table->string('n_serie', 100)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Primero actualizar los valores NULL a valores por defecto
        DB::table('vehiculos')
            ->whereNull('placas')
            ->update(['placas' => 'SIN-PLACAS']);
            
        DB::table('vehiculos')
            ->whereNull('n_serie')
            ->update(['n_serie' => 'SIN-SERIE']);
            
        Schema::table('vehiculos', function (Blueprint $table) {
            // Revertir cambios - hacer NOT NULL nuevamente
            $table->string('placas', 20)->nullable(false)->change();
            $table->string('n_serie', 100)->nullable(false)->change();
        });
    }
};
