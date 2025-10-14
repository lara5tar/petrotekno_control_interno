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
        Schema::table('tipo_activos', function (Blueprint $table) {
            $table->boolean('tiene_placa')->default(true)->after('tiene_kilometraje');
            $table->boolean('tiene_numero_serie')->default(true)->after('tiene_placa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipo_activos', function (Blueprint $table) {
            $table->dropColumn(['tiene_placa', 'tiene_numero_serie']);
        });
    }
};
