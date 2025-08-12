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
        Schema::table('asignaciones_obra', function (Blueprint $table) {
            // Hacer que operador_id sea nullable
            $table->unsignedBigInteger('operador_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asignaciones_obra', function (Blueprint $table) {
            // Revertir operador_id a no nullable (pero esto podría fallar si hay registros con NULL)
            $table->unsignedBigInteger('operador_id')->nullable(false)->change();
        });
    }
};
