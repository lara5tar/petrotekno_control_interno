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
            $table->string('foto_frontal', 500)->nullable()->comment('Ruta de la foto frontal del vehículo');
            $table->string('foto_lateral', 500)->nullable()->comment('Ruta de la foto lateral del vehículo');
            $table->string('foto_trasera', 500)->nullable()->comment('Ruta de la foto trasera del vehículo');
            $table->string('foto_interior', 500)->nullable()->comment('Ruta de la foto del interior del vehículo');
            $table->json('documentos_adicionales')->nullable()->comment('Array JSON con rutas de documentos adicionales');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehiculos', function (Blueprint $table) {
            $table->dropColumn([
                'foto_frontal',
                'foto_lateral', 
                'foto_trasera',
                'foto_interior',
                'documentos_adicionales'
            ]);
        });
    }
};
