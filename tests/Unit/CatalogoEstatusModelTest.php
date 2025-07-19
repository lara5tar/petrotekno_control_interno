<?php

namespace Tests\Unit;

use App\Models\CatalogoEstatus;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CatalogoEstatusModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function puede_crear_catalogo_estatus()
    {
        $estatus = CatalogoEstatus::create([
            'nombre_estatus' => 'activo',
            'descripcion' => 'Vehículo en estado activo',
            'activo' => true,
        ]);

        $this->assertDatabaseHas('catalogo_estatus', [
            'nombre_estatus' => 'activo',
            'descripcion' => 'Vehículo en estado activo',
            'activo' => true,
        ]);
    }

    #[Test]
    public function puede_crear_estatus_sin_descripcion()
    {
        $estatus = CatalogoEstatus::create([
            'nombre_estatus' => 'disponible',
            'activo' => true,
        ]);

        $this->assertDatabaseHas('catalogo_estatus', [
            'nombre_estatus' => 'disponible',
            'descripcion' => null,
            'activo' => true,
        ]);
    }

    #[Test]
    public function relacion_con_vehiculos_funciona_correctamente()
    {
        $estatus = CatalogoEstatus::factory()->create();

        $vehiculo1 = Vehiculo::factory()->create(['estatus_id' => $estatus->id]);
        $vehiculo2 = Vehiculo::factory()->create(['estatus_id' => $estatus->id]);

        $estatus = $estatus->fresh();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $estatus->vehiculos);
        $this->assertCount(2, $estatus->vehiculos);
        $this->assertTrue($estatus->vehiculos->contains($vehiculo1));
        $this->assertTrue($estatus->vehiculos->contains($vehiculo2));
    }

    #[Test]
    public function scope_activos_filtra_correctamente()
    {
        CatalogoEstatus::factory()->create([
            'nombre_estatus' => 'activo',
            'activo' => true,
        ]);

        CatalogoEstatus::factory()->create([
            'nombre_estatus' => 'inactivo',
            'activo' => false,
        ]);

        CatalogoEstatus::factory()->create([
            'nombre_estatus' => 'disponible',
            'activo' => true,
        ]);

        $estatusActivos = CatalogoEstatus::activos()->get();

        $this->assertCount(2, $estatusActivos);
        $this->assertTrue($estatusActivos->every(function ($estatus) {
            return $estatus->activo === true;
        }));
    }

    #[Test]
    public function accessor_nombre_estatus_convierte_primera_letra_mayuscula()
    {
        $estatus = CatalogoEstatus::factory()->create([
            'nombre_estatus' => 'disponible',
        ]);

        $this->assertEquals('Disponible', $estatus->nombre_estatus);
    }

    #[Test]
    public function cast_activo_funciona_correctamente()
    {
        $estatus = CatalogoEstatus::factory()->create(['activo' => 1]);

        $this->assertIsBool($estatus->activo);
        $this->assertTrue($estatus->activo);

        $estatus->update(['activo' => 0]);
        $estatus->refresh();

        $this->assertIsBool($estatus->activo);
        $this->assertFalse($estatus->activo);
    }

    #[Test]
    public function fillable_permite_campos_correctos()
    {
        $estatus = new CatalogoEstatus();

        $expectedFillable = [
            'nombre_estatus',
            'descripcion',
            'activo',
        ];

        $this->assertEquals($expectedFillable, $estatus->getFillable());
    }

    #[Test]
    public function tabla_es_catalogo_estatus()
    {
        $estatus = new CatalogoEstatus();

        $this->assertEquals('catalogo_estatus', $estatus->getTable());
    }

    #[Test]
    public function modelo_usa_timestamps()
    {
        $estatus = new CatalogoEstatus();

        $this->assertTrue($estatus->usesTimestamps());
    }

    #[Test]
    public function puede_actualizar_estatus()
    {
        $estatus = CatalogoEstatus::factory()->create([
            'nombre_estatus' => 'original',
            'descripcion' => 'Descripción original',
            'activo' => true,
        ]);

        $estatus->update([
            'nombre_estatus' => 'actualizado',
            'descripcion' => 'Descripción actualizada',
            'activo' => false,
        ]);

        $this->assertDatabaseHas('catalogo_estatus', [
            'id' => $estatus->id,
            'nombre_estatus' => 'actualizado',
            'descripcion' => 'Descripción actualizada',
            'activo' => false,
        ]);
    }

    #[Test]
    public function puede_eliminar_estatus()
    {
        $estatus = CatalogoEstatus::factory()->create();
        $estatusId = $estatus->id;

        $estatus->delete();

        $this->assertDatabaseMissing('catalogo_estatus', ['id' => $estatusId]);
    }

    #[Test]
    public function campos_requeridos_validan_correctamente()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        CatalogoEstatus::create([
            'descripcion' => 'Sin nombre de estatus',
            'activo' => true,
        ]);
    }

    #[Test]
    public function factory_crea_estatus_correctamente()
    {
        $estatus = CatalogoEstatus::factory()->create();

        $this->assertNotNull($estatus->nombre_estatus);
        $this->assertIsBool($estatus->activo);
        $this->assertInstanceOf(CatalogoEstatus::class, $estatus);
        $this->assertTrue($estatus->exists);
    }

    #[Test]
    public function factory_puede_crear_con_parametros_especificos()
    {
        $estatus = CatalogoEstatus::factory()->create([
            'nombre_estatus' => 'mantenimiento',
            'descripcion' => 'En mantenimiento programado',
            'activo' => false,
        ]);

        $this->assertEquals('Mantenimiento', $estatus->nombre_estatus); // Con accessor
        $this->assertEquals('En mantenimiento programado', $estatus->descripcion);
        $this->assertFalse($estatus->activo);
    }

    #[Test]
    public function relacion_vehiculos_es_has_many()
    {
        $estatus = new CatalogoEstatus();
        $relacion = $estatus->vehiculos();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relacion);
        $this->assertEquals('estatus_id', $relacion->getForeignKeyName());
        $this->assertEquals('id', $relacion->getLocalKeyName());
    }
}
