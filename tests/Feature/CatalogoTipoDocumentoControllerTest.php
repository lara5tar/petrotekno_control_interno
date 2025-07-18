<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\CatalogoTipoDocumento;
use App\Models\Documento;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CatalogoTipoDocumentoControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $adminUser;
    private User $regularUser;
    private Role $adminRole;
    private Role $userRole;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear roles y permisos
        $this->adminRole = Role::factory()->admin()->create();
        $this->userRole = Role::factory()->create(['nombre_rol' => 'Usuario']);
        
        // Permisos para catálogos (tipos de documento)
        $verCatalogosPermission = Permission::factory()->withName('ver_catalogos')->create();
        $crearCatalogosPermission = Permission::factory()->withName('crear_catalogos')->create();
        $editarCatalogosPermission = Permission::factory()->withName('editar_catalogos')->create();
        $eliminarCatalogosPermission = Permission::factory()->withName('eliminar_catalogos')->create();
        
        // Permisos para documentos
        $createPermission = Permission::factory()->withName('crear_documentos')->create();
        $viewPermission = Permission::factory()->withName('ver_documentos')->create();
        $editPermission = Permission::factory()->withName('editar_documentos')->create();
        $deletePermission = Permission::factory()->withName('eliminar_documentos')->create();
        
        $this->adminRole->permisos()->attach([
            $verCatalogosPermission->id, $crearCatalogosPermission->id, 
            $editarCatalogosPermission->id, $eliminarCatalogosPermission->id,
            $createPermission->id, $viewPermission->id, 
            $editPermission->id, $deletePermission->id
        ]);
        $this->userRole->permisos()->attach([$viewPermission->id]);
        
        // Crear usuarios
        $this->adminUser = User::factory()->create(['rol_id' => $this->adminRole->id]);
        $this->regularUser = User::factory()->create(['rol_id' => $this->userRole->id]);
    }

        public function test_usuario_sin_permisos_no_puede_acceder_a_tipos_documento()
    {
        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->getJson('/api/catalogo-tipos-documento');

        $response->assertStatus(403);
    }

        public function test_usuario_con_permisos_puede_listar_tipos_documento()
    {
        CatalogoTipoDocumento::factory()->count(3)->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/catalogo-tipos-documento');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'nombre_tipo_documento',
                            'descripcion',
                            'requiere_vencimiento',
                            'created_at',
                            'updated_at'
                        ]
                    ]
                ]
            ]);
    }

        public function test_puede_crear_tipo_documento_con_datos_validos()
    {
        $tipoDocumentoData = [
            'nombre_tipo_documento' => 'Tipo de Documento Test',
            'descripcion' => 'Descripción del tipo de documento',
            'requiere_vencimiento' => true
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/catalogo-tipos-documento', $tipoDocumentoData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'nombre_tipo_documento',
                    'descripcion',
                    'requiere_vencimiento'
                ]
            ]);

        $this->assertDatabaseHas('catalogo_tipos_documento', [
            'nombre_tipo_documento' => 'Tipo de Documento Test',
            'descripcion' => 'Descripción del tipo de documento',
            'requiere_vencimiento' => true
        ]);
    }

        public function test_no_puede_crear_tipo_documento_sin_nombre()
    {
        $tipoDocumentoData = [
            'descripcion' => 'Descripción sin nombre',
            'requiere_vencimiento' => false
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/catalogo-tipos-documento', $tipoDocumentoData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nombre_tipo_documento']);
    }

        public function test_no_puede_crear_tipo_documento_con_nombre_duplicado()
    {
        CatalogoTipoDocumento::factory()->create([
            'nombre_tipo_documento' => 'Tipo Existente'
        ]);

        $tipoDocumentoData = [
            'nombre_tipo_documento' => 'Tipo Existente',
            'descripcion' => 'Intento de duplicado'
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/catalogo-tipos-documento', $tipoDocumentoData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nombre_tipo_documento']);
    }

        public function test_puede_obtener_tipo_documento_especifico()
    {
        $tipoDocumento = CatalogoTipoDocumento::factory()->create([
            'nombre_tipo_documento' => 'Tipo Específico',
            'requiere_vencimiento' => true
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson("/api/catalogo-tipos-documento/{$tipoDocumento->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $tipoDocumento->id,
                    'nombre_tipo_documento' => 'Tipo Específico',
                    'requiere_vencimiento' => true
                ]
            ]);
    }

        public function test_puede_obtener_tipo_documento_con_documentos_relacionados()
    {
        $tipoDocumento = CatalogoTipoDocumento::factory()->create();
        
        // Crear algunos documentos relacionados
        Documento::factory()->count(2)->create([
            'tipo_documento_id' => $tipoDocumento->id
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson("/api/catalogo-tipos-documento/{$tipoDocumento->id}?with_documentos=true");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'nombre_tipo_documento',
                    'documentos' => [
                        '*' => [
                            'id',
                            'descripcion',
                            'vehiculo',
                            'personal',
                            'obra'
                        ]
                    ]
                ]
            ]);

        $documentos = $response->json('data.documentos');
        $this->assertCount(2, $documentos);
    }

        public function test_puede_actualizar_tipo_documento()
    {
        $tipoDocumento = CatalogoTipoDocumento::factory()->create([
            'nombre_tipo_documento' => 'Nombre Original',
            'requiere_vencimiento' => false
        ]);

        $updateData = [
            'nombre_tipo_documento' => 'Nombre Actualizado',
            'requiere_vencimiento' => true
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/catalogo-tipos-documento/{$tipoDocumento->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Tipo de documento actualizado exitosamente'
            ]);

        $this->assertDatabaseHas('catalogo_tipos_documento', [
            'id' => $tipoDocumento->id,
            'nombre_tipo_documento' => 'Nombre Actualizado',
            'requiere_vencimiento' => true
        ]);
    }

        public function test_no_puede_actualizar_tipo_documento_con_nombre_duplicado()
    {
        $tipoDocumento1 = CatalogoTipoDocumento::factory()->create([
            'nombre_tipo_documento' => 'Tipo Original'
        ]);
        
        $tipoDocumento2 = CatalogoTipoDocumento::factory()->create([
            'nombre_tipo_documento' => 'Tipo Duplicado'
        ]);

        $updateData = [
            'nombre_tipo_documento' => 'Tipo Duplicado' // Nombre ya existente
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/catalogo-tipos-documento/{$tipoDocumento1->id}", $updateData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nombre_tipo_documento']);
    }

        public function test_puede_eliminar_tipo_documento_sin_documentos_asociados()
    {
        $tipoDocumento = CatalogoTipoDocumento::factory()->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/catalogo-tipos-documento/{$tipoDocumento->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Tipo de documento eliminado exitosamente'
            ]);

        $this->assertDatabaseMissing('catalogo_tipos_documento', [
            'id' => $tipoDocumento->id
        ]);
    }

        public function test_no_puede_eliminar_tipo_documento_con_documentos_asociados()
    {
        $tipoDocumento = CatalogoTipoDocumento::factory()->create();
        
        // Crear documento asociado
        Documento::factory()->create([
            'tipo_documento_id' => $tipoDocumento->id
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/catalogo-tipos-documento/{$tipoDocumento->id}");

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'No se puede eliminar el tipo de documento porque tiene documentos asociados'
            ]);

        $this->assertDatabaseHas('catalogo_tipos_documento', [
            'id' => $tipoDocumento->id
        ]);
    }

        public function test_puede_filtrar_tipos_por_si_requieren_vencimiento()
    {
        CatalogoTipoDocumento::factory()->count(2)->create([
            'requiere_vencimiento' => true
        ]);
        
        CatalogoTipoDocumento::factory()->count(3)->create([
            'requiere_vencimiento' => false
        ]);

        // Filtrar por tipos que requieren vencimiento
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/catalogo-tipos-documento?requiere_vencimiento=true');

        $response->assertStatus(200);
        
        $tipos = $response->json('data.data');
        $this->assertCount(2, $tipos);
        
        foreach ($tipos as $tipo) {
            $this->assertTrue($tipo['requiere_vencimiento']);
        }

        // Filtrar por tipos que NO requieren vencimiento
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/catalogo-tipos-documento?requiere_vencimiento=false');

        $response->assertStatus(200);
        
        $tipos = $response->json('data.data');
        $this->assertCount(3, $tipos);
        
        foreach ($tipos as $tipo) {
            $this->assertFalse($tipo['requiere_vencimiento']);
        }
    }

        public function test_puede_buscar_tipos_por_nombre()
    {
        CatalogoTipoDocumento::factory()->create([
            'nombre_tipo_documento' => 'Documento de Seguridad'
        ]);
        
        CatalogoTipoDocumento::factory()->create([
            'nombre_tipo_documento' => 'Factura de Compra'
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/catalogo-tipos-documento?search=seguridad');

        $response->assertStatus(200);
        
        $tipos = $response->json('data.data');
        $this->assertCount(1, $tipos);
        $this->assertStringContainsString('Seguridad', $tipos[0]['nombre_tipo_documento']);
    }

        public function test_puede_obtener_tipos_con_conteo_de_documentos()
    {
        $tipoDocumento = CatalogoTipoDocumento::factory()->create();
        
        // Crear documentos asociados
        Documento::factory()->count(3)->create([
            'tipo_documento_id' => $tipoDocumento->id
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/catalogo-tipos-documento?with_counts=true');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'nombre_tipo_documento',
                            'documentos_count'
                        ]
                    ]
                ]
            ]);

        $tipos = $response->json('data.data');
        $tipoEncontrado = collect($tipos)->firstWhere('id', $tipoDocumento->id);
        $this->assertEquals(3, $tipoEncontrado['documentos_count']);
    }

        public function test_puede_obtener_lista_sin_paginacion()
    {
        CatalogoTipoDocumento::factory()->count(5)->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/catalogo-tipos-documento?paginate=false');

        $response->assertStatus(200);
        
        // Verificar que no hay estructura de paginación
        $response->assertJsonMissing(['current_page', 'last_page', 'per_page']);
        
        $tipos = $response->json('data');
        $this->assertCount(5, $tipos);
    }

        public function test_puede_ordenar_tipos_por_diferentes_campos()
    {
        CatalogoTipoDocumento::factory()->create([
            'nombre_tipo_documento' => 'Z Último Tipo',
            'created_at' => now()->subDays(1)
        ]);
        
        CatalogoTipoDocumento::factory()->create([
            'nombre_tipo_documento' => 'A Primer Tipo',
            'created_at' => now()
        ]);

        // Ordenar por nombre ascendente
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/catalogo-tipos-documento?sort_by=nombre_tipo_documento&sort_order=asc');

        $response->assertStatus(200);
        
        $tipos = $response->json('data.data');
        $this->assertEquals('A Primer Tipo', $tipos[0]['nombre_tipo_documento']);
        $this->assertEquals('Z Último Tipo', $tipos[1]['nombre_tipo_documento']);

        // Ordenar por fecha de creación descendente
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/catalogo-tipos-documento?sort_by=created_at&sort_order=desc');

        $response->assertStatus(200);
        
        $tipos = $response->json('data.data');
        $this->assertEquals('A Primer Tipo', $tipos[0]['nombre_tipo_documento']);
    }
}
