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
        Schema::table('obras', function (Blueprint $table) {
            // Campos para archivos importantes de la obra
            $table->string('archivo_contrato')->nullable()->after('observaciones')->comment('Ruta del archivo de contrato');
            $table->string('archivo_fianza')->nullable()->after('archivo_contrato')->comment('Ruta del archivo de fianza');
            $table->string('archivo_acta_entrega_recepcion')->nullable()->after('archivo_fianza')->comment('Ruta del archivo de acta entrega-recepción');
            
            // Fechas de subida de archivos para control
            $table->timestamp('fecha_subida_contrato')->nullable()->after('archivo_acta_entrega_recepcion');
            $table->timestamp('fecha_subida_fianza')->nullable()->after('fecha_subida_contrato');
            $table->timestamp('fecha_subida_acta')->nullable()->after('fecha_subida_fianza');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obras', function (Blueprint $table) {
            $table->dropColumn([
                'archivo_contrato',
                'archivo_fianza', 
                'archivo_acta_entrega_recepcion',
                'fecha_subida_contrato',
                'fecha_subida_fianza',
                'fecha_subida_acta'
            ]);
        });
    }
};
