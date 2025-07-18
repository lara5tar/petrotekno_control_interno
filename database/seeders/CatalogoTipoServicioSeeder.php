<?php

namespace Database\Seeders;

use App\Models\CatalogoTipoServicio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CatalogoTipoServicioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiposServicio = [
            'Mantenimiento Preventivo Motor',
            'Mantenimiento Preventivo Transmisión',
            'Mantenimiento Preventivo Hidráulico',
            'Reparación General',
            'Cambio de Aceite',
            'Revisión de Frenos',
            'Servicio Eléctrico',
            'Reparación de Motor',
            'Cambio de Llantas',
            'Alineación y Balanceo',
            'Reparación de Transmisión',
            'Servicio de Aire Acondicionado',
            'Inspección General',
            'Reparación de Suspensión',
            'Cambio de Filtros',
            'Mantenimiento de Sistema de Combustible',
            'Reparación de Dirección',
            'Servicio de Enfriamiento',
            'Revisión de Escape',
            'Mantenimiento Correctivo'
        ];

        foreach ($tiposServicio as $tipo) {
            CatalogoTipoServicio::firstOrCreate([
                'nombre_tipo_servicio' => $tipo
            ]);
        }
    }
}
