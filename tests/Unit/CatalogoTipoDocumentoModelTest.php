<?php

namespace Tests\Unit;

use App\Models\CatalogoTipoDocumento;
use App\Models\Documento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogoTipoDocumentoModelTest extends TestCase
{
    use RefreshDatabase;

        public function test_catalogo_tipo_documento_tiene_muchos_documentos()
    {
        $tipoDocumento = CatalogoTipoDocumento::factory()->create();
        
        $documento1 = Documento::factory()->create([
            'tipo_documento_id' => $tipoDocumento->id
        ]);
        
        $documento2 = Documento::factory()->create([
            'tipo_documento_id' => $tipoDocumento->id
        ]);

        $this->assertCount(2, $tipoDocumento->documentos);
        $this->assertTrue($tipoDocumento->documentos->contains($documento1));
        $this->assertTrue($tipoDocumento->documentos->contains($documento2));
    }

        public function test_scope_que_requieren_vencimiento_filtra_correctamente()
    {
        $tipoConVencimiento = CatalogoTipoDocumento::factory()->create([
            'requiere_vencimiento' => true
        ]);
        
        $tipoSinVencimiento = CatalogoTipoDocumento::factory()->create([
            'requiere_vencimiento' => false
        ]);

        $tiposQueRequieren = CatalogoTipoDocumento::queRequierenVencimiento()->get();

        $this->assertTrue($tiposQueRequieren->contains($tipoConVencimiento));
        $this->assertFalse($tiposQueRequieren->contains($tipoSinVencimiento));
    }

        public function test_scope_que_no_requieren_vencimiento_filtra_correctamente()
    {
        $tipoConVencimiento = CatalogoTipoDocumento::factory()->create([
            'requiere_vencimiento' => true
        ]);
        
        $tipoSinVencimiento = CatalogoTipoDocumento::factory()->create([
            'requiere_vencimiento' => false
        ]);

        $tiposQueNoRequieren = CatalogoTipoDocumento::queNoRequierenVencimiento()->get();

        $this->assertFalse($tiposQueNoRequieren->contains($tipoConVencimiento));
        $this->assertTrue($tiposQueNoRequieren->contains($tipoSinVencimiento));
    }

        public function test_requiere_vencimiento_se_castea_como_boolean()
    {
        $tipoDocumento = CatalogoTipoDocumento::factory()->create([
            'requiere_vencimiento' => 1
        ]);

        $this->assertIsBool($tipoDocumento->requiere_vencimiento);
        $this->assertTrue($tipoDocumento->requiere_vencimiento);

        $tipoDocumento->update(['requiere_vencimiento' => 0]);
        $tipoDocumento->refresh();

        $this->assertIsBool($tipoDocumento->requiere_vencimiento);
        $this->assertFalse($tipoDocumento->requiere_vencimiento);
    }

        public function test_fillable_attributes_estan_configurados_correctamente()
    {
        $tipoDocumento = new CatalogoTipoDocumento();
        
        $expectedFillable = [
            'nombre_tipo_documento',
            'descripcion',
            'requiere_vencimiento',
        ];

        $this->assertEquals($expectedFillable, $tipoDocumento->getFillable());
    }

        public function test_tabla_personalizada_esta_configurada()
    {
        $tipoDocumento = new CatalogoTipoDocumento();
        
        $this->assertEquals('catalogo_tipos_documento', $tipoDocumento->getTable());
    }

        public function test_cast_attributes_estan_configurados()
    {
        $tipoDocumento = new CatalogoTipoDocumento();
        
        $expectedCasts = [
            'id' => 'int',
            'requiere_vencimiento' => 'boolean',
        ];

        $this->assertEquals($expectedCasts, $tipoDocumento->getCasts());
    }

        public function test_puede_crear_tipo_documento_con_factory()
    {
        $tipoDocumento = CatalogoTipoDocumento::factory()->create([
            'nombre_tipo_documento' => 'Tipo Test',
            'descripcion' => 'Descripción test',
            'requiere_vencimiento' => true
        ]);

        $this->assertDatabaseHas('catalogo_tipos_documento', [
            'nombre_tipo_documento' => 'Tipo Test',
            'descripcion' => 'Descripción test',
            'requiere_vencimiento' => true
        ]);
    }

        public function test_nombre_tipo_documento_debe_ser_unico()
    {
        CatalogoTipoDocumento::factory()->create([
            'nombre_tipo_documento' => 'Tipo Único'
        ]);

        $this->expectException(\Illuminate\Database\UniqueConstraintViolationException::class);

        CatalogoTipoDocumento::factory()->create([
            'nombre_tipo_documento' => 'Tipo Único'
        ]);
    }

        public function test_puede_obtener_conteo_de_documentos_relacionados()
    {
        $tipoDocumento = CatalogoTipoDocumento::factory()->create();
        
        // Crear 3 documentos relacionados
        Documento::factory()->count(3)->create([
            'tipo_documento_id' => $tipoDocumento->id
        ]);

        $tipoConConteo = CatalogoTipoDocumento::withCount('documentos')
            ->find($tipoDocumento->id);

        $this->assertEquals(3, $tipoConConteo->documentos_count);
    }

        public function test_relacion_documentos_incluye_soft_deleted()
    {
        $tipoDocumento = CatalogoTipoDocumento::factory()->create();
        
        $documento1 = Documento::factory()->create([
            'tipo_documento_id' => $tipoDocumento->id
        ]);
        
        $documento2 = Documento::factory()->create([
            'tipo_documento_id' => $tipoDocumento->id
        ]);

        // Eliminar un documento (soft delete)
        $documento1->delete();

        // Verificar que solo trae documentos activos por defecto
        $this->assertCount(1, $tipoDocumento->documentos);
        $this->assertTrue($tipoDocumento->documentos->contains($documento2));
        $this->assertFalse($tipoDocumento->documentos->contains($documento1));

        // Verificar que puede traer todos incluyendo eliminados
        $this->assertCount(2, $tipoDocumento->documentos()->withTrashed()->get());
    }

        public function test_puede_determinar_si_tiene_documentos_asociados()
    {
        $tipoSinDocumentos = CatalogoTipoDocumento::factory()->create();
        $tipoConDocumentos = CatalogoTipoDocumento::factory()->create();
        
        Documento::factory()->create([
            'tipo_documento_id' => $tipoConDocumentos->id
        ]);

        $this->assertFalse($tipoSinDocumentos->documentos()->exists());
        $this->assertTrue($tipoConDocumentos->documentos()->exists());
    }

        public function test_descripcion_puede_ser_null()
    {
        $tipoDocumento = CatalogoTipoDocumento::factory()->create([
            'descripcion' => null
        ]);

        $this->assertNull($tipoDocumento->descripcion);
        $this->assertDatabaseHas('catalogo_tipos_documento', [
            'id' => $tipoDocumento->id,
            'descripcion' => null
        ]);
    }

        public function test_requiere_vencimiento_tiene_valor_por_defecto_false()
    {
        // Crear usando create directo (sin factory)
        $tipoDocumento = CatalogoTipoDocumento::create([
            'nombre_tipo_documento' => 'Tipo Sin Vencimiento Default'
        ]);

        $this->assertFalse($tipoDocumento->requiere_vencimiento);
    }
}
