<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mantenimiento>
 */
class MantenimientoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fechaInicio = $this->faker->dateTimeBetween('-2 years', 'now');
        $fechaFin = $this->faker->optional(0.7)->dateTimeBetween($fechaInicio, 'now');

        return [
            'vehiculo_id' => \App\Models\Vehiculo::factory(),
            'tipo_servicio_id' => \App\Models\CatalogoTipoServicio::factory(),
            'proveedor' => $this->faker->optional(0.8)->company(),
            'descripcion' => $this->faker->paragraph(2),
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'kilometraje_servicio' => $this->faker->numberBetween(1000, 300000),
            'costo' => $this->faker->optional(0.9)->randomFloat(2, 500, 50000),
        ];
    }

    /**
     * Indicate that the mantenimiento is completed.
     */
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            $fechaInicio = $attributes['fecha_inicio'];

            return [
                'fecha_fin' => $this->faker->dateTimeBetween($fechaInicio, 'now'),
            ];
        });
    }

    /**
     * Indicate that the mantenimiento is pending.
     */
    public function pending(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'fecha_fin' => null,
            ];
        });
    }

    /**
     * Indicate that the mantenimiento is expensive.
     */
    public function expensive(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'costo' => $this->faker->randomFloat(2, 20000, 100000),
            ];
        });
    }
}
