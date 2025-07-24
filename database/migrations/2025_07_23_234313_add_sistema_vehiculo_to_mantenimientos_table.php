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
        Schema::table('mantenimientos', function (Blueprint $table) {
            $table->enum('sistema_vehiculo', ['motor', 'transmision', 'hidraulico', 'general'])
                ->default('general')
                ->after('tipo_servicio')
                ->comment('Sistema del vehículo al que corresponde el mantenimiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mantenimientos', function (Blueprint $table) {
            $table->dropColumn('sistema_vehiculo');
        });
    }
};
