<?php

namespace Tests\Unit;

use App\Models\CatalogoTipoDocumento;
use App\Models\Documento;
use App\Models\Obra;
use App\Models\Personal;
use App\Models\Vehiculo;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentoModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_documento_pertenece_a_tipo_documento()
    {
        $tipoDocumento = CatalogoTipoDocumento::factory()->create();
        $documento = Documento::factory()->create([
            'tipo_documento_id' => $tipoDocumento->id,
        ]);

        $this->assertInstanceOf(CatalogoTipoDocumento::class, $documento->tipoDocumento);
        $this->assertEquals($tipoDocumento->id, $documento->tipoDocumento->id);
    }

    public function test_documento_pertenece_a_vehiculo_opcionalmente()
    {
        $vehiculo = Vehiculo::factory()->create();
        $documento = Documento::factory()->create([
            'vehiculo_id' => $vehiculo->id,
        ]);

        $this->assertInstanceOf(Vehiculo::class, $documento->vehiculo);
        $this->assertEquals($vehiculo->id, $documento->vehiculo->id);

        // Probar documento sin vehículo
        $documentoSinVehiculo = Documento::factory()->create([
            'vehiculo_id' => null,
        ]);
        $this->assertNull($documentoSinVehiculo->vehiculo);
    }

    public function test_documento_pertenece_a_personal_opcionalmente()
    {
        $personal = Personal::factory()->create();
        $documento = Documento::factory()->create([
            'personal_id' => $personal->id,
        ]);

        $this->assertInstanceOf(Personal::class, $documento->personal);
        $this->assertEquals($personal->id, $documento->personal->id);
    }

    public function test_documento_pertenece_a_obra_opcionalmente()
    {
        $obra = Obra::factory()->create();
        $documento = Documento::factory()->create([
            'obra_id' => $obra->id,
        ]);

        $this->assertInstanceOf(Obra::class, $documento->obra);
        $this->assertEquals($obra->id, $documento->obra->id);
    }

    public function test_scope_vencidos_filtra_documentos_vencidos()
    {
        $tipoDocumento = CatalogoTipoDocumento::factory()->create();

        // Documento vencido
        $documentoVencido = Documento::factory()->create([
            'tipo_documento_id' => $tipoDocumento->id,
            'fecha_vencimiento' => now()->subDays(5),
        ]);

        // Documento vigente
        $documentoVigente = Documento::factory()->create([
            'tipo_documento_id' => $tipoDocumento->id,
            'fecha_vencimiento' => now()->addDays(30),
        ]);

        // Documento sin fecha de vencimiento
        $documentoSinFecha = Documento::factory()->create([
            'tipo_documento_id' => $tipoDocumento->id,
            'fecha_vencimiento' => null,
        ]);

        $documentosVencidos = Documento::vencidos()->get();

        $this->assertTrue($documentosVencidos->contains($documentoVencido));
        $this->assertFalse($documentosVencidos->contains($documentoVigente));
        $this->assertFalse($documentosVencidos->contains($documentoSinFecha));
    }

    public function test_scope_proximos_a_vencer_filtra_correctamente()
    {
        $tipoDocumento = CatalogoTipoDocumento::factory()->create();

        // Documento que vence en 15 días
        $documentoProximo = Documento::factory()->create([
            'tipo_documento_id' => $tipoDocumento->id,
            'fecha_vencimiento' => now()->addDays(15),
        ]);

        // Documento que vence en 45 días
        $documentoLejano = Documento::factory()->create([
            'tipo_documento_id' => $tipoDocumento->id,
            'fecha_vencimiento' => now()->addDays(45),
        ]);

        // Documento ya vencido
        $documentoVencido = Documento::factory()->create([
            'tipo_documento_id' => $tipoDocumento->id,
            'fecha_vencimiento' => now()->subDays(1),
        ]);

        $documentosProximos = Documento::proximosAVencer(30)->get();

        $this->assertTrue($documentosProximos->contains($documentoProximo));
        $this->assertFalse($documentosProximos->contains($documentoLejano));
        $this->assertFalse($documentosProximos->contains($documentoVencido));
    }

    public function test_scope_por_tipo_filtra_por_tipo_documento()
    {
        $tipoDocumento1 = CatalogoTipoDocumento::factory()->create();
        $tipoDocumento2 = CatalogoTipoDocumento::factory()->create();

        $documento1 = Documento::factory()->create([
            'tipo_documento_id' => $tipoDocumento1->id,
        ]);

        $documento2 = Documento::factory()->create([
            'tipo_documento_id' => $tipoDocumento2->id,
        ]);

        $documentosTipo1 = Documento::porTipo($tipoDocumento1->id)->get();

        $this->assertTrue($documentosTipo1->contains($documento1));
        $this->assertFalse($documentosTipo1->contains($documento2));
    }

    public function test_scope_de_vehiculo_filtra_por_vehiculo()
    {
        $vehiculo1 = Vehiculo::factory()->create();
        $vehiculo2 = Vehiculo::factory()->create();

        $documento1 = Documento::factory()->create(['vehiculo_id' => $vehiculo1->id]);
        $documento2 = Documento::factory()->create(['vehiculo_id' => $vehiculo2->id]);
        $documento3 = Documento::factory()->create(['vehiculo_id' => null]);

        $documentosVehiculo1 = Documento::deVehiculo($vehiculo1->id)->get();

        $this->assertTrue($documentosVehiculo1->contains($documento1));
        $this->assertFalse($documentosVehiculo1->contains($documento2));
        $this->assertFalse($documentosVehiculo1->contains($documento3));
    }

    public function test_accessor_esta_vencido_calcula_correctamente()
    {
        $tipoDocumento = CatalogoTipoDocumento::factory()->create();

        // Documento vencido
        $documentoVencido = Documento::factory()->create([
            'tipo_documento_id' => $tipoDocumento->id,
            'fecha_vencimiento' => now()->subDays(1),
        ]);

        // Documento vigente
        $documentoVigente = Documento::factory()->create([
            'tipo_documento_id' => $tipoDocumento->id,
            'fecha_vencimiento' => now()->addDays(30),
        ]);

        // Documento sin fecha de vencimiento
        $documentoSinFecha = Documento::factory()->create([
            'tipo_documento_id' => $tipoDocumento->id,
            'fecha_vencimiento' => null,
        ]);

        $this->assertTrue($documentoVencido->esta_vencido);
        $this->assertFalse($documentoVigente->esta_vencido);
        $this->assertFalse($documentoSinFecha->esta_vencido);
    }

    public function test_accessor_dias_hasta_vencimiento_calcula_correctamente()
    {
        $tipoDocumento = CatalogoTipoDocumento::factory()->create();

        // Documento que vence en 15 días
        $fechaFutura = now()->addDays(15)->startOfDay();
        $documentoFuturo = Documento::factory()->create([
            'tipo_documento_id' => $tipoDocumento->id,
            'fecha_vencimiento' => $fechaFutura,
        ]);

        // Documento vencido hace 5 días
        $fechaPasada = now()->subDays(5)->startOfDay();
        $documentoVencido = Documento::factory()->create([
            'tipo_documento_id' => $tipoDocumento->id,
            'fecha_vencimiento' => $fechaPasada,
        ]);

        // Documento sin fecha
        $documentoSinFecha = Documento::factory()->create([
            'tipo_documento_id' => $tipoDocumento->id,
            'fecha_vencimiento' => null,
        ]);

        // Usar rango aceptable para evitar problemas de timing
        $this->assertGreaterThanOrEqual(14, $documentoFuturo->dias_hasta_vencimiento);
        $this->assertLessThanOrEqual(15, $documentoFuturo->dias_hasta_vencimiento);

        $this->assertGreaterThanOrEqual(-6, $documentoVencido->dias_hasta_vencimiento);
        $this->assertLessThanOrEqual(-4, $documentoVencido->dias_hasta_vencimiento);

        $this->assertNull($documentoSinFecha->dias_hasta_vencimiento);
    }

    public function test_accessor_estado_determina_estado_correctamente()
    {
        $tipoDocumento = CatalogoTipoDocumento::factory()->create();

        // Documento vigente (más de 30 días)
        $documentoVigente = Documento::factory()->create([
            'tipo_documento_id' => $tipoDocumento->id,
            'fecha_vencimiento' => now()->addDays(45),
        ]);

        // Documento próximo a vencer (menos de 30 días)
        $documentoProximo = Documento::factory()->create([
            'tipo_documento_id' => $tipoDocumento->id,
            'fecha_vencimiento' => now()->addDays(15),
        ]);

        // Documento vencido
        $documentoVencido = Documento::factory()->create([
            'tipo_documento_id' => $tipoDocumento->id,
            'fecha_vencimiento' => now()->subDays(1),
        ]);

        // Documento sin fecha
        $documentoSinFecha = Documento::factory()->create([
            'tipo_documento_id' => $tipoDocumento->id,
            'fecha_vencimiento' => null,
        ]);

        $this->assertEquals('vigente', $documentoVigente->estado);
        $this->assertEquals('proximo_a_vencer', $documentoProximo->estado);
        $this->assertEquals('vencido', $documentoVencido->estado);
        $this->assertEquals('vigente', $documentoSinFecha->estado);
    }

    public function test_documento_usa_soft_deletes()
    {
        $documento = Documento::factory()->create();
        $documentoId = $documento->id;

        // Eliminar (soft delete)
        $documento->delete();

        // Verificar que no aparece en consultas normales
        $this->assertNull(Documento::find($documentoId));

        // Verificar que existe en consultas con trashed
        $this->assertNotNull(Documento::withTrashed()->find($documentoId));
        $this->assertTrue(Documento::withTrashed()->find($documentoId)->trashed());
    }

    public function test_documento_cast_fecha_vencimiento_como_date()
    {
        $fecha = '2025-12-31';
        $documento = Documento::factory()->create([
            'fecha_vencimiento' => $fecha,
        ]);

        $this->assertInstanceOf(Carbon::class, $documento->fecha_vencimiento);
        $this->assertEquals('2025-12-31', $documento->fecha_vencimiento->format('Y-m-d'));
    }

    public function test_documento_fillable_attributes_estan_configurados()
    {
        $documento = new Documento;

        $expectedFillable = [
            'tipo_documento_id',
            'descripcion',
            'ruta_archivo',
            'fecha_vencimiento',
            'vehiculo_id',
            'personal_id',
            'obra_id',
            'mantenimiento_id',
        ];

        $this->assertEquals($expectedFillable, $documento->getFillable());
    }
}
