<?php

namespace Database\Factories;

use App\Models\CatalogoEstatus;
use App\Models\CategoriaPersonal;
use App\Models\Kilometraje;
use App\Models\Obra;
use App\Models\Personal;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kilometraje>
 */
class KilometrajeFactory extends Factory
{
    protected $model = Kilometraje::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vehiculo_id' => function () {
                $estatus = CatalogoEstatus::factory()->create();

                return Vehiculo::factory()->create(['estatus_id' => $estatus->id])->id;
            },
            'kilometraje' => $this->faker->numberBetween(1000, 100000),
            'fecha_captura' => $this->faker->date(),
            'usuario_captura_id' => function () {
                $categoria = CategoriaPersonal::factory()->create();
                $personal = Personal::factory()->create(['categoria_id' => $categoria->id]);

                return User::factory()->create(['personal_id' => $personal->id])->id;
            },
            'obra_id' => function () {
                return Obra::factory()->create()->id;
            },
            'observaciones' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * Kilometraje sin obra asignada
     */
    public function sinObra(): static
    {
        return $this->state(fn (array $attributes) => [
            'obra_id' => null,
        ]);
    }

    /**
     * Kilometraje con observaciones específicas
     */
    public function conObservaciones(string $observaciones): static
    {
        return $this->state(fn (array $attributes) => [
            'observaciones' => $observaciones,
        ]);
    }

    /**
     * Kilometraje reciente (últimos 7 días)
     */
    public function reciente(): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_captura' => $this->faker->dateTimeBetween('-7 days', 'now'),
        ]);
    }

    /**
     * Kilometraje con valor específico
     */
    public function conKilometraje(int $kilometraje): static
    {
        return $this->state(fn (array $attributes) => [
            'kilometraje' => $kilometraje,
        ]);
    }

    /**
     * Kilometraje en fecha específica
     */
    public function enFecha(string $fecha): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_captura' => $fecha,
        ]);
    }
}
