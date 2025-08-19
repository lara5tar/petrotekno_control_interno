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
            $table->string('poliza_url')->nullable()->comment('URL o ruta del archivo de la póliza de seguro');
            $table->date('poliza_vencimiento')->nullable()->comment('Fecha de vencimiento de la póliza de seguro');
            $table->string('factura_url')->nullable()->comment('URL o ruta del archivo de la factura del vehículo');
            $table->string('derecho_url')->nullable()->comment('URL o ruta del archivo de derecho vehicular');
            $table->date('derecho_vencimiento')->nullable()->comment('Fecha de vencimiento del derecho vehicular');
            $table->string('url_imagen')->nullable()->comment('URL o ruta de la imagen del vehículo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehiculos', function (Blueprint $table) {
            $table->dropColumn([
                'poliza_url',
                'poliza_vencimiento',
                'factura_url',
                'derecho_url',
                'derecho_vencimiento',
                'url_imagen'
            ]);
        });
    }
};
