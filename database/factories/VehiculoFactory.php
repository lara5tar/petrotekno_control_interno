<?php

namespace Database\Factories;

use App\Enums\EstadoVehiculo;
use App\Models\CatalogoEstatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehiculo>
 */
class VehiculoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $marcas = ['Toyota', 'Ford', 'Chevrolet', 'Nissan', 'Honda', 'Volkswagen', 'Hyundai', 'Kia'];
        $modelos = [
            'Toyota' => ['Hilux', 'Corolla', 'Camry', 'RAV4'],
            'Ford' => ['F-150', 'Explorer', 'Focus', 'Escape'],
            'Chevrolet' => ['Silverado', 'Equinox', 'Malibu', 'Suburban'],
            'Nissan' => ['Frontier', 'Sentra', 'Altima', 'Pathfinder'],
            'Honda' => ['Civic', 'Accord', 'CR-V', 'Pilot'],
            'Volkswagen' => ['Jetta', 'Passat', 'Tiguan', 'Atlas'],
            'Hyundai' => ['Elantra', 'Sonata', 'Tucson', 'Santa Fe'],
            'Kia' => ['Forte', 'Optima', 'Sorento', 'Sportage'],
        ];

        $marca = $this->faker->randomElement($marcas);
        $modelo = $this->faker->randomElement($modelos[$marca]);

        return [
            'marca' => $marca,
            'modelo' => $modelo,
            'anio' => $this->faker->numberBetween(2010, 2024),
            'n_serie' => strtoupper($this->faker->bothify('???######')),
            'placas' => strtoupper($this->faker->bothify('???-###')),
            'estatus' => $this->faker->randomElement(array_column(EstadoVehiculo::cases(), 'value')),
            'kilometraje_actual' => $this->faker->numberBetween(0, 300000),
            'intervalo_km_motor' => $this->faker->randomElement([5000, 7500, 10000]),
            'intervalo_km_transmision' => $this->faker->randomElement([40000, 60000, 80000]),
            'intervalo_km_hidraulico' => $this->faker->randomElement([20000, 30000, 40000]),
            'observaciones' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * State for active vehicles
     */
    public function activo(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'estatus' => EstadoVehiculo::DISPONIBLE->value,
            ];
        });
    }

    /**
     * State for vehicles in maintenance
     */
    public function enMantenimiento(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'estatus' => EstadoVehiculo::EN_MANTENIMIENTO->value,
            ];
        });
    }

    /**
     * State for high mileage vehicles
     */
    public function altoKilometraje(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'kilometraje_actual' => $this->faker->numberBetween(200000, 500000),
            ];
        });
    }
}
