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

    protected function setUp(): void
    {
        parent::setUp();

        // Crear estatus de catálogo
        $this->estatus = CatalogoEstatus::factory()->create(['nombre_estatus' => 'Activo']);

        // Crear permisos necesarios
        $permissions = [
            'listar_vehiculos',
            'crear_vehiculo',
            'editar_vehiculo',
            'eliminar_vehiculo',
            'restaurar_vehiculo'
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['nombre_permiso' => $permissionName]);
        }

        // Crear rol administrador
        $adminRole = Role::firstOrCreate(['nombre_rol' => 'Administrador']);
        $adminRole->permisos()->sync(Permission::whereIn('nombre_permiso', $permissions)->pluck('id'));

        // Crear personal
        $personal = Personal::factory()->create();

        // Crear usuario administrador
        $this->admin = User::factory()->create([
            'personal_id' => $personal->id,
            'rol_id' => $adminRole->id,
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
                            'kilometraje_actual'
                        ]
                    ]
                ]);
    }

    public function test_admin_can_create_vehiculo()
    {
        $this->withoutMiddleware();

        $vehiculoData = [
            'marca' => 'Toyota',
            'modelo' => 'Hilux',
            'anio' => 2023,
            'n_serie' => 'ABC123456789',
            'placas' => 'ABC-123',
            'estatus_id' => $this->estatus->id,
            'kilometraje_actual' => 15000,
            'intervalo_km_motor' => 10000,
            'intervalo_km_transmision' => 50000,
            'intervalo_km_hidraulico' => 30000
        ];

        $response = $this->postJson('/api/vehiculos', $vehiculoData);

        $response->assertStatus(201)
                ->assertJsonFragment(['marca' => 'Toyota'])
                ->assertJsonFragment(['modelo' => 'Hilux']);

        $this->assertDatabaseHas('vehiculos', [
            'marca' => 'Toyota',
            'modelo' => 'Hilux',
            'n_serie' => 'ABC123456789'
        ]);
    }

    public function test_vehiculo_creation_validation_works()
    {
        $this->withoutMiddleware();

        // Test missing required fields
        $response = $this->postJson('/api/vehiculos', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['marca', 'modelo', 'anio', 'n_serie', 'placas', 'estatus_id', 'kilometraje_actual']);
    }

    public function test_admin_can_update_vehiculo()
    {
        $this->withoutMiddleware();

        $vehiculo = Vehiculo::factory()->create(['estatus_id' => $this->estatus->id]);

        $updateData = [
            'marca' => 'Ford',
            'modelo' => 'Ranger',
            'anio' => 2024,
            'kilometraje_actual' => 20000
        ];

        $response = $this->putJson("/api/vehiculos/{$vehiculo->id}", $updateData);

        $response->assertStatus(200)
                ->assertJsonFragment(['marca' => 'Ford'])
                ->assertJsonFragment(['modelo' => 'Ranger']);
    }

    public function test_admin_can_delete_vehiculo()
    {
        $this->withoutMiddleware();

        $vehiculo = Vehiculo::factory()->create(['estatus_id' => $this->estatus->id]);

        $response = $this->deleteJson("/api/vehiculos/{$vehiculo->id}");

        $response->assertStatus(200)
                ->assertJsonFragment(['message' => 'Vehículo eliminado correctamente']);

        $this->assertSoftDeleted('vehiculos', ['id' => $vehiculo->id]);
    }

    public function test_unauthorized_user_cannot_access_vehiculos()
    {
        // Limpiar autenticación usando el método correcto
        $this->app['auth']->forgetGuards();

        $response = $this->getJson('/api/vehiculos');

        $response->assertStatus(401);
    }
}
