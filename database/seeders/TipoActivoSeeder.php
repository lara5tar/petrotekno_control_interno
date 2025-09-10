<?php

namespace Database\Seeders;

use App\Models\TipoActivo;
use Illuminate\Database\Seeder;

class TipoActivoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏷️ Creando tipos de activo predeterminados...');
        
        $tiposActivo = [
            [
                'nombre' => 'Vehículo',
                'tiene_kilometraje' => true,
            ],
            [
                'nombre' => 'Maquinaria',
                'tiene_kilometraje' => false,
            ],
        ];
        
        foreach ($tiposActivo as $tipoData) {
            TipoActivo::updateOrCreate(
                ['nombre' => $tipoData['nombre']],
                $tipoData
            );
            
            $kilometraje = $tipoData['tiene_kilometraje'] ? 'con kilometraje' : 'sin kilometraje';
            $this->command->info("✅ Tipo de activo creado: {$tipoData['nombre']} ({$kilometraje})");
        }
        
        $this->command->info('🎯 Tipos de activo predeterminados creados exitosamente');
    }
}