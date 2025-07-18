<?php

namespace Tests\Feature;

use App\Models\CatalogoTipoDocumento;
use App\Models\Obra;
use App\Models\Permission;
use App\Models\Personal;
use App\Models\Role;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentoValidationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    private CatalogoTipoDocumento $tipoDocumento;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear usuario con permisos
        $role = Role::factory()->create();
        $permission = Permission::factory()->withName('crear_documentos')->create();
        $role->permisos()->attach($permission->id);

        $this->user = User::factory()->create(['rol_id' => $role->id]);

        // Crear tipo de documento que NO requiera vencimiento para la mayoría de tests
        $this->tipoDocumento = CatalogoTipoDocumento::factory()->create([
            'requiere_vencimiento' => false,
        ]);

        Storage::fake('public');
    }

    public function test_valida_tipo_documento_id_requerido()
    {
        $data = [
            'descripcion' => 'Test documento',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/documentos', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['tipo_documento_id']);
    }

    public function test_valida_tipo_documento_id_existe()
    {
        $data = [
            'tipo_documento_id' => 99999, // ID que no existe
            'descripcion' => 'Test documento',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/documentos', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['tipo_documento_id']);
    }

    public function test_valida_descripcion_longitud_maxima()
    {
        $data = [
            'tipo_documento_id' => $this->tipoDocumento->id,
            'descripcion' => str_repeat('a', 1001), // Más de 1000 caracteres
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/documentos', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['descripcion']);
    }

    public function test_acepta_descripcion_de_1000_caracteres()
    {
        $data = [
            'tipo_documento_id' => $this->tipoDocumento->id,
            'descripcion' => str_repeat('a', 1000), // Exactamente 1000 caracteres
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/documentos', $data);

        $response->assertStatus(201);
    }

    public function test_valida_ruta_archivo_longitud_maxima()
    {
        $data = [
            'tipo_documento_id' => $this->tipoDocumento->id,
            'ruta_archivo' => str_repeat('a', 501), // Más de 500 caracteres
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/documentos', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['ruta_archivo']);
    }

    public function test_valida_fecha_vencimiento_no_puede_ser_pasada()
    {
        $data = [
            'tipo_documento_id' => $this->tipoDocumento->id,
            'fecha_vencimiento' => now()->subDay()->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/documentos', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['fecha_vencimiento']);
    }

    public function test_acepta_fecha_vencimiento_hoy()
    {
        $data = [
            'tipo_documento_id' => $this->tipoDocumento->id,
            'fecha_vencimiento' => now()->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/documentos', $data);

        $response->assertStatus(201);
    }

    public function test_valida_vehiculo_id_existe()
    {
        $data = [
            'tipo_documento_id' => $this->tipoDocumento->id,
            'vehiculo_id' => 99999, // ID que no existe
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/documentos', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['vehiculo_id']);
    }

    public function test_valida_personal_id_existe()
    {
        $data = [
            'tipo_documento_id' => $this->tipoDocumento->id,
            'personal_id' => 99999, // ID que no existe
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/documentos', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['personal_id']);
    }

    public function test_valida_obra_id_existe()
    {
        $data = [
            'tipo_documento_id' => $this->tipoDocumento->id,
            'obra_id' => 99999, // ID que no existe
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/documentos', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['obra_id']);
    }

    public function test_acepta_entidades_opcionales_validas()
    {
        $vehiculo = Vehiculo::factory()->create();

        $data = [
            'tipo_documento_id' => $this->tipoDocumento->id,
            'vehiculo_id' => $vehiculo->id,
            'descripcion' => 'Documento con entidad válida',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/documentos', $data);

        $response->assertStatus(201);
    }

    public function test_rechaza_multiples_entidades_asociadas()
    {
        $vehiculo = Vehiculo::factory()->create();
        $personal = Personal::factory()->create();
        $obra = Obra::factory()->create();

        $data = [
            'tipo_documento_id' => $this->tipoDocumento->id,
            'vehiculo_id' => $vehiculo->id,
            'personal_id' => $personal->id,
            'obra_id' => $obra->id,
            'descripcion' => 'Documento con múltiples entidades',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/documentos', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['multiple_associations']);
    }

    public function test_valida_archivo_tamaño_maximo()
    {
        $archivo = UploadedFile::fake()->create('documento.pdf', 10241); // Más de 10MB

        $data = [
            'tipo_documento_id' => $this->tipoDocumento->id,
            'archivo' => $archivo,
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/documentos', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['archivo']);
    }

    public function test_acepta_archivo_de_10mb_exactos()
    {
        $archivo = UploadedFile::fake()->create('documento.pdf', 10240); // Exactamente 10MB

        $data = [
            'tipo_documento_id' => $this->tipoDocumento->id,
            'archivo' => $archivo,
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/documentos', $data);

        $response->assertStatus(201);
    }

    public function test_valida_tipos_de_archivo_permitidos()
    {
        $archivosPermitidos = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'txt', 'xls', 'xlsx'];

        foreach ($archivosPermitidos as $extension) {
            $archivo = UploadedFile::fake()->create("documento.{$extension}", 1024);

            $data = [
                'tipo_documento_id' => $this->tipoDocumento->id,
                'archivo' => $archivo,
            ];

            $response = $this->actingAs($this->user, 'sanctum')
                ->postJson('/api/documentos', $data);

            $response->assertStatus(201, "Archivo .{$extension} debería ser aceptado");
        }
    }

    public function test_rechaza_tipos_de_archivo_no_permitidos()
    {
        $archivosNoPermitidos = ['exe', 'bat', 'sh', 'zip', 'rar'];

        foreach ($archivosNoPermitidos as $extension) {
            $archivo = UploadedFile::fake()->create("archivo.{$extension}", 1024);

            $data = [
                'tipo_documento_id' => $this->tipoDocumento->id,
                'archivo' => $archivo,
            ];

            $response = $this->actingAs($this->user, 'sanctum')
                ->postJson('/api/documentos', $data);

            $response->assertStatus(422, "Archivo .{$extension} debería ser rechazado")
                ->assertJsonValidationErrors(['archivo']);
        }
    }

    public function test_requiere_fecha_vencimiento_cuando_tipo_lo_requiere()
    {
        $tipoQueRequiere = CatalogoTipoDocumento::factory()->create([
            'requiere_vencimiento' => true,
        ]);

        $data = [
            'tipo_documento_id' => $tipoQueRequiere->id,
            'descripcion' => 'Documento sin fecha pero que la requiere',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/documentos', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['fecha_vencimiento']);
    }

    public function test_no_requiere_fecha_vencimiento_cuando_tipo_no_lo_requiere()
    {
        $tipoQueNoRequiere = CatalogoTipoDocumento::factory()->create([
            'requiere_vencimiento' => false,
        ]);

        $data = [
            'tipo_documento_id' => $tipoQueNoRequiere->id,
            'descripcion' => 'Documento sin fecha y no la requiere',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/documentos', $data);

        $response->assertStatus(201);
    }

    public function test_valida_formato_fecha_vencimiento()
    {
        $fechasInvalidas = [
            '31/12/2025', // Formato DD/MM/YYYY
            '2025-13-01', // Mes inválido
            '2025-12-32', // Día inválido
            'no-es-fecha', // No es fecha
        ];

        foreach ($fechasInvalidas as $fecha) {
            $data = [
                'tipo_documento_id' => $this->tipoDocumento->id,
                'fecha_vencimiento' => $fecha,
            ];

            $response = $this->actingAs($this->user, 'sanctum')
                ->postJson('/api/documentos', $data);

            $response->assertStatus(422, "Fecha {$fecha} debería ser inválida")
                ->assertJsonValidationErrors(['fecha_vencimiento']);
        }
    }

    public function test_acepta_formatos_fecha_validos()
    {
        $fechasValidas = [
            now()->addDays(1)->format('Y-m-d'),
            now()->addMonths(1)->format('Y-m-d'),
            now()->addYears(1)->format('Y-m-d'),
        ];

        foreach ($fechasValidas as $fecha) {
            $data = [
                'tipo_documento_id' => $this->tipoDocumento->id,
                'fecha_vencimiento' => $fecha,
                'descripcion' => "Documento con fecha {$fecha}",
            ];

            $response = $this->actingAs($this->user, 'sanctum')
                ->postJson('/api/documentos', $data);

            $response->assertStatus(201, "Fecha {$fecha} debería ser válida");
        }
    }

    public function test_valida_que_archivo_sea_archivo_real()
    {
        $data = [
            'tipo_documento_id' => $this->tipoDocumento->id,
            'archivo' => 'no-es-un-archivo',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/documentos', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['archivo']);
    }

    public function test_valida_limites_numericos_de_ids()
    {
        $idsInvalidos = [
            'no-es-numero',
            -1,
            0,
            1.5, // Decimal
            '1a', // Alfanumérico
        ];

        foreach ($idsInvalidos as $id) {
            $data = [
                'tipo_documento_id' => $id,
            ];

            $response = $this->actingAs($this->user, 'sanctum')
                ->postJson('/api/documentos', $data);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['tipo_documento_id']);
        }
    }
}
