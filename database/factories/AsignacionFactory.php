<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Asignacion>
 */
class AsignacionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fechaAsignacion = $this->faker->dateTimeBetween('-6 months', 'now');
        $esActiva = $this->faker->boolean(30); // 30% probabilidad de estar activa

        // Para evitar duplicaciones, usar existing() cuando sea posible
        $vehiculo = \App\Models\Vehiculo::inRandomOrder()->first() ?? \App\Models\Vehiculo::factory();
        $obra = \App\Models\Obra::inRandomOrder()->first() ?? \App\Models\Obra::factory();
        $personal = \App\Models\Personal::inRandomOrder()->first() ?? \App\Models\Personal::factory();
        $usuario = \App\Models\User::inRandomOrder()->first() ?? \App\Models\User::factory();

        return [
            'vehiculo_id' => $vehiculo->id ?? $vehiculo,
            'obra_id' => $obra->id ?? $obra,
            'personal_id' => $personal->id ?? $personal,
            'creado_por_id' => $usuario->id ?? $usuario,
            'fecha_asignacion' => $fechaAsignacion,
            'fecha_liberacion' => $esActiva ? null : $this->faker->dateTimeBetween($fechaAsignacion, 'now'),
            'kilometraje_inicial' => $this->faker->numberBetween(50000, 200000),
            'kilometraje_final' => $esActiva ? null : $this->faker->numberBetween(200000, 350000),
            'observaciones' => $this->faker->optional(0.4)->paragraph(),
        ];
    }

    /**
     * Estado para asignaciones activas
     */
    public function activa(): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_liberacion' => null,
            'kilometraje_final' => null,
        ]);
    }

    /**
     * Estado para asignaciones liberadas
     */
    public function liberada(): static
    {
        return $this->state(function (array $attributes) {
            $kmInicial = $attributes['kilometraje_inicial'] ?? $this->faker->numberBetween(50000, 200000);

            return [
                'fecha_liberacion' => $this->faker->dateTimeBetween($attributes['fecha_asignacion'] ?? '-3 months', 'now'),
                'kilometraje_final' => $kmInicial + $this->faker->numberBetween(500, 15000),
            ];
        });
    }

    /**
     * Estado para asignaciones con observaciones específicas
     */
    public function conObservaciones(): static
    {
        return $this->state(fn (array $attributes) => [
            'observaciones' => $this->faker->paragraph()."\n\nObservaciones adicionales: ".$this->faker->sentence(),
        ]);
    }
}
