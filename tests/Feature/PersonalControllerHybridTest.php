<?php

namespace Tests\Feature;

use App\Models\CategoriaPersonal;
use App\Models\Permission;
use App\Models\Personal;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PersonalControllerHybridTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected User $supervisorUser;

    protected User $operadorUser;

    protected CategoriaPersonal $categoria;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles y permisos
        $adminRole = Role::factory()->create(['nombre_rol' => 'Administrador']);
        $supervisorRole = Role::factory()->create(['nombre_rol' => 'Supervisor']);
        $operadorRole = Role::factory()->create(['nombre_rol' => 'Operador']);

        // Crear permisos
        $permisos = [
            'ver_personal',
            'crear_personal',
            'editar_personal',
            'eliminar_personal',
        ];

        foreach ($permisos as $permiso) {
            Permission::factory()->create(['nombre_permiso' => $permiso]);
        }

        // Asignar permisos al rol admin
        $adminRole->permisos()->sync(Permission::all()->pluck('id'));

        // Asignar solo ver_personal al supervisor
        $supervisorRole->permisos()->sync(
            Permission::where('nombre_permiso', 'ver_personal')->pluck('id')
        );

        // Crear usuarios
        $personalAdmin = Personal::factory()->create();
        $this->adminUser = User::factory()->create([
            'personal_id' => $personalAdmin->id,
            'rol_id' => $adminRole->id,
        ]);

        $personalSupervisor = Personal::factory()->create();
        $this->supervisorUser = User::factory()->create([
            'personal_id' => $personalSupervisor->id,
            'rol_id' => $supervisorRole->id,
        ]);

        $personalOperador = Personal::factory()->create();
        $this->operadorUser = User::factory()->create([
            'personal_id' => $personalOperador->id,
            'rol_id' => $operadorRole->id,
        ]);

        $this->categoria = CategoriaPersonal::factory()->create();
    }

    #[Test]
    public function admin_puede_ver_index_personal_blade()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->get('/personal');

        $response->assertStatus(200)
            ->assertViewIs('personal.index')
            ->assertViewHas('personal');
    }

    #[Test]
    public function admin_puede_ver_index_personal_api()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->getJson('/api/personal');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'current_page',
                    'data',
                    'first_page_url',
                    'from',
                    'last_page',
                    'last_page_url',
                    'links',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to',
                    'total',
                ],
            ]);
    }

    #[Test]
    public function admin_puede_ver_create_personal_blade()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->get('/personal/create');

        $response->assertStatus(200)
            ->assertViewIs('personal.create')
            ->assertViewHas('categorias');
    }

    #[Test]
    public function admin_puede_ver_create_personal_api()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->getJson('/api/personal/create');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'categorias',
                ],
            ]);
    }

    #[Test]
    public function admin_puede_crear_personal_api()
    {
        Sanctum::actingAs($this->adminUser);

        $data = [
            'nombre_completo' => 'Juan Pérez García',
            'categoria_id' => $this->categoria->id,
            'estatus' => 'activo',
        ];

        $response = $this->postJson('/api/personal', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Personal creado exitosamente',
            ]);

        $this->assertDatabaseHas('personal', [
            'nombre_completo' => 'Juan Pérez García',
            'categoria_id' => $this->categoria->id,
            'estatus' => 'activo',
        ]);
    }

    #[Test]
    public function admin_puede_crear_personal_blade()
    {
        Sanctum::actingAs($this->adminUser);

        $data = [
            'nombre_completo' => 'María López Rodríguez',
            'categoria_id' => $this->categoria->id,
            'estatus' => 'activo',
        ];

        $response = $this->post('/personal', $data);

        $response->assertRedirect()
            ->assertSessionHas('success', 'Personal creado exitosamente');

        $this->assertDatabaseHas('personal', [
            'nombre_completo' => 'María López Rodríguez',
            'categoria_id' => $this->categoria->id,
            'estatus' => 'activo',
        ]);
    }

    #[Test]
    public function admin_puede_ver_personal_individual_blade()
    {
        Sanctum::actingAs($this->adminUser);

        $personal = Personal::factory()->create([
            'categoria_id' => $this->categoria->id,
        ]);

        $response = $this->get("/personal/{$personal->id}");

        $response->assertStatus(200)
            ->assertViewIs('personal.show')
            ->assertViewHas('personal', $personal);
    }

    #[Test]
    public function admin_puede_ver_personal_individual_api()
    {
        Sanctum::actingAs($this->adminUser);

        $personal = Personal::factory()->create([
            'categoria_id' => $this->categoria->id,
        ]);

        $response = $this->getJson("/api/personal/{$personal->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Personal obtenido exitosamente',
                'data' => [
                    'id' => $personal->id,
                    'nombre_completo' => $personal->nombre_completo,
                    'categoria_id' => $this->categoria->id,
                ],
            ]);
    }

    #[Test]
    public function admin_puede_ver_edit_personal_blade()
    {
        Sanctum::actingAs($this->adminUser);

        $personal = Personal::factory()->create([
            'categoria_id' => $this->categoria->id,
        ]);

        $response = $this->get("/personal/{$personal->id}/edit");

        $response->assertStatus(200)
            ->assertViewIs('personal.edit')
            ->assertViewHas('personal', $personal)
            ->assertViewHas('categorias');
    }

    #[Test]
    public function admin_puede_ver_edit_personal_api()
    {
        Sanctum::actingAs($this->adminUser);

        $personal = Personal::factory()->create([
            'categoria_id' => $this->categoria->id,
        ]);

        $response = $this->getJson("/api/personal/{$personal->id}/edit");

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'personal',
                    'categorias',
                ],
            ]);
    }

    #[Test]
    public function admin_puede_actualizar_personal_api()
    {
        Sanctum::actingAs($this->adminUser);

        $personal = Personal::factory()->create([
            'categoria_id' => $this->categoria->id,
        ]);

        $data = [
            'nombre_completo' => 'Nombre Actualizado API',
            'categoria_id' => $this->categoria->id,
            'estatus' => 'inactivo',
        ];

        $response = $this->putJson("/api/personal/{$personal->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Personal actualizado exitosamente',
                'data' => [
                    'id' => $personal->id,
                    'nombre_completo' => 'Nombre Actualizado API',
                    'estatus' => 'inactivo',
                ],
            ]);

        $this->assertDatabaseHas('personal', [
            'id' => $personal->id,
            'nombre_completo' => 'Nombre Actualizado API',
            'estatus' => 'inactivo',
        ]);
    }

    #[Test]
    public function admin_puede_actualizar_personal_blade()
    {
        Sanctum::actingAs($this->adminUser);

        $personal = Personal::factory()->create([
            'categoria_id' => $this->categoria->id,
        ]);

        $data = [
            'nombre_completo' => 'Nombre Actualizado Blade',
            'categoria_id' => $this->categoria->id,
            'estatus' => 'inactivo',
        ];

        $response = $this->put("/personal/{$personal->id}", $data);

        $response->assertRedirect()
            ->assertSessionHas('success', 'Personal actualizado exitosamente');

        $this->assertDatabaseHas('personal', [
            'id' => $personal->id,
            'nombre_completo' => 'Nombre Actualizado Blade',
            'estatus' => 'inactivo',
        ]);
    }

    #[Test]
    public function admin_puede_eliminar_personal_api()
    {
        Sanctum::actingAs($this->adminUser);

        $personal = Personal::factory()->create([
            'categoria_id' => $this->categoria->id,
        ]);

        $response = $this->deleteJson("/api/personal/{$personal->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Personal eliminado exitosamente',
            ]);

        $this->assertSoftDeleted('personal', [
            'id' => $personal->id,
        ]);
    }

    #[Test]
    public function admin_puede_eliminar_personal_blade()
    {
        Sanctum::actingAs($this->adminUser);

        $personal = Personal::factory()->create([
            'categoria_id' => $this->categoria->id,
        ]);

        $response = $this->delete("/personal/{$personal->id}");

        $response->assertRedirect()
            ->assertSessionHas('success', 'Personal eliminado exitosamente');

        $this->assertSoftDeleted('personal', [
            'id' => $personal->id,
        ]);
    }

    #[Test]
    public function supervisor_puede_ver_personal_pero_no_crear()
    {
        Sanctum::actingAs($this->supervisorUser);

        // Puede ver lista
        $response = $this->getJson('/api/personal');
        $response->assertStatus(200);

        // No puede crear
        $response = $this->postJson('/api/personal', [
            'nombre_completo' => 'Test Personal',
            'categoria_id' => $this->categoria->id,
            'estatus' => 'Activo',
        ]);
        $response->assertStatus(403);
    }

    #[Test]
    public function operador_no_puede_acceder_a_personal()
    {
        Sanctum::actingAs($this->operadorUser);

        $response = $this->getJson('/api/personal');
        $response->assertStatus(403);
    }

    #[Test]
    public function validacion_falla_con_datos_invalidos_api()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->postJson('/api/personal', [
            'nombre_completo' => '', // Campo requerido vacío
            'categoria_id' => 999, // ID inexistente
            'estatus' => 'InvalidStatus', // Valor inválido
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nombre_completo', 'categoria_id', 'estatus']);
    }

    #[Test]
    public function validacion_falla_con_datos_invalidos_blade()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->post('/personal', [
            'nombre_completo' => '',
            'categoria_id' => 999,
            'estatus' => 'InvalidStatus',
        ]);

        $response->assertRedirect()
            ->assertSessionHasErrors(['nombre_completo', 'categoria_id', 'estatus']);
    }

    #[Test]
    public function filtros_funcionan_correctamente_en_index()
    {
        Sanctum::actingAs($this->adminUser);

        // Crear personal con diferentes estados
        Personal::factory()->create([
            'categoria_id' => $this->categoria->id,
            'estatus' => 'Activo',
            'nombre_completo' => 'Personal Activo',
        ]);

        Personal::factory()->create([
            'categoria_id' => $this->categoria->id,
            'estatus' => 'Inactivo',
            'nombre_completo' => 'Personal Inactivo',
        ]);

        // Filtrar por estatus activo
        $response = $this->getJson('/api/personal?estatus=Activo');
        $response->assertStatus(200);

        $data = $response->json('data.data');
        $this->assertNotEmpty($data);
        $this->assertEquals('Activo', $data[0]['estatus']);
    }

    #[Test]
    public function busqueda_por_nombre_funciona_correctamente()
    {
        Sanctum::actingAs($this->adminUser);

        Personal::factory()->create([
            'categoria_id' => $this->categoria->id,
            'nombre_completo' => 'Juan Carlos Pérez',
        ]);

        Personal::factory()->create([
            'categoria_id' => $this->categoria->id,
            'nombre_completo' => 'María García López',
        ]);

        $response = $this->getJson('/api/personal?search=Juan');
        $response->assertStatus(200);

        $data = $response->json('data.data');
        $this->assertCount(1, $data);
        $this->assertStringContainsString('Juan', $data[0]['nombre_completo']);
    }

    #[Test]
    public function web_requests_redirect_on_permission_denied()
    {
        Sanctum::actingAs($this->operadorUser);

        $response = $this->get('/personal');

        $response->assertRedirect()
            ->assertSessionHas('error', 'No tienes permisos para acceder a esta sección');
    }

    #[Test]
    public function api_requests_return_403_on_permission_denied()
    {
        Sanctum::actingAs($this->operadorUser);

        $response = $this->getJson('/api/personal');

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => "No tienes el permiso 'ver_personal' necesario para acceder a este recurso",
            ]);
    }

    #[Test]
    public function both_api_and_web_return_same_data_structure()
    {
        Sanctum::actingAs($this->adminUser);

        $personal = Personal::factory()->create([
            'categoria_id' => $this->categoria->id,
        ]);

        // Obtener datos vía API
        $apiResponse = $this->getJson("/api/personal/{$personal->id}");
        $apiData = $apiResponse->json('data');

        // Obtener datos vía Web (simulamos la estructura que tendría la vista)
        $webResponse = $this->get("/personal/{$personal->id}");
        $webData = $webResponse->viewData('personal')->toArray();

        // Verificar que contienen la misma información esencial
        $this->assertEquals($apiData['id'], $webData['id']);
        $this->assertEquals($apiData['nombre_completo'], $webData['nombre_completo']);
        $this->assertEquals($apiData['categoria_id'], $webData['categoria_id']);
        $this->assertEquals($apiData['estatus'], $webData['estatus']);
    }
}
