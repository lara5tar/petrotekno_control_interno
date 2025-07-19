<?php

namespace Tests\Unit;

use App\Models\CatalogoTipoServicio;
use App\Models\Mantenimiento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CatalogoTipoServicioModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function puede_crear_catalogo_tipo_servicio()
    {
        $tipoServicio = CatalogoTipoServicio::create([
            'nombre_tipo_servicio' => 'Mantenimiento Preventivo',
        ]);

        $this->assertDatabaseHas('catalogo_tipos_servicio', [
            'nombre_tipo_servicio' => 'Mantenimiento Preventivo',
        ]);
    }

    #[Test]
    public function relacion_con_mantenimientos_funciona_correctamente()
    {
        $tipoServicio = CatalogoTipoServicio::factory()->create();

        $mantenimiento1 = Mantenimiento::factory()->create(['tipo_servicio_id' => $tipoServicio->id]);
        $mantenimiento2 = Mantenimiento::factory()->create(['tipo_servicio_id' => $tipoServicio->id]);

        $tipoServicio = $tipoServicio->fresh();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $tipoServicio->mantenimientos);
        $this->assertCount(2, $tipoServicio->mantenimientos);
        $this->assertTrue($tipoServicio->mantenimientos->contains($mantenimiento1));
        $this->assertTrue($tipoServicio->mantenimientos->contains($mantenimiento2));
    }

    #[Test]
    public function fillable_permite_campos_correctos()
    {
        $tipoServicio = new CatalogoTipoServicio();

        $expectedFillable = [
            'nombre_tipo_servicio',
        ];

        $this->assertEquals($expectedFillable, $tipoServicio->getFillable());
    }

    #[Test]
    public function tabla_es_catalogo_tipos_servicio()
    {
        $tipoServicio = new CatalogoTipoServicio();

        $this->assertEquals('catalogo_tipos_servicio', $tipoServicio->getTable());
    }

    #[Test]
    public function modelo_usa_timestamps()
    {
        $tipoServicio = new CatalogoTipoServicio();

        $this->assertTrue($tipoServicio->usesTimestamps());
    }

    #[Test]
    public function puede_actualizar_tipo_servicio()
    {
        $tipoServicio = CatalogoTipoServicio::factory()->create([
            'nombre_tipo_servicio' => 'Servicio Original',
        ]);

        $tipoServicio->update([
            'nombre_tipo_servicio' => 'Servicio Actualizado',
        ]);

        $this->assertDatabaseHas('catalogo_tipos_servicio', [
            'id' => $tipoServicio->id,
            'nombre_tipo_servicio' => 'Servicio Actualizado',
        ]);
    }

    #[Test]
    public function puede_eliminar_tipo_servicio()
    {
        $tipoServicio = CatalogoTipoServicio::factory()->create();
        $tipoServicioId = $tipoServicio->id;

        $tipoServicio->delete();

        $this->assertDatabaseMissing('catalogo_tipos_servicio', ['id' => $tipoServicioId]);
    }

    #[Test]
    public function campos_requeridos_validan_correctamente()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        CatalogoTipoServicio::create([
            // Sin nombre_tipo_servicio requerido
        ]);
    }

    #[Test]
    public function factory_crea_tipo_servicio_correctamente()
    {
        $tipoServicio = CatalogoTipoServicio::factory()->create();

        $this->assertNotNull($tipoServicio->nombre_tipo_servicio);
        $this->assertInstanceOf(CatalogoTipoServicio::class, $tipoServicio);
        $this->assertTrue($tipoServicio->exists);
    }

    #[Test]
    public function factory_puede_crear_con_parametros_especificos()
    {
        $tipoServicio = CatalogoTipoServicio::factory()->create([
            'nombre_tipo_servicio' => 'Cambio de Aceite',
        ]);

        $this->assertEquals('Cambio de Aceite', $tipoServicio->nombre_tipo_servicio);
    }

    #[Test]
    public function relacion_mantenimientos_es_has_many()
    {
        $tipoServicio = new CatalogoTipoServicio();
        $relacion = $tipoServicio->mantenimientos();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relacion);
        $this->assertEquals('tipo_servicio_id', $relacion->getForeignKeyName());
        $this->assertEquals('id', $relacion->getLocalKeyName());
    }

    #[Test]
    public function puede_crear_multiples_tipos_servicio()
    {
        $tipos = [
            'Mantenimiento Preventivo',
            'Mantenimiento Correctivo',
            'Cambio de Aceite',
            'Revisión General',
            'Reparación de Motor',
        ];

        foreach ($tipos as $tipo) {
            CatalogoTipoServicio::create(['nombre_tipo_servicio' => $tipo]);
        }

        $this->assertCount(5, CatalogoTipoServicio::all());

        foreach ($tipos as $tipo) {
            $this->assertDatabaseHas('catalogo_tipos_servicio', [
                'nombre_tipo_servicio' => $tipo,
            ]);
        }
    }

    #[Test]
    public function puede_buscar_tipo_servicio_por_nombre()
    {
        CatalogoTipoServicio::factory()->create(['nombre_tipo_servicio' => 'Servicio A']);
        CatalogoTipoServicio::factory()->create(['nombre_tipo_servicio' => 'Servicio B']);
        CatalogoTipoServicio::factory()->create(['nombre_tipo_servicio' => 'Servicio C']);

        $tipoEncontrado = CatalogoTipoServicio::where('nombre_tipo_servicio', 'Servicio B')->first();

        $this->assertNotNull($tipoEncontrado);
        $this->assertEquals('Servicio B', $tipoEncontrado->nombre_tipo_servicio);
    }

    #[Test]
    public function puede_contar_mantenimientos_relacionados()
    {
        $tipoServicio = CatalogoTipoServicio::factory()->create();

        // Crear 3 mantenimientos con este tipo de servicio
        Mantenimiento::factory()->count(3)->create(['tipo_servicio_id' => $tipoServicio->id]);

        // Crear otros mantenimientos con diferentes tipos (para asegurar que el conteo sea correcto)
        $otroTipo = CatalogoTipoServicio::factory()->create();
        Mantenimiento::factory()->count(2)->create(['tipo_servicio_id' => $otroTipo->id]);

        $tipoServicio = $tipoServicio->fresh();

        $this->assertEquals(3, $tipoServicio->mantenimientos()->count());
    }

    #[Test]
    public function modelo_solo_tiene_casts_por_defecto()
    {
        $tipoServicio = new CatalogoTipoServicio();

        // Solo debería tener los casts por defecto (timestamps)
        $casts = $tipoServicio->getCasts();

        // Verificar que no hay casts personalizados más allá de los predeterminados
        $this->assertArrayNotHasKey('nombre_tipo_servicio', $casts);
    }

    #[Test]
    public function primary_key_es_id()
    {
        $tipoServicio = new CatalogoTipoServicio();

        $this->assertEquals('id', $tipoServicio->getKeyName());
    }
}
