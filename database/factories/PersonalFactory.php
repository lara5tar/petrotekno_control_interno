<?php

namespace Database\Factories;

use App\Models\Personal;
use App\Models\CategoriaPersonal;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonalFactory extends Factory
{
    protected $model = Personal::class;

    public function definition(): array
    {
        return [
            'nombre_completo' => $this->faker->name(),
            'estatus' => $this->faker->randomElement(['activo', 'inactivo']),
            'categoria_id' => CategoriaPersonal::factory(),
        ];
    }
}
