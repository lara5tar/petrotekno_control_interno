<?php

namespace Database\Factories;

use App\Models\Obra;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Obra>
 */
class ObraFactory extends Factory
{
    /**
     * El nombre del modelo correspondiente a la factory
     */
    protected $model = Obra::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fechaInicio = $this->faker->dateTimeBetween('-2 years', '+6 months');
        $fechaFin = $this->faker->optional(0.8)->dateTimeBetween($fechaInicio, '+1 year');

        return [
            'nombre_obra' => $this->faker->unique()->sentence(3, false),
            'estatus' => $this->faker->randomElement(Obra::ESTADOS_VALIDOS),
            'avance' => $this->faker->numberBetween(0, 100),
            'fecha_inicio' => $fechaInicio->format('Y-m-d'),
            'fecha_fin' => $fechaFin ? $fechaFin->format('Y-m-d') : null,
        ];
    }

    /**
     * Indica que la obra está planificada
     */
    public function planificada()
    {
        return $this->state(function (array $attributes) {
            return [
                'estatus' => Obra::ESTATUS_PLANIFICADA,
                'avance' => 0,
                'fecha_inicio' => $this->faker->dateTimeBetween('+1 week', '+3 months')->format('Y-m-d'),
            ];
        });
    }

    /**
     * Indica que la obra está en progreso
     */
    public function enProgreso()
    {
        return $this->state(function (array $attributes) {
            return [
                'estatus' => Obra::ESTATUS_EN_PROGRESO,
                'avance' => $this->faker->numberBetween(1, 99),
                'fecha_inicio' => $this->faker->dateTimeBetween('-6 months', '-1 week')->format('Y-m-d'),
            ];
        });
    }

    /**
     * Indica que la obra está completada
     */
    public function completada()
    {
        return $this->state(function (array $attributes) {
            $fechaInicio = $this->faker->dateTimeBetween('-1 year', '-2 months');
            $fechaFin = $this->faker->dateTimeBetween($fechaInicio, '-1 week');

            return [
                'estatus' => Obra::ESTATUS_COMPLETADA,
                'avance' => 100,
                'fecha_inicio' => $fechaInicio->format('Y-m-d'),
                'fecha_fin' => $fechaFin->format('Y-m-d'),
            ];
        });
    }

    /**
     * Indica que la obra está suspendida
     */
    public function suspendida()
    {
        return $this->state(function (array $attributes) {
            return [
                'estatus' => Obra::ESTATUS_SUSPENDIDA,
                'avance' => $this->faker->numberBetween(10, 70),
                'fecha_inicio' => $this->faker->dateTimeBetween('-1 year', '-2 months')->format('Y-m-d'),
            ];
        });
    }

    /**
     * Indica que la obra está cancelada
     */
    public function cancelada()
    {
        return $this->state(function (array $attributes) {
            return [
                'estatus' => Obra::ESTATUS_CANCELADA,
                'avance' => $this->faker->numberBetween(0, 50),
                'fecha_inicio' => $this->faker->dateTimeBetween('-1 year', '-3 months')->format('Y-m-d'),
                'fecha_fin' => null,
            ];
        });
    }

    /**
     * Obra con fechas específicas
     */
    public function conFechas($fechaInicio, $fechaFin = null)
    {
        return $this->state(function (array $attributes) use ($fechaInicio, $fechaFin) {
            return [
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
            ];
        });
    }

    /**
     * Obra atrasada (fecha fin pasada pero no completada)
     */
    public function atrasada()
    {
        return $this->state(function (array $attributes) {
            $fechaInicio = $this->faker->dateTimeBetween('-1 year', '-6 months');
            $fechaFin = $this->faker->dateTimeBetween($fechaInicio, '-1 month');

            return [
                'estatus' => $this->faker->randomElement([Obra::ESTATUS_EN_PROGRESO, Obra::ESTATUS_SUSPENDIDA]),
                'avance' => $this->faker->numberBetween(30, 80),
                'fecha_inicio' => $fechaInicio->format('Y-m-d'),
                'fecha_fin' => $fechaFin->format('Y-m-d'),
            ];
        });
    }
}
