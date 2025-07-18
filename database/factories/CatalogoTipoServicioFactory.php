<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CatalogoTipoServicio>
 */
class CatalogoTipoServicioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
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
        ];

        return [
            'nombre_tipo_servicio' => $this->faker->unique()->randomElement($tiposServicio),
        ];
    }
}
