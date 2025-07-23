<?php

namespace Tests\Feature;

use App\Models\CatalogoTipoDocumento;
use App\Models\Permission;
use App\Models\Personal;
use App\Models\Role;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DocumentoControllerHybridTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected Vehiculo $vehiculo;

    protected Personal $personal;

    protected CatalogoTipoDocumento $tipoDocumento;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles y permisos
        $adminRole = Role::factory()->create(['nombre_rol' => 'Administrador']);

        // Crear permisos
        $permisos = [
            'ver_documentos',
            'crear_documentos',
            'editar_documentos',
            'eliminar_documentos',
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
        $this->personal = Personal::factory()->create();
        $this->tipoDocumento = CatalogoTipoDocumento::factory()->create([
            'requiere_vencimiento' => false,
        ]);
    }

    #[Test]
    public function admin_puede_ver_index_documentos_blade()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->get('/documentos');

        $response->assertStatus(200)
            ->assertViewIs('documentos.index')
            ->assertViewHas('documentos');
    }

    #[Test]
    public function admin_puede_ver_index_documentos_api()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->getJson('/api/documentos');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    #[Test]
    public function admin_puede_crear_documento_api()
    {
        Sanctum::actingAs($this->adminUser);

        $data = [
            'tipo_documento_id' => $this->tipoDocumento->id,
            'descripcion' => 'Documento de prueba',
            'vehiculo_id' => $this->vehiculo->id,
        ];

        $response = $this->postJson('/api/documentos', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('documentos', [
            'tipo_documento_id' => $this->tipoDocumento->id,
            'descripcion' => 'Documento de prueba',
        ]);
    }

    #[Test]
    public function usuario_sin_permisos_no_puede_crear_documento()
    {
        $operadorRole = Role::factory()->create(['nombre_rol' => 'Operador']);
        $user = User::factory()->create([
            'personal_id' => Personal::factory()->create()->id,
            'rol_id' => $operadorRole->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/documentos', [
            'tipo_documento_id' => $this->tipoDocumento->id,
            'descripcion' => 'Documento de prueba',
            'vehiculo_id' => $this->vehiculo->id,
        ]);

        $response->assertStatus(403);
    }
}
