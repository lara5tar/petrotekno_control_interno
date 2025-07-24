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
        Schema::create('configuracion_alertas', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_config', ['general', 'horarios', 'destinatarios'])
                ->comment('Tipo de configuración');
            $table->string('clave', 100)
                ->comment('Clave de configuración');
            $table->text('valor')
                ->comment('Valor de configuración (puede ser JSON)');
            $table->text('descripcion')->nullable()
                ->comment('Descripción de la configuración');
            $table->boolean('activo')->default(true)
                ->comment('Si la configuración está activa');
            $table->timestamps();

            // Índices
            $table->unique(['tipo_config', 'clave'], 'unique_config');
            $table->index(['tipo_config', 'activo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracion_alertas');
    }
};
