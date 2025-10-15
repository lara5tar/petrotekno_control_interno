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
            $table->year('anio')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Primero actualizar los valores NULL a un año por defecto
        DB::table('vehiculos')
            ->whereNull('anio')
            ->update(['anio' => 2000]);
            
        Schema::table('vehiculos', function (Blueprint $table) {
            $table->year('anio')->nullable(false)->change();
        });
    }
};
