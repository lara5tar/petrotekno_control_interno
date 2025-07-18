<?php

namespace Tests\Unit\Unit;

use App\Models\Mantenimiento;
use App\Models\Vehiculo;
use App\Models\CatalogoTipoServicio;
use App\Models\Documento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class MantenimientoModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test que verifica la creación básica de un mantenimiento.
     */
    public function test_can_create_mantenimiento(): void
    {
        $vehiculo = Vehiculo::factory()->create();
        $tipoServicio = CatalogoTipoServicio::factory()->create();

        $mantenimiento = Mantenimiento::create([
            'vehiculo_id' => $vehiculo->id,
            'tipo_servicio_id' => $tipoServicio->id,
            'descripcion' => 'Cambio de aceite y filtros',
            'fecha_inicio' => '2025-07-15',
            'fecha_fin' => '2025-07-15',
            'kilometraje_servicio' => 150000,
            'costo' => 2500.00,
        ]);

        $this->assertInstanceOf(Mantenimiento::class, $mantenimiento);
        $this->assertEquals($vehiculo->id, $mantenimiento->vehiculo_id);
        $this->assertEquals($tipoServicio->id, $mantenimiento->tipo_servicio_id);
        $this->assertEquals('Cambio de aceite y filtros', $mantenimiento->descripcion);
    }

    /**
     * Test de relaciones del modelo.
     */
    public function test_mantenimiento_relationships(): void
    {
        $vehiculo = Vehiculo::factory()->create();
        $tipoServicio = CatalogoTipoServicio::factory()->create();
        $mantenimiento = Mantenimiento::factory()->create([
            'vehiculo_id' => $vehiculo->id,
            'tipo_servicio_id' => $tipoServicio->id,
        ]);

        // Test relación belongsTo con Vehiculo
        $this->assertInstanceOf(Vehiculo::class, $mantenimiento->vehiculo);
        $this->assertEquals($vehiculo->id, $mantenimiento->vehiculo->id);

        // Test relación belongsTo con CatalogoTipoServicio
        $this->assertInstanceOf(CatalogoTipoServicio::class, $mantenimiento->tipoServicio);
        $this->assertEquals($tipoServicio->id, $mantenimiento->tipoServicio->id);

        // Test relación hasMany con Documentos
        $documento = Documento::factory()->create([
            'mantenimiento_id' => $mantenimiento->id,
        ]);
        
        $this->assertTrue($mantenimiento->documentos->contains($documento));
    }

    /**
     * Test de scopes del modelo.
     */
    public function test_mantenimiento_scopes(): void
    {
        $vehiculo1 = Vehiculo::factory()->create();
        $vehiculo2 = Vehiculo::factory()->create();
        $tipoServicio1 = CatalogoTipoServicio::factory()->create();
        $tipoServicio2 = CatalogoTipoServicio::factory()->create();

        // Crear mantenimientos de prueba
        $mantenimiento1 = Mantenimiento::factory()->create([
            'vehiculo_id' => $vehiculo1->id,
            'tipo_servicio_id' => $tipoServicio1->id,
            'fecha_inicio' => '2025-07-01',
            'fecha_fin' => '2025-07-01',
        ]);

        $mantenimiento2 = Mantenimiento::factory()->create([
            'vehiculo_id' => $vehiculo2->id,
            'tipo_servicio_id' => $tipoServicio2->id,
            'fecha_inicio' => '2025-07-10',
            'fecha_fin' => null, // Pendiente
        ]);

        // Test scope byVehiculo
        $mantenimientosVehiculo1 = Mantenimiento::byVehiculo($vehiculo1->id)->get();
        $this->assertCount(1, $mantenimientosVehiculo1);
        $this->assertEquals($mantenimiento1->id, $mantenimientosVehiculo1->first()->id);

        // Test scope byTipoServicio
        $mantenimientosTipo1 = Mantenimiento::byTipoServicio($tipoServicio1->id)->get();
        $this->assertCount(1, $mantenimientosTipo1);
        $this->assertEquals($mantenimiento1->id, $mantenimientosTipo1->first()->id);

        // Test scope completed
        $mantenimientosCompletados = Mantenimiento::completed()->get();
        $this->assertCount(1, $mantenimientosCompletados);
        $this->assertEquals($mantenimiento1->id, $mantenimientosCompletados->first()->id);

        // Test scope pending
        $mantenimientosPendientes = Mantenimiento::pending()->get();
        $this->assertCount(1, $mantenimientosPendientes);
        $this->assertEquals($mantenimiento2->id, $mantenimientosPendientes->first()->id);
    }

    /**
     * Test de accessors del modelo.
     */
    public function test_mantenimiento_accessors(): void
    {
        // Test mantenimiento completado
        $mantenimientoCompletado = Mantenimiento::factory()->create([
            'fecha_inicio' => '2025-07-01',
            'fecha_fin' => '2025-07-05',
        ]);

        $this->assertEquals(4, $mantenimientoCompletado->duracion_dias);
        $this->assertTrue($mantenimientoCompletado->is_completado);

        // Test mantenimiento pendiente
        $mantenimientoPendiente = Mantenimiento::factory()->create([
            'fecha_inicio' => '2025-07-01',
            'fecha_fin' => null,
        ]);

        $this->assertNull($mantenimientoPendiente->duracion_dias);
        $this->assertFalse($mantenimientoPendiente->is_completado);
    }

    /**
     * Test de validación de fechas.
     */
    public function test_fecha_casting(): void
    {
        $mantenimiento = Mantenimiento::factory()->create([
            'fecha_inicio' => '2025-07-15',
            'fecha_fin' => '2025-07-20',
        ]);

        $this->assertInstanceOf(Carbon::class, $mantenimiento->fecha_inicio);
        $this->assertInstanceOf(Carbon::class, $mantenimiento->fecha_fin);
        
        $this->assertEquals('2025-07-15', $mantenimiento->fecha_inicio->format('Y-m-d'));
        $this->assertEquals('2025-07-20', $mantenimiento->fecha_fin->format('Y-m-d'));
    }

    /**
     * Test de scope byDateRange.
     */
    public function test_scope_by_date_range(): void
    {
        // Crear exactamente los registros que necesitamos
        $vehiculo = Vehiculo::factory()->create();
        $tipoServicio = CatalogoTipoServicio::factory()->create();
        
        $mantenimiento1 = Mantenimiento::create([
            'vehiculo_id' => $vehiculo->id,
            'tipo_servicio_id' => $tipoServicio->id,
            'descripcion' => 'Test Junio',
            'fecha_inicio' => '2025-06-01',
            'kilometraje_servicio' => 100000
        ]);
        
        $mantenimiento2 = Mantenimiento::create([
            'vehiculo_id' => $vehiculo->id,
            'tipo_servicio_id' => $tipoServicio->id,
            'descripcion' => 'Test Julio',
            'fecha_inicio' => '2025-07-01',
            'kilometraje_servicio' => 101000
        ]);
        
        $mantenimiento3 = Mantenimiento::create([
            'vehiculo_id' => $vehiculo->id,
            'tipo_servicio_id' => $tipoServicio->id,
            'descripcion' => 'Test Agosto',
            'fecha_inicio' => '2025-08-01',
            'kilometraje_servicio' => 102000
        ]);

        // Verificar que se crearon correctamente
        $this->assertCount(3, Mantenimiento::all());

        // Test usando whereDate para comparar solo la parte de fecha
        $mantenimientos = Mantenimiento::whereDate('fecha_inicio', '=', '2025-06-01')->get();
        $this->assertCount(1, $mantenimientos, 'Debería encontrar el mantenimiento de junio');

        $mantenimientos = Mantenimiento::whereDate('fecha_inicio', '>=', '2025-07-01')->get();
        $this->assertCount(2, $mantenimientos, 'Debería encontrar julio y agosto');

        $mantenimientos = Mantenimiento::whereDate('fecha_inicio', '<=', '2025-07-01')->get();
        $this->assertCount(2, $mantenimientos, 'Debería encontrar junio y julio');

        // Test del scope: desde julio en adelante
        $mantenimientos = Mantenimiento::byDateRange('2025-07-01')->get();
        $this->assertCount(2, $mantenimientos, 'Scope: desde julio en adelante');

        // Test del scope: hasta julio inclusive  
        $mantenimientos = Mantenimiento::byDateRange(null, '2025-07-01')->get();
        $this->assertCount(2, $mantenimientos, 'Scope: hasta julio inclusive');

        // Test del scope: rango exacto
        $mantenimientos = Mantenimiento::byDateRange('2025-07-01', '2025-07-01')->get();
        $this->assertCount(1, $mantenimientos, 'Scope: solo julio exacto');
        $this->assertEquals($mantenimiento2->id, $mantenimientos->first()->id);
    }
}
