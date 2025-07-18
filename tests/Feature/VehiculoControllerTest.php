<?php

namespace Tests\Feature;

use App\Models\CatalogoEstatus;
use App\Models\Permission;
use App\Models\Personal;
use App\Models\Role;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehiculoControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected $estatus;

    /**
     * Helper method to create valid vehiculo data
     */
    protected function getValidVehiculoData(array $overrides = []): array
    {
        return array_merge([
            'marca' => 'Toyota',
            'modelo' => 'Hilux',
            'anio' => 2023,
            'n_serie' => 'TEST'.uniqid(),
            'placas' => 'TST-'.rand(100, 999),
            'estatus_id' => $this->estatus->id,
            'kilometraje_actual' => 15000,
            'intervalo_km_motor' => 10000,
            'intervalo_km_transmision' => 50000,
            'intervalo_km_hidraulico' => 30000,
            'observaciones' => 'Vehículo de prueba',
        ], $overrides);
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Crear categoría personal primero (necesaria para personal)
        $categoria = \App\Models\CategoriaPersonal::firstOrCreate([
            'nombre_categoria' => 'Operador',
            'descripcion' => 'Operador de vehículos',
        ]);

        // Crear estatus de catálogo
        $this->estatus = CatalogoEstatus::factory()->create(['nombre_estatus' => 'Activo']);

        // Crear permisos necesarios
        $permissions = [
            'ver_vehiculos',
            'crear_vehiculo',
            'editar_vehiculo',
            'eliminar_vehiculo',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'nombre_permiso' => $permissionName,
                'descripcion' => 'Permiso para '.str_replace('_', ' ', $permissionName),
            ]);
        }

        // Crear rol administrador
        $adminRole = Role::firstOrCreate([
            'nombre_rol' => 'Administrador',
            'descripcion' => 'Administrador del sistema con todos los permisos',
        ]);

        // Sincronizar permisos
        $adminRole->permisos()->sync(Permission::whereIn('nombre_permiso', $permissions)->pluck('id'));

        // Crear personal con la categoría existente
        $personal = Personal::factory()->create([
            'categoria_id' => $categoria->id,
            'nombre_completo' => 'Admin Test User',
            'estatus' => 'activo',
        ]);

        // Crear usuario administrador
        $this->admin = User::factory()->create([
            'personal_id' => $personal->id,
            'rol_id' => $adminRole->id,
            'nombre_usuario' => 'admin_test',
            'email' => 'admin.test@petrotekno.com',
        ]);

        // Autenticar usuario
        $this->actingAs($this->admin);
    }

    public function test_admin_can_list_vehiculos()
    {
        $this->withoutMiddleware();

        // Crear algunos vehículos de prueba
        Vehiculo::factory()->count(3)->create(['estatus_id' => $this->estatus->id]);

        $response = $this->getJson('/api/vehiculos');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'marca',
                        'modelo',
                        'anio',
                        'n_serie',
                        'placas',
                        'estatus_id',
                        'kilometraje_actual',
                    ],
                ],
            ]);
    }

    public function test_admin_can_create_vehiculo()
    {
        $this->withoutMiddleware();

        $vehiculoData = $this->getValidVehiculoData([
            'marca' => 'Toyota',
            'modelo' => 'Hilux',
            'n_serie' => 'ABC123456789',
            'placas' => 'ABC-123',
        ]);

        $response = $this->postJson('/api/vehiculos', $vehiculoData);

        $response->assertStatus(201)
            ->assertJsonFragment(['marca' => 'Toyota'])
            ->assertJsonFragment(['modelo' => 'Hilux']);

        $this->assertDatabaseHas('vehiculos', [
            'marca' => 'Toyota',
            'modelo' => 'Hilux',
            'n_serie' => 'ABC123456789',
            'placas' => 'ABC-123',
        ]);
    }

    public function test_vehiculo_creation_validation_works()
    {
        $this->withoutMiddleware();

        // Test missing required fields
        $response = $this->postJson('/api/vehiculos', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['marca', 'modelo', 'anio', 'n_serie', 'placas', 'estatus_id', 'kilometraje_actual']);

        // Test invalid year (too old)
        $invalidData = $this->getValidVehiculoData(['anio' => 1989]);
        $response = $this->postJson('/api/vehiculos', $invalidData);
        $response->assertStatus(422)->assertJsonValidationErrors(['anio']);

        // Test invalid placas format
        $invalidData = $this->getValidVehiculoData(['placas' => 'invalid@placas!']);
        $response = $this->postJson('/api/vehiculos', $invalidData);
        $response->assertStatus(422)->assertJsonValidationErrors(['placas']);
    }

    public function test_vehiculo_unique_constraints_work()
    {
        $this->withoutMiddleware();

        $vehiculoData = $this->getValidVehiculoData([
            'n_serie' => 'UNIQUE123456',
            'placas' => 'UNQ-123',
        ]);

        // Create first vehiculo
        Vehiculo::create($vehiculoData);

        // Try to create another with same serie number
        $response = $this->postJson('/api/vehiculos', $vehiculoData);
        $response->assertStatus(422)->assertJsonValidationErrors(['n_serie']);

        // Try to create another with same placas but different serie
        $vehiculoData['n_serie'] = 'DIFFERENT123';
        $response = $this->postJson('/api/vehiculos', $vehiculoData);
        $response->assertStatus(422)->assertJsonValidationErrors(['placas']);
    }

    public function test_admin_can_update_vehiculo()
    {
        $this->withoutMiddleware();

        $vehiculo = Vehiculo::factory()->create(['estatus_id' => $this->estatus->id]);

        $updateData = [
            'marca' => 'Ford',
            'modelo' => 'Ranger',
            'anio' => 2024,
            'kilometraje_actual' => 20000,
        ];

        $response = $this->putJson("/api/vehiculos/{$vehiculo->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment(['marca' => 'Ford'])
            ->assertJsonFragment(['modelo' => 'Ranger']);

        $this->assertDatabaseHas('vehiculos', [
            'id' => $vehiculo->id,
            'marca' => 'Ford',
            'modelo' => 'Ranger',
        ]);
    }

    public function test_admin_can_delete_vehiculo()
    {
        $this->withoutMiddleware();

        $vehiculo = Vehiculo::factory()->create(['estatus_id' => $this->estatus->id]);

        $response = $this->deleteJson("/api/vehiculos/{$vehiculo->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Vehículo eliminado exitosamente']);

        $this->assertSoftDeleted('vehiculos', ['id' => $vehiculo->id]);
    }

    public function test_admin_can_restore_vehiculo()
    {
        $this->withoutMiddleware();

        $vehiculo = Vehiculo::factory()->create(['estatus_id' => $this->estatus->id]);
        $vehiculo->delete(); // Soft delete

        $response = $this->postJson("/api/vehiculos/{$vehiculo->id}/restore");

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Vehículo restaurado exitosamente']);

        $this->assertDatabaseHas('vehiculos', [
            'id' => $vehiculo->id,
        ]);

        // Verificar que el vehículo ya no está marcado como eliminado
        $vehiculo->refresh();
        $this->assertNull($vehiculo->deleted_at);
    }

    public function test_vehiculo_show_works()
    {
        $this->withoutMiddleware();

        $vehiculo = Vehiculo::factory()->create(['estatus_id' => $this->estatus->id]);

        $response = $this->getJson("/api/vehiculos/{$vehiculo->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['marca' => $vehiculo->marca])
            ->assertJsonFragment(['modelo' => $vehiculo->modelo]);
    }

    public function test_vehiculo_not_found_returns_404()
    {
        $this->withoutMiddleware();

        $response = $this->getJson('/api/vehiculos/99999');

        $response->assertStatus(404)
            ->assertJsonFragment(['message' => 'Vehículo no encontrado']);
    }

    public function test_can_get_estatus_options()
    {
        $this->withoutMiddleware();

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
        $this->withoutMiddleware();

        $vehiculoData = $this->getValidVehiculoData([
            'marca' => 'toyota', // lowercase should be converted to title case
            'modelo' => 'HILUX', // uppercase should be converted to title case
            'placas' => 'abc-123', // lowercase should be converted to uppercase
        ]);

        $response = $this->postJson('/api/vehiculos', $vehiculoData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('vehiculos', [
            'marca' => 'Toyota', // Should be title case
            'modelo' => 'Hilux', // Should be title case
            'placas' => 'ABC-123', // Should be uppercase
        ]);
    }

    public function test_unauthorized_user_cannot_access_vehiculos()
    {
        // Limpiar autenticación
        $this->app['auth']->forgetGuards();

        $response = $this->getJson('/api/vehiculos');

        $response->assertStatus(401);
    }
}
