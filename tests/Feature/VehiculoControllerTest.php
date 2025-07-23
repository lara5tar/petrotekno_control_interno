<?php

namespace Tests\Feature;

use App\Models\CatalogoEstatus;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VehiculoControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected CatalogoEstatus $estatus;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear estatus por defecto
        $this->estatus = CatalogoEstatus::factory()->create([
            'nombre_estatus' => 'Activo',
        ]);

        // Crear permisos necesarios para vehículos
        $permissions = [
            'ver_vehiculos',
            'crear_vehiculos',
            'editar_vehiculos',
            'eliminar_vehiculos',
            'restaurar_vehiculos',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['nombre_permiso' => $permission]);
        }

        // Crear rol admin con todos los permisos
        $adminRole = Role::firstOrCreate(['nombre_rol' => 'Admin']);
        $adminRole->permisos()->sync(Permission::all());

        // Crear usuario admin
        $this->user = User::factory()->create([
            'rol_id' => $adminRole->id,
        ]);

        Sanctum::actingAs($this->user);
    }

    public function test_admin_can_list_vehiculos()
    {
        Vehiculo::factory()->count(3)->create(['estatus_id' => $this->estatus->id]);

        $response = $this->getJson('/api/vehiculos');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'marca',
                        'modelo',
                        'anio',
                        'n_serie',
                        'placas',
                        'estatus_id',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
            ]);

        $this->assertEquals(3, count($response->json('data')));
    }

    public function test_admin_can_create_vehiculo()
    {
        $vehiculoData = [
            'marca' => 'Toyota',
            'modelo' => 'Corolla',
            'anio' => 2023,
            'n_serie' => 'ABC123456',
            'placas' => 'XYZ-123',
            'estatus_id' => $this->estatus->id,
            'kilometraje_actual' => 1000,
        ];

        $response = $this->postJson('/api/vehiculos', $vehiculoData);

        $response->assertStatus(201)
            ->assertJsonFragment(['marca' => 'Toyota'])
            ->assertJsonFragment(['modelo' => 'Corolla'])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'marca', 'modelo'],
            ]);

        $this->assertDatabaseHas('vehiculos', [
            'marca' => 'Toyota',
            'modelo' => 'Corolla',
            'n_serie' => 'ABC123456',
        ]);
    }

    public function test_vehiculo_creation_validation_works()
    {

        $invalidData = [
            'marca' => '',
            'modelo' => '',
            'anio' => 1800, // Año inválido
        ];

        $response = $this->postJson('/api/vehiculos', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['marca', 'modelo', 'anio']);
    }

    public function test_vehiculo_unique_constraints_work()
    {

        // Crear primer vehículo
        $vehiculoData = [
            'marca' => 'Toyota',
            'modelo' => 'Corolla',
            'anio' => 2023,
            'n_serie' => 'UNIQUE123',
            'placas' => 'ABC-123',
            'estatus_id' => $this->estatus->id,
            'kilometraje_actual' => 1000,
        ];

        $this->postJson('/api/vehiculos', $vehiculoData);

        // Intentar crear segundo vehículo con mismos datos únicos
        $duplicateData = [
            'marca' => 'Honda',
            'modelo' => 'Civic',
            'anio' => 2022,
            'n_serie' => 'UNIQUE123', // Mismo número de serie
            'placas' => 'XYZ-789',
            'estatus_id' => $this->estatus->id,
            'kilometraje_actual' => 5000,
        ];

        $response = $this->postJson('/api/vehiculos', $duplicateData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['n_serie']);
    }

    public function test_admin_can_update_vehiculo()
    {

        $vehiculo = Vehiculo::factory()->create(['estatus_id' => $this->estatus->id]);

        $updateData = [
            'marca' => 'Ford',
            'modelo' => 'Ranger',
            'anio' => 2023,
            'estatus_id' => $this->estatus->id,
        ];

        $response = $this->putJson("/api/vehiculos/{$vehiculo->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonPath('data.marca', 'Ford')
            ->assertJsonPath('data.modelo', 'Ranger')
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('vehiculos', [
            'id' => $vehiculo->id,
            'marca' => 'Ford',
            'modelo' => 'Ranger',
        ]);
    }

    public function test_admin_can_delete_vehiculo()
    {

        $vehiculo = Vehiculo::factory()->create(['estatus_id' => $this->estatus->id]);

        $response = $this->deleteJson("/api/vehiculos/{$vehiculo->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Vehículo eliminado correctamente']);

        $this->assertSoftDeleted('vehiculos', ['id' => $vehiculo->id]);
    }

    public function test_admin_can_restore_vehiculo()
    {

        $vehiculo = Vehiculo::factory()->create(['estatus_id' => $this->estatus->id]);
        $vehiculo->delete(); // Soft delete

        $response = $this->postJson("/api/vehiculos/{$vehiculo->id}/restore");

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Vehículo restaurado correctamente']);

        $vehiculo->refresh();
        $this->assertNull($vehiculo->deleted_at);
    }

    public function test_vehiculo_show_works()
    {

        $vehiculo = Vehiculo::factory()->create(['estatus_id' => $this->estatus->id]);

        $response = $this->getJson("/api/vehiculos/{$vehiculo->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.marca', $vehiculo->marca)
            ->assertJsonPath('data.modelo', $vehiculo->modelo)
            ->assertJsonPath('success', true);
    }

    public function test_vehiculo_not_found_returns_404()
    {

        $response = $this->getJson('/api/vehiculos/99999');

        $response->assertStatus(404)
            ->assertJsonFragment(['message' => 'Vehículo no encontrado']);
    }

    public function test_can_get_estatus_options()
    {

        // Create additional estatus
        CatalogoEstatus::factory()->create(['nombre_estatus' => 'Inactivo']);
        CatalogoEstatus::factory()->create(['nombre_estatus' => 'Mantenimiento']);

        $response = $this->getJson('/api/vehiculos/estatus');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => ['id', 'nombre_estatus'],
                ],
            ]);

        $responseData = $response->json('data');
        $this->assertGreaterThanOrEqual(3, count($responseData));
    }

    public function test_vehiculo_data_sanitization_works()
    {

        $vehiculoData = [
            'marca' => '  Toyota  ',
            'modelo' => '  Corolla  ',
            'anio' => 2023,
            'n_serie' => '  ABC123  ',
            'placas' => 'xyz-123',
            'estatus_id' => $this->estatus->id,
            'kilometraje_actual' => 2500,
        ];

        $response = $this->postJson('/api/vehiculos', $vehiculoData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('vehiculos', [
            'marca' => 'Toyota',
            'modelo' => 'Corolla',
            'n_serie' => 'ABC123',
            'placas' => 'XYZ-123',
        ]);
    }

    public function test_unauthorized_user_cannot_access_vehiculos()
    {
        // Crear un rol sin permisos de vehículos
        $operadorRole = Role::firstOrCreate(['nombre_rol' => 'Operador']);

        $unauthorizedUser = User::factory()->create([
            'rol_id' => $operadorRole->id,
        ]);
        Sanctum::actingAs($unauthorizedUser);

        $response = $this->getJson('/api/vehiculos');

        $response->assertStatus(403);
    }
}
