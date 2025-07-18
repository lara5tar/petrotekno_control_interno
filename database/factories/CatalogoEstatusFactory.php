<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CatalogoEstatus>
 */
class CatalogoEstatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $estatusOptions = [
            ['nombre' => 'activo', 'descripcion' => 'Vehículo en operación normal'],
            ['nombre' => 'mantenimiento', 'descripcion' => 'Vehículo en proceso de mantenimiento'],
            ['nombre' => 'disponible', 'descripcion' => 'Vehículo disponible para asignación'],
            ['nombre' => 'asignado', 'descripcion' => 'Vehículo asignado a obra específica'],
            ['nombre' => 'fuera_servicio', 'descripcion' => 'Vehículo temporalmente fuera de servicio'],
        ];

        $estatus = $this->faker->randomElement($estatusOptions);

        return [
            'nombre_estatus' => $estatus['nombre'],
            'descripcion' => $estatus['descripcion'],
            'activo' => true,
        ];
    }

    /**
     * State for active status
     */
    public function activo(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'nombre_estatus' => 'activo',
                'descripcion' => 'Vehículo en operación normal',
            ];
        });
    }

    /**
     * State for maintenance status
     */
    public function mantenimiento(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'nombre_estatus' => 'mantenimiento',
                'descripcion' => 'Vehículo en proceso de mantenimiento',
            ];
        });
    }
}
