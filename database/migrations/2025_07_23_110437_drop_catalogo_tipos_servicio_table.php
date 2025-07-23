<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('catalogo_tipos_servicio');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('catalogo_tipos_servicio', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_tipo_servicio');
            $table->timestamps();
        });

        // Restaurar datos básicos
        DB::table('catalogo_tipos_servicio')->insert([
            ['nombre_tipo_servicio' => 'Mantenimiento Preventivo', 'created_at' => now(), 'updated_at' => now()],
            ['nombre_tipo_servicio' => 'Mantenimiento Correctivo', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
};
