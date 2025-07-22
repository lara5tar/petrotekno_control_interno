<?php

namespace Tests\Feature;

use App\Models\Obra;
use App\Models\Permission;
use App\Models\Personal;
use App\Models\Role;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AsignacionControllerHybridTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected Vehiculo $vehiculo;

    protected Obra $obra;

    protected Personal $personal;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles y permisos
        $adminRole = Role::factory()->create(['nombre_rol' => 'Administrador']);

        // Crear permisos
        $permisos = [
            'ver_asignaciones',
            'crear_asignaciones',
            'editar_asignaciones',
            'eliminar_asignaciones',
            'liberar_asignaciones',
        ];

        foreach ($permisos as $permiso) {
            Permission::factory()->create(['nombre_permiso' => $permiso]);
        }

        // Asignar permisos al rol admin
        $adminRole->permisos()->sync(Permission::all()->pluck('id'));

        // Crear usuario admin
        $personalAdmin = Personal::factory()->create();
        $this->adminUser = User::factory()->create([
            'personal_id' => $personalAdmin->id,
            'rol_id' => $adminRole->id,
        ]);

        $this->vehiculo = Vehiculo::factory()->create();
        $this->obra = Obra::factory()->create();
        $this->personal = Personal::factory()->create();
    }

    #[Test]
    public function admin_puede_ver_index_asignaciones_blade()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->get('/asignaciones');

        $response->assertStatus(200)
            ->assertViewIs('asignaciones.index')
            ->assertViewHas('asignaciones');
    }

    #[Test]
    public function admin_puede_ver_index_asignaciones_api()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->getJson('/api/asignaciones');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Asignaciones obtenidas exitosamente']);
    }

    #[Test]
    public function admin_puede_crear_asignacion_api()
    {
        Sanctum::actingAs($this->adminUser);

        $data = [
            'vehiculo_id' => $this->vehiculo->id,
            'obra_id' => $this->obra->id,
            'personal_id' => $this->personal->id,
            'fecha_asignacion' => now()->format('Y-m-d'),
            'kilometraje_inicial' => 10000,
            'observaciones' => 'Asignación de prueba',
        ];

        $response = $this->postJson('/api/asignaciones', $data);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Asignación creada exitosamente',
            ]);

        $this->assertDatabaseHas('asignaciones', [
            'vehiculo_id' => $this->vehiculo->id,
            'obra_id' => $this->obra->id,
            'personal_id' => $this->personal->id,
        ]);
    }

    #[Test]
    public function usuario_sin_permisos_no_puede_crear_asignacion()
    {
        $operadorRole = Role::factory()->create(['nombre_rol' => 'Operador']);
        $user = User::factory()->create([
            'personal_id' => Personal::factory()->create()->id,
            'rol_id' => $operadorRole->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/asignaciones', [
            'vehiculo_id' => $this->vehiculo->id,
            'obra_id' => $this->obra->id,
            'personal_id' => $this->personal->id,
            'fecha_asignacion' => now()->format('Y-m-d'),
            'kilometraje_inicial' => 10000,
        ]);

        $response->assertStatus(403);
    }
}
