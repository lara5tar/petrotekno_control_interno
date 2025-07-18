<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Documento>
 */
class DocumentoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tipo_documento_id' => \App\Models\CatalogoTipoDocumento::factory(),
            'descripcion' => $this->faker->paragraph(),
            'ruta_archivo' => $this->faker->optional()->filePath(),
            'fecha_vencimiento' => $this->faker->optional()->dateTimeBetween('now', '+2 years'),
            'vehiculo_id' => $this->faker->optional()->randomElement(\App\Models\Vehiculo::pluck('id')->toArray()),
            'personal_id' => $this->faker->optional()->randomElement(\App\Models\Personal::pluck('id')->toArray()),
            'obra_id' => $this->faker->optional()->randomElement(\App\Models\Obra::pluck('id')->toArray()),
            'mantenimiento_id' => null, // Por ahora null hasta implementar mantenimientos
        ];
    }
}
