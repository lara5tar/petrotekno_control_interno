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
        Schema::table('asignaciones', function (Blueprint $table) {
            // Campos de combustible
            $table->decimal('combustible_inicial', 8, 2)->nullable()->after('kilometraje_inicial')
                ->comment('Nivel de combustible al inicio de la asignación (en litros)');

            $table->decimal('combustible_final', 8, 2)->nullable()->after('kilometraje_final')
                ->comment('Nivel de combustible al finalizar la asignación (en litros)');

            // Campos adicionales para control de combustible
            $table->decimal('combustible_suministrado', 10, 2)->default(0)->after('combustible_final')
                ->comment('Total de combustible suministrado durante la asignación (en litros)');

            $table->decimal('costo_combustible', 10, 2)->default(0)->after('combustible_suministrado')
                ->comment('Costo total del combustible consumido');

            $table->json('historial_combustible')->nullable()->after('costo_combustible')
                ->comment('Historial de recargas de combustible durante la asignación');

            // Índices para consultas de eficiencia
            $table->index(['combustible_inicial', 'combustible_final'], 'idx_asignacion_combustible');
            $table->index('combustible_suministrado', 'idx_combustible_suministrado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asignaciones', function (Blueprint $table) {
            $table->dropIndex('idx_asignacion_combustible');
            $table->dropIndex('idx_combustible_suministrado');

            $table->dropColumn([
                'combustible_inicial',
                'combustible_final',
                'combustible_suministrado',
                'costo_combustible',
                'historial_combustible'
            ]);
        });
    }
};
