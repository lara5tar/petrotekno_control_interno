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
            // Eliminar las múltiples fotos
            $table->dropColumn([
                'foto_frontal',
                'foto_lateral', 
                'foto_trasera',
                'foto_interior'
            ]);
            
            // Agregar solo una foto del vehículo
            $table->string('fotografia_vehiculo', 500)->nullable()->comment('Ruta de la fotografía del vehículo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehiculos', function (Blueprint $table) {
            // Restaurar las múltiples fotos
            $table->string('foto_frontal', 500)->nullable()->comment('Ruta de la foto frontal del vehículo');
            $table->string('foto_lateral', 500)->nullable()->comment('Ruta de la foto lateral del vehículo');
            $table->string('foto_trasera', 500)->nullable()->comment('Ruta de la foto trasera del vehículo');
            $table->string('foto_interior', 500)->nullable()->comment('Ruta de la foto del interior del vehículo');
            
            // Eliminar la foto única
            $table->dropColumn('fotografia_vehiculo');
        });
    }
};