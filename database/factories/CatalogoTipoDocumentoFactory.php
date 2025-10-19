<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CatalogoTipoDocumento>
 */
class CatalogoTipoDocumentoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre_tipo_documento' => $this->faker->unique()->words(3, true),
            'descripcion' => $this->faker->sentence(),
            'requiere_vencimiento' => $this->faker->boolean(),
        ];
    }
}
