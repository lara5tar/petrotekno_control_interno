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
    public function catalogo_estatus_tiene_muchos_vehiculos()
    {
        $estatus = CatalogoEstatus::factory()->create();
        $vehiculo = Vehiculo::factory()->create(['estatus_id' => $estatus->id]);

        $this->assertTrue($estatus->vehiculos->contains($vehiculo));
    }

    #[Test]
    public function scope_activos_filtra_correctamente()
    {
        $activo = CatalogoEstatus::factory()->create([
            'nombre_estatus' => 'test_activo_'.time(),
            'activo' => true,
        ]);
        $inactivo = CatalogoEstatus::factory()->create([
            'nombre_estatus' => 'test_inactivo_'.time(),
            'activo' => false,
        ]);

        $resultado = CatalogoEstatus::activos()->get();

        $this->assertTrue($resultado->contains($activo));
        $this->assertFalse($resultado->contains($inactivo));
    }

    #[Test]
    public function accessor_nombre_estatus_funciona()
    {
        $estatus = CatalogoEstatus::factory()->create(['nombre_estatus' => 'activo']);

        $this->assertEquals('Activo', $estatus->nombre_estatus);
    }

    #[Test]
    public function activo_se_castea_como_boolean()
    {
        $estatus = CatalogoEstatus::factory()->create(['activo' => true]);

        $this->assertIsBool($estatus->activo);
        $this->assertTrue($estatus->activo);
    }

    #[Test]
    public function fillable_attributes_estan_configurados_correctamente()
    {
        $fillable = (new CatalogoEstatus)->getFillable();

        $this->assertContains('nombre_estatus', $fillable);
        $this->assertContains('descripcion', $fillable);
        $this->assertContains('activo', $fillable);
    }

    #[Test]
    public function tabla_personalizada_esta_configurada()
    {
        $estatus = new CatalogoEstatus;

        $this->assertEquals('catalogo_estatus', $estatus->getTable());
    }

    #[Test]
    public function timestamps_estan_habilitados()
    {
        $estatus = new CatalogoEstatus;

        $this->assertTrue($estatus->timestamps);
    }

    #[Test]
    public function puede_actualizar_estatus()
    {
        $estatus = CatalogoEstatus::factory()->create(['nombre_estatus' => 'activo']);

        $estatus->update(['nombre_estatus' => 'mantenimiento']);

        $this->assertEquals('Mantenimiento', $estatus->fresh()->nombre_estatus);
    }

    #[Test]
    public function puede_eliminar_estatus_sin_vehiculos()
    {
        $estatus = CatalogoEstatus::factory()->create();

        $this->assertTrue($estatus->delete());
        $this->assertDatabaseMissing('catalogo_estatus', ['id' => $estatus->id]);
    }

    #[Test]
    public function nombre_estatus_debe_ser_unico()
    {
        CatalogoEstatus::factory()->create(['nombre_estatus' => 'activo']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        CatalogoEstatus::factory()->create(['nombre_estatus' => 'activo']);
    }

    #[Test]
    public function puede_crear_estatus_con_factory()
    {
        $estatus = CatalogoEstatus::factory()->create();

        $this->assertInstanceOf(CatalogoEstatus::class, $estatus);
        $this->assertDatabaseHas('catalogo_estatus', ['id' => $estatus->id]);
    }

    #[Test]
    public function puede_obtener_conteo_de_vehiculos_relacionados()
    {
        $estatus = CatalogoEstatus::factory()->create();
        Vehiculo::factory(3)->create(['estatus_id' => $estatus->id]);

        $estatusConConteo = CatalogoEstatus::withCount('vehiculos')->find($estatus->id);

        $this->assertEquals(3, $estatusConConteo->vehiculos_count);
    }

    #[Test]
    public function relacion_vehiculos_incluye_soft_deleted()
    {
        $estatus = CatalogoEstatus::factory()->create();
        $vehiculo = Vehiculo::factory()->create(['estatus_id' => $estatus->id]);

        $vehiculo->delete();

        $this->assertEquals(1, $estatus->vehiculos()->withTrashed()->count());
        $this->assertEquals(0, $estatus->vehiculos()->count());
    }

    #[Test]
    public function puede_determinar_si_tiene_vehiculos_asociados()
    {
        $estatusSinVehiculos = CatalogoEstatus::factory()->create([
            'nombre_estatus' => 'sin_vehiculos_'.time(),
        ]);
        $estatusConVehiculos = CatalogoEstatus::factory()->create([
            'nombre_estatus' => 'con_vehiculos_'.time(),
        ]);

        Vehiculo::factory()->create(['estatus_id' => $estatusConVehiculos->id]);

        $this->assertFalse($estatusSinVehiculos->vehiculos()->exists());
        $this->assertTrue($estatusConVehiculos->vehiculos()->exists());
    }
}
