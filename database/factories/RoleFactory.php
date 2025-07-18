<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre_rol' => $this->faker->unique()->randomElement([
                'Administrador',
                'Supervisor',
                'Operador',
                'Mantenimiento',
                'Recursos Humanos',
                'Gerente',
                'Coordinador'
            ]),
            'descripcion' => $this->faker->sentence(6),
        ];
    }

    /**
     * Indicate that the role is an administrator.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'nombre_rol' => 'Administrador',
            'descripcion' => 'Acceso completo al sistema',
        ]);
    }

    /**
     * Indicate that the role is a supervisor.
     */
    public function supervisor(): static
    {
        return $this->state(fn (array $attributes) => [
            'nombre_rol' => 'Supervisor',
            'descripcion' => 'Supervisión de operaciones y personal',
        ]);
    }
}
