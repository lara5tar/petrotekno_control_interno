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
            // Eliminar las columnas de fotos múltiples existentes
            $table->dropColumn([
                'foto_frontal',
                'foto_lateral',
                'foto_trasera',
                'foto_interior'
            ]);
            
            // Agregar una sola columna para la foto general del vehículo
            $table->string('imagen', 500)->nullable()->after('observaciones')->comment('URL de la imagen general del vehículo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehiculos', function (Blueprint $table) {
            // Eliminar la columna de imagen general
            $table->dropColumn('imagen');
            
            // Restaurar las columnas de fotos múltiples
            $table->string('foto_frontal', 500)->nullable()->comment('Ruta de la foto frontal del vehículo');
            $table->string('foto_lateral', 500)->nullable()->comment('Ruta de la foto lateral del vehículo');
            $table->string('foto_trasera', 500)->nullable()->comment('Ruta de la foto trasera del vehículo');
            $table->string('foto_interior', 500)->nullable()->comment('Ruta de la foto del interior del vehículo');
        });
    }
};
