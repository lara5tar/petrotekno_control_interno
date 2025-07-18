<?php

namespace Database\Factories;

use App\Models\CategoriaPersonal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CategoriaPersonal>
 */
class CategoriaPersonalFactory extends Factory
{
    protected $model = CategoriaPersonal::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre_categoria' => $this->faker->randomElement([
                'Operador',
                'Mecánico',
                'Supervisor',
                'Jefe de Taller',
                'Administrador'
            ]),
        ];
    }
}
