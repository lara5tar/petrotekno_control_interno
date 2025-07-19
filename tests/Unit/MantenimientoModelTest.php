<?php

namespace Tests\Unit;

use App\Models\CatalogoTipoServicio;
use App\Models\Documento;
use App\Models\Mantenimiento;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MantenimientoModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    #[Test]
    public function mantenimiento_can_be_created()
    {
        $vehiculo = Vehiculo::factory()->create();
        $tipoServicio = CatalogoTipoServicio::factory()->create();

        $mantenimiento = Mantenimiento::create([
            'vehiculo_id' => $vehiculo->id,
            'tipo_servicio_id' => $tipoServicio->id,
            'proveedor' => 'Taller Ejemplo',
            'descripcion' => 'Cambio de aceite',
            'fecha_inicio' => '2025-01-01',
            'kilometraje_servicio' => 50000,
            'costo' => 250.50,
        ]);

        $this->assertInstanceOf(Mantenimiento::class, $mantenimiento);
        $this->assertDatabaseHas('mantenimientos', [
            'vehiculo_id' => $vehiculo->id,
            'tipo_servicio_id' => $tipoServicio->id,
            'proveedor' => 'Taller Ejemplo',
        ]);
    }

    #[Test]
    public function mantenimiento_belongs_to_vehiculo()
    {
        $vehiculo = Vehiculo::factory()->create();
        $mantenimiento = Mantenimiento::factory()->create([
            'vehiculo_id' => $vehiculo->id,
        ]);

        $this->assertInstanceOf(Vehiculo::class, $mantenimiento->vehiculo);
        $this->assertEquals($vehiculo->id, $mantenimiento->vehiculo->id);
    }

    #[Test]
    public function mantenimiento_belongs_to_tipo_servicio()
    {
        $tipoServicio = CatalogoTipoServicio::factory()->create();
        $mantenimiento = Mantenimiento::factory()->create([
            'tipo_servicio_id' => $tipoServicio->id,
        ]);

        $this->assertInstanceOf(CatalogoTipoServicio::class, $mantenimiento->tipoServicio);
        $this->assertEquals($tipoServicio->id, $mantenimiento->tipoServicio->id);
    }

    #[Test]
    public function mantenimiento_has_many_documentos()
    {
        $mantenimiento = Mantenimiento::factory()->create();

        Documento::factory()->count(3)->create([
            'mantenimiento_id' => $mantenimiento->id,
        ]);

        $this->assertCount(3, $mantenimiento->documentos);
        $this->assertInstanceOf(Documento::class, $mantenimiento->documentos->first());
    }

    #[Test]
    public function scope_by_vehiculo_filters_correctly()
    {
        $vehiculo1 = Vehiculo::factory()->create();
        $vehiculo2 = Vehiculo::factory()->create();

        Mantenimiento::factory()->create(['vehiculo_id' => $vehiculo1->id]);
        Mantenimiento::factory()->create(['vehiculo_id' => $vehiculo2->id]);
        Mantenimiento::factory()->create(['vehiculo_id' => $vehiculo1->id]);

        $mantenimientos = Mantenimiento::byVehiculo($vehiculo1->id)->get();

        $this->assertCount(2, $mantenimientos);
        foreach ($mantenimientos as $mantenimiento) {
            $this->assertEquals($vehiculo1->id, $mantenimiento->vehiculo_id);
        }
    }

    #[Test]
    public function scope_by_tipo_servicio_filters_correctly()
    {
        $tipoServicio1 = CatalogoTipoServicio::factory()->create();
        $tipoServicio2 = CatalogoTipoServicio::factory()->create();

        Mantenimiento::factory()->create(['tipo_servicio_id' => $tipoServicio1->id]);
        Mantenimiento::factory()->create(['tipo_servicio_id' => $tipoServicio2->id]);
        Mantenimiento::factory()->create(['tipo_servicio_id' => $tipoServicio1->id]);

        $mantenimientos = Mantenimiento::byTipoServicio($tipoServicio1->id)->get();

        $this->assertCount(2, $mantenimientos);
        foreach ($mantenimientos as $mantenimiento) {
            $this->assertEquals($tipoServicio1->id, $mantenimiento->tipo_servicio_id);
        }
    }

    #[Test]
    public function scope_by_date_range_filters_correctly()
    {
        Mantenimiento::factory()->create(['fecha_inicio' => '2025-01-01']);
        Mantenimiento::factory()->create(['fecha_inicio' => '2025-02-15']);
        Mantenimiento::factory()->create(['fecha_inicio' => '2025-03-30']);

        $mantenimientos = Mantenimiento::byDateRange('2025-02-01', '2025-03-01')->get();

        $this->assertCount(1, $mantenimientos);
        $this->assertEquals('2025-02-15', $mantenimientos->first()->fecha_inicio->format('Y-m-d'));
    }

    #[Test]
    public function scope_completed_filters_only_completed()
    {
        Mantenimiento::factory()->create(['fecha_fin' => '2025-01-10']); // Completado
        Mantenimiento::factory()->create(['fecha_fin' => null]); // Pendiente
        Mantenimiento::factory()->create(['fecha_fin' => '2025-01-15']); // Completado

        $completados = Mantenimiento::completed()->get();

        $this->assertCount(2, $completados);
        foreach ($completados as $mantenimiento) {
            $this->assertNotNull($mantenimiento->fecha_fin);
        }
    }

    #[Test]
    public function scope_pending_filters_only_pending()
    {
        Mantenimiento::factory()->create(['fecha_fin' => '2025-01-10']); // Completado
        Mantenimiento::factory()->create(['fecha_fin' => null]); // Pendiente
        Mantenimiento::factory()->create(['fecha_fin' => null]); // Pendiente

        $pendientes = Mantenimiento::pending()->get();

        $this->assertCount(2, $pendientes);
        foreach ($pendientes as $mantenimiento) {
            $this->assertNull($mantenimiento->fecha_fin);
        }
    }

    #[Test]
    public function duracion_dias_accessor_calculates_correctly()
    {
        $mantenimiento = Mantenimiento::factory()->create([
            'fecha_inicio' => '2025-01-01',
            'fecha_fin' => '2025-01-06',
        ]);

        $this->assertEquals(5, $mantenimiento->duracion_dias);
    }

    #[Test]
    public function duracion_dias_accessor_returns_null_for_pending()
    {
        $mantenimiento = Mantenimiento::factory()->create([
            'fecha_inicio' => '2025-01-01',
            'fecha_fin' => null,
        ]);

        $this->assertNull($mantenimiento->duracion_dias);
    }

    #[Test]
    public function is_completado_accessor_works_correctly()
    {
        $completado = Mantenimiento::factory()->create(['fecha_fin' => '2025-01-10']);
        $pendiente = Mantenimiento::factory()->create(['fecha_fin' => null]);

        $this->assertTrue($completado->is_completado);
        $this->assertFalse($pendiente->is_completado);
    }

    #[Test]
    public function mantenimiento_uses_soft_deletes()
    {
        $mantenimiento = Mantenimiento::factory()->create();

        $mantenimiento->delete();

        $this->assertSoftDeleted('mantenimientos', ['id' => $mantenimiento->id]);
        $this->assertNotNull($mantenimiento->fresh()->deleted_at);
    }

    #[Test]
    public function mantenimiento_can_be_restored()
    {
        $mantenimiento = Mantenimiento::factory()->create();

        $mantenimiento->delete();
        $mantenimiento->restore();

        $this->assertNull($mantenimiento->fresh()->deleted_at);
        $this->assertDatabaseHas('mantenimientos', [
            'id' => $mantenimiento->id,
            'deleted_at' => null,
        ]);
    }

    #[Test]
    public function mantenimiento_fillable_attributes()
    {
        $fillable = [
            'vehiculo_id',
            'tipo_servicio_id',
            'proveedor',
            'descripcion',
            'fecha_inicio',
            'fecha_fin',
            'kilometraje_servicio',
            'costo',
        ];

        $mantenimiento = new Mantenimiento();

        $this->assertEquals($fillable, $mantenimiento->getFillable());
    }

    #[Test]
    public function mantenimiento_casts_attributes_correctly()
    {
        $mantenimiento = Mantenimiento::factory()->create([
            'fecha_inicio' => '2025-01-01',
            'fecha_fin' => '2025-01-06',
            'costo' => 250.75,
            'kilometraje_servicio' => 50000,
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $mantenimiento->fecha_inicio);
        $this->assertInstanceOf(\Carbon\Carbon::class, $mantenimiento->fecha_fin);
        $this->assertIsString($mantenimiento->costo); // decimal:2 devuelve string
        $this->assertEquals('250.75', $mantenimiento->costo);
        $this->assertIsInt($mantenimiento->kilometraje_servicio);
    }

    #[Test]
    public function mantenimiento_table_name_is_correct()
    {
        $mantenimiento = new Mantenimiento();

        $this->assertEquals('mantenimientos', $mantenimiento->getTable());
    }

    #[Test]
    public function mantenimiento_has_timestamps()
    {
        $mantenimiento = Mantenimiento::factory()->create();

        $this->assertNotNull($mantenimiento->created_at);
        $this->assertNotNull($mantenimiento->updated_at);
    }
}
