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
            // Verificar y crear índices individuales solo si no existen
            $indexes = [
                'idx_vehiculos_modelo' => 'modelo',
                'idx_vehiculos_placas' => 'placas',
                'idx_vehiculos_n_serie' => 'n_serie',
                'idx_vehiculos_estatus' => 'estatus',
                'idx_vehiculos_kilometraje' => 'kilometraje_actual'
            ];
            
            foreach ($indexes as $indexName => $column) {
                if (!$this->indexExists('vehiculos', $indexName)) {
                    $table->index($column, $indexName);
                }
            }
            
            // Verificar y crear índices compuestos solo si no existen
            $compositeIndexes = [
                'idx_vehiculos_marca_modelo' => ['marca', 'modelo'],
                'idx_vehiculos_marca_anio' => ['marca', 'anio'],
                'idx_vehiculos_estatus_marca' => ['estatus', 'marca'],
                'idx_vehiculos_estatus_anio' => ['estatus', 'anio'],
                'idx_vehiculos_km_anio' => ['kilometraje_actual', 'anio'],
                'idx_vehiculos_filtros_principales' => ['estatus', 'marca', 'anio']
            ];
            
            foreach ($compositeIndexes as $indexName => $columns) {
                if (!$this->indexExists('vehiculos', $indexName)) {
                    $table->index($columns, $indexName);
                }
            }
        });
    }
    
    /**
     * Check if an index exists on a table
     */
    private function indexExists($table, $indexName)
    {
        $indexes = \DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
        return count($indexes) > 0;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehiculos', function (Blueprint $table) {
            // Eliminar índices individuales solo si existen
            $individualIndexes = [
                'idx_vehiculos_modelo',
                'idx_vehiculos_placas',
                'idx_vehiculos_n_serie',
                'idx_vehiculos_estatus',
                'idx_vehiculos_kilometraje'
            ];
            
            foreach ($individualIndexes as $indexName) {
                if ($this->indexExists('vehiculos', $indexName)) {
                    $table->dropIndex($indexName);
                }
            }
            
            // Eliminar índices compuestos solo si existen
            $compositeIndexes = [
                'idx_vehiculos_marca_modelo',
                'idx_vehiculos_marca_anio',
                'idx_vehiculos_estatus_marca',
                'idx_vehiculos_estatus_anio',
                'idx_vehiculos_km_anio',
                'idx_vehiculos_filtros_principales'
            ];
            
            foreach ($compositeIndexes as $indexName) {
                if ($this->indexExists('vehiculos', $indexName)) {
                    $table->dropIndex($indexName);
                }
            }
        });
    }
};
