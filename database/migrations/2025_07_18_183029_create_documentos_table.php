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
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_documento_id')->constrained('catalogo_tipos_documento')->onDelete('restrict');
            $table->text('descripcion')->nullable();
            $table->string('ruta_archivo', 500)->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->foreignId('vehiculo_id')->nullable()->constrained('vehiculos')->onDelete('cascade');
            $table->foreignId('personal_id')->nullable()->constrained('personal')->onDelete('cascade');
            $table->foreignId('obra_id')->nullable()->constrained('obras')->onDelete('cascade');
            // TODO: Agregar foreign key cuando se implemente la tabla mantenimientos
            $table->unsignedBigInteger('mantenimiento_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Índices para optimizar consultas
            $table->index(['tipo_documento_id', 'fecha_vencimiento']);
            $table->index(['vehiculo_id', 'created_at']);
            $table->index(['personal_id', 'created_at']);
            $table->index(['obra_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
