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
            $table->integer('kilometraje_actual')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Primero actualizar los valores nulos a 0
        DB::table('vehiculos')->whereNull('kilometraje_actual')->update(['kilometraje_actual' => 0]);
        
        // Luego cambiar la columna a no nula con valor predeterminado 0
        Schema::table('vehiculos', function (Blueprint $table) {
            $table->integer('kilometraje_actual')->default(0)->change();
        });
    }
};
