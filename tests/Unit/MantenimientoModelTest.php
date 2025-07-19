<?php

namespace Tests\Unit;

use App\Models\CatalogoTipoServicio;
use App\Models\Mantenimiento;
use App\Models\Vehiculo;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MantenimientoModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function puede_crear_mantenimiento()
    {
        $vehiculo = Vehiculo::factory()->create();
        $tipoServicio = CatalogoTipoServicio::factory()->create();

        $mantenimiento = Mantenimiento::create([
            'vehiculo_id' => $vehiculo->id,
            'tipo_servicio_id' => $tipoServicio->id,
            'descripcion' => 'Cambio de aceite',
            'fecha_inicio' => Carbon::today(),
            'kilometraje_servicio' => 15000,
        ]);

        $this->assertInstanceOf(Mantenimiento::class, $mantenimiento);
        $this->assertEquals($vehiculo->id, $mantenimiento->vehiculo_id);
    }

    #[Test]
    public function mantenimiento_relationships()
    {
        $vehiculo = Vehiculo::factory()->create();
        $tipoServicio = CatalogoTipoServicio::factory()->create();

        $mantenimiento = Mantenimiento::factory()->create([
            'vehiculo_id' => $vehiculo->id,
            'tipo_servicio_id' => $tipoServicio->id,
        ]);

        $this->assertInstanceOf(Vehiculo::class, $mantenimiento->vehiculo);
        $this->assertInstanceOf(CatalogoTipoServicio::class, $mantenimiento->tipoServicio);
        $this->assertEquals($vehiculo->id, $mantenimiento->vehiculo->id);
        $this->assertEquals($tipoServicio->id, $mantenimiento->tipoServicio->id);
    }

    #[Test]
    public function mantenimiento_scopes()
    {
        $vehiculo1 = Vehiculo::factory()->create();
        $vehiculo2 = Vehiculo::factory()->create();

        Mantenimiento::factory()->create([
            'vehiculo_id' => $vehiculo1->id,
            'fecha_inicio' => Carbon::today(),
        ]);

        Mantenimiento::factory()->create([
            'vehiculo_id' => $vehiculo2->id,
            'fecha_inicio' => Carbon::yesterday(),
        ]);

        $mantenimientosVehiculo1 = Mantenimiento::porVehiculo($vehiculo1->id)->get();
        $mantenimientosHoy = Mantenimiento::porFecha(Carbon::today())->get();

        $this->assertCount(1, $mantenimientosVehiculo1);
        $this->assertCount(1, $mantenimientosHoy);
    }

    #[Test]
    public function mantenimiento_accessors()
    {
        $mantenimiento = Mantenimiento::factory()->create([
            'costo' => 1500.50,
            'fecha_inicio' => Carbon::parse('2024-01-15'),
            'fecha_fin' => Carbon::parse('2024-01-17'),
        ]);

        $this->assertEquals('$1,500.50', $mantenimiento->costo_formateado);
        $this->assertEquals(2, $mantenimiento->duracion_dias);
    }

    #[Test]
    public function fecha_casting()
    {
        $casts = (new Mantenimiento)->getCasts();

        $this->assertArrayHasKey('fecha_inicio', $casts);
        $this->assertArrayHasKey('fecha_fin', $casts);
        $this->assertEquals('date', $casts['fecha_inicio']);
        $this->assertEquals('date', $casts['fecha_fin']);
    }

    #[Test]
    public function scope_by_date_range()
    {
        Mantenimiento::factory()->create(['fecha_inicio' => '2024-01-01']);
        Mantenimiento::factory()->create(['fecha_inicio' => '2024-01-15']);
        Mantenimiento::factory()->create(['fecha_inicio' => '2024-02-01']);

        $result = Mantenimiento::entreFechas('2024-01-01', '2024-01-31')->get();

        $this->assertCount(2, $result);
    }
}
