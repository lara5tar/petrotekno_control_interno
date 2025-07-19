<?php

namespace Tests\Feature;

use App\Models\CatalogoTipoDocumento;
use App\Models\Documento;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentoControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $adminUser;

    private User $userWithoutPermissions;

    private Role $adminRole;

    private Role $userRole;

    private Role $noPermissionRole;

    private CatalogoTipoDocumento $tipoDocumento;

    private Vehiculo $vehiculo;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles y permisos
        $this->adminRole = Role::factory()->admin()->create();
        $this->userRole = Role::factory()->create(['nombre_rol' => 'Usuario']);
        $this->noPermissionRole = Role::factory()->create(['nombre_rol' => 'Sin Permisos']);

        $createPermission = Permission::factory()->withName('crear_documentos')->create();
        $viewPermission = Permission::factory()->withName('ver_documentos')->create();
        $editPermission = Permission::factory()->withName('editar_documentos')->create();
        $deletePermission = Permission::factory()->withName('eliminar_documentos')->create();

        $this->adminRole->permisos()->attach([
            $createPermission->id,
            $viewPermission->id,
            $editPermission->id,
            $deletePermission->id,
        ]);
        $this->userRole->permisos()->attach([$viewPermission->id]);
        // noPermissionRole no tiene permisos de documentos

        // Crear usuarios
        $this->adminUser = User::factory()->create(['rol_id' => $this->adminRole->id]);
        $this->userWithoutPermissions = User::factory()->create(['rol_id' => $this->noPermissionRole->id]);

        // Crear datos de prueba
        $this->tipoDocumento = CatalogoTipoDocumento::factory()->create();
        $this->vehiculo = Vehiculo::factory()->create();

        Storage::fake('public');
    }

    protected function tearDown(): void
    {
        // Limpiar todos los archivos del storage fake
        Storage::disk('public')->deleteDirectory('documentos');
        parent::tearDown();
    }

    public function test_usuario_sin_permisos_no_puede_acceder_a_documentos()
    {
        $response = $this->actingAs($this->userWithoutPermissions, 'sanctum')
            ->getJson('/api/documentos');

        $response->assertStatus(403);
    }

    public function test_usuario_con_permisos_puede_listar_documentos()
    {
        Documento::factory()->count(3)->create([
            'tipo_documento_id' => $this->tipoDocumento->id,
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/documentos');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'tipo_documento_id',
                            'descripcion',
                            'fecha_vencimiento',
                            'estado',
                            'tipo_documento',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                    'total',
                    'per_page',
                ],
            ]);
    }

    public function test_puede_crear_documento_con_datos_validos()
    {
        $documentoData = [
            'tipo_documento_id' => $this->tipoDocumento->id,
            'descripcion' => 'Documento de prueba',
            'fecha_vencimiento' => now()->addYear()->format('Y-m-d'),
            'vehiculo_id' => $this->vehiculo->id,
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/documentos', $documentoData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'tipo_documento_id',
                    'descripcion',
                    'fecha_vencimiento',
                    'vehiculo_id',
                    'estado',
                    'dias_hasta_vencimiento',
                ],
            ]);

        $this->assertDatabaseHas('documentos', [
            'tipo_documento_id' => $this->tipoDocumento->id,
            'descripcion' => 'Documento de prueba',
            'vehiculo_id' => $this->vehiculo->id,
        ]);
    }

    public function test_puede_crear_documento_con_archivo()
    {
        $archivo = UploadedFile::fake()->create('documento.pdf', 1024, 'application/pdf');

        $documentoData = [
            'tipo_documento_id' => $this->tipoDocumento->id,
            'descripcion' => 'Documento con archivo',
            'fecha_vencimiento' => now()->addYear()->format('Y-m-d'),
            'archivo' => $archivo,
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/documentos', $documentoData);

        $response->assertStatus(201);

        // Verificar que el archivo se guardó
        $documento = Documento::latest()->first();
        $this->assertNotNull($documento->ruta_archivo);
        Storage::disk('public')->assertExists($documento->ruta_archivo);
    }

    public function test_no_puede_crear_documento_sin_tipo_documento()
    {
        $documentoData = [
            'descripcion' => 'Documento sin tipo',
            'vehiculo_id' => $this->vehiculo->id,
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/documentos', $documentoData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['tipo_documento_id']);
    }

    public function test_no_puede_crear_documento_con_fecha_vencimiento_pasada()
    {
        $documentoData = [
            'tipo_documento_id' => $this->tipoDocumento->id,
            'descripcion' => 'Documento con fecha pasada',
            'fecha_vencimiento' => now()->subDays(1)->format('Y-m-d'),
            'vehiculo_id' => $this->vehiculo->id,
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/documentos', $documentoData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['fecha_vencimiento']);
    }

    public function test_requiere_fecha_vencimiento_cuando_tipo_lo_requiere()
    {
        // Crear tipo específico que sí requiera vencimiento
        $tipoQueRequiereVencimiento = CatalogoTipoDocumento::factory()->create([
            'requiere_vencimiento' => true,
        ]);

        $documentoData = [
            'tipo_documento_id' => $tipoQueRequiereVencimiento->id,
            'descripcion' => 'Documento sin fecha de vencimiento requerida',
            'vehiculo_id' => $this->vehiculo->id,
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/documentos', $documentoData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['fecha_vencimiento']);
    }

    public function test_puede_obtener_documento_especifico()
    {
        $documento = Documento::factory()->create([
            'tipo_documento_id' => $this->tipoDocumento->id,
            'vehiculo_id' => $this->vehiculo->id,
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson("/api/documentos/{$documento->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $documento->id,
                    'tipo_documento_id' => $this->tipoDocumento->id,
                    'vehiculo_id' => $this->vehiculo->id,
                ],
            ]);
    }

    public function test_puede_actualizar_documento()
    {
        $documento = Documento::factory()->create([
            'tipo_documento_id' => $this->tipoDocumento->id,
            'descripcion' => 'Descripción original',
        ]);

        $updateData = [
            'descripcion' => 'Descripción actualizada',
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/documentos/{$documento->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Documento actualizado exitosamente',
            ]);

        $this->assertDatabaseHas('documentos', [
            'id' => $documento->id,
            'descripcion' => 'Descripción actualizada',
        ]);
    }

    public function test_puede_eliminar_documento()
    {
        $documento = Documento::factory()->create([
            'tipo_documento_id' => $this->tipoDocumento->id,
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/documentos/{$documento->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Documento eliminado exitosamente',
            ]);

        $this->assertSoftDeleted('documentos', ['id' => $documento->id]);
    }

    public function test_puede_filtrar_documentos_por_tipo()
    {
        $tipoDocumento2 = CatalogoTipoDocumento::factory()->create();

        Documento::factory()->count(2)->create([
            'tipo_documento_id' => $this->tipoDocumento->id,
        ]);

        Documento::factory()->create([
            'tipo_documento_id' => $tipoDocumento2->id,
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson("/api/documentos?tipo_documento_id={$this->tipoDocumento->id}");

        $response->assertStatus(200);

        $documentos = $response->json('data.data');
        $this->assertCount(2, $documentos);

        foreach ($documentos as $documento) {
            $this->assertEquals($this->tipoDocumento->id, $documento['tipo_documento_id']);
        }
    }

    public function test_puede_filtrar_documentos_por_vehiculo()
    {
        $vehiculo2 = Vehiculo::factory()->create();

        Documento::factory()->count(2)->create([
            'tipo_documento_id' => $this->tipoDocumento->id,
            'vehiculo_id' => $this->vehiculo->id,
        ]);

        Documento::factory()->create([
            'tipo_documento_id' => $this->tipoDocumento->id,
            'vehiculo_id' => $vehiculo2->id,
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson("/api/documentos?vehiculo_id={$this->vehiculo->id}");

        $response->assertStatus(200);

        $documentos = $response->json('data.data');
        $this->assertCount(2, $documentos);

        foreach ($documentos as $documento) {
            $this->assertEquals($this->vehiculo->id, $documento['vehiculo_id']);
        }
    }

    public function test_puede_obtener_documentos_proximos_a_vencer()
    {
        // Documento que vence en 15 días
        Documento::factory()->create([
            'tipo_documento_id' => $this->tipoDocumento->id,
            'fecha_vencimiento' => now()->addDays(15),
        ]);

        // Documento que vence en 45 días
        Documento::factory()->create([
            'tipo_documento_id' => $this->tipoDocumento->id,
            'fecha_vencimiento' => now()->addDays(45),
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/documentos/proximos-a-vencer?dias=30');

        $response->assertStatus(200);

        $documentos = $response->json('data');
        $this->assertCount(1, $documentos); // Solo el que vence en 15 días
    }

    public function test_puede_obtener_documentos_vencidos()
    {
        // Documento vencido
        Documento::factory()->create([
            'tipo_documento_id' => $this->tipoDocumento->id,
            'fecha_vencimiento' => now()->subDays(5),
        ]);

        // Documento vigente
        Documento::factory()->create([
            'tipo_documento_id' => $this->tipoDocumento->id,
            'fecha_vencimiento' => now()->addDays(30),
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/documentos/vencidos');

        $response->assertStatus(200);

        $documentos = $response->json('data');
        $this->assertCount(1, $documentos); // Solo el vencido
    }

    public function test_puede_buscar_documentos_por_descripcion()
    {
        Documento::factory()->create([
            'tipo_documento_id' => $this->tipoDocumento->id,
            'descripcion' => 'Documento importante de seguridad',
        ]);

        Documento::factory()->create([
            'tipo_documento_id' => $this->tipoDocumento->id,
            'descripcion' => 'Factura de compra',
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/documentos?search=seguridad');

        $response->assertStatus(200);

        $documentos = $response->json('data.data');
        $this->assertCount(1, $documentos);
        $this->assertStringContainsString('seguridad', $documentos[0]['descripcion']);
    }

    public function test_elimina_archivo_al_actualizar_documento_con_nuevo_archivo()
    {
        // Asegurar que el storage esté limpio
        Storage::disk('public')->deleteDirectory('documentos');

        // Crear documento con archivo
        Storage::disk('public')->put('documentos/archivo_original.pdf', 'contenido original');

        $documento = Documento::factory()->create([
            'tipo_documento_id' => $this->tipoDocumento->id,
            'ruta_archivo' => 'documentos/archivo_original.pdf',
        ]);

        $nuevoArchivo = UploadedFile::fake()->create('nuevo_archivo.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/documentos/{$documento->id}", [
                'archivo' => $nuevoArchivo,
            ]);

        $response->assertStatus(200);

        // Verificar que se eliminó el archivo original
        Storage::disk('public')->assertMissing('documentos/archivo_original.pdf');

        // Verificar que existe el nuevo archivo
        $documento->refresh();
        $this->assertNotEquals('documentos/archivo_original.pdf', $documento->ruta_archivo);
        Storage::disk('public')->assertExists($documento->ruta_archivo);
    }

    public function test_elimina_archivo_al_eliminar_documento()
    {
        // Asegurar que el storage esté limpio
        Storage::disk('public')->deleteDirectory('documentos');

        // Crear documento con archivo
        Storage::disk('public')->put('documentos/archivo_a_eliminar.pdf', 'contenido');

        $documento = Documento::factory()->create([
            'tipo_documento_id' => $this->tipoDocumento->id,
            'ruta_archivo' => 'documentos/archivo_a_eliminar.pdf',
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/documentos/{$documento->id}");

        $response->assertStatus(200);

        // Verificar que se eliminó el archivo
        Storage::disk('public')->assertMissing('documentos/archivo_a_eliminar.pdf');
    }
}
