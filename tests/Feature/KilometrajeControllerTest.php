<?php

namespace Tests\Feature;

use App\Models\Kilometraje;
use App\Models\Obra;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class KilometrajeControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    private Vehiculo $vehiculo;

    private Obra $obra;

    protected function setUp(): void
    {
        parent::setUp();

        // Ejecutar todos los seeders (incluyendo permisos de kilometrajes)
        $this->artisan('db:seed');

        // Usar el usuario admin que ya tiene permisos
        $this->user = User::where('email', 'admin@petrotekno.com')->first();

        // Crear vehiculo y obra para las pruebas
        $this->vehiculo = Vehiculo::factory()->create();
        $this->obra = Obra::factory()->create();
    }

    public function test_puede_listar_kilometrajes(): void
    {
        // Usar withoutMiddleware para probar solo la funcionalidad del controlador
        $this->withoutMiddleware();

        // Crear algunos kilometrajes de prueba
        Kilometraje::factory()->count(3)->create([
            'vehiculo_id' => $this->vehiculo->id,
            'usuario_captura_id' => $this->user->id,
            'obra_id' => $this->obra->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/kilometrajes');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'vehiculo_id',
                        'kilometraje',
                        'fecha_captura',
                        'usuario_captura_id',
                        'obra_id',
                        'observaciones',
                        'vehiculo',
                        'obra',
                        'usuario_captura',
                    ],
                ],
            ])
            ->assertJson([
                'success' => true,
            ]);

        // Verificar que devuelve la cantidad correcta de kilometrajes
        $this->assertCount(3, $response->json('data'));
    }
}
