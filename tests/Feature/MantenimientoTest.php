<?php

namespace Tests\Feature;

use App\Models\Mantenimiento;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MantenimientoTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    private Vehiculo $vehiculo;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear datos base para las pruebas
        $this->user = User::factory()->create();
        $this->vehiculo = Vehiculo::factory()->create();

        $this->actingAs($this->user, 'sanctum');
    }

    /**
     * Test de creación de mantenimiento.
     */
    public function test_can_create_mantenimiento(): void
    {
        $mantenimientoData = [
            'vehiculo_id' => $this->vehiculo->id,
            'tipo_servicio' => 'CORRECTIVO',
            'proveedor' => 'Taller Mecánico ABC',
            'descripcion' => 'Cambio de aceite y filtros completo',
            'fecha_inicio' => '2025-07-15',
            'fecha_fin' => '2025-07-15',
            'kilometraje_servicio' => 150000,
            'costo' => 2500.00,
        ];

        $mantenimiento = Mantenimiento::create($mantenimientoData);

        $this->assertInstanceOf(Mantenimiento::class, $mantenimiento);
        $this->assertEquals($this->vehiculo->id, $mantenimiento->vehiculo_id);
        $this->assertEquals('CORRECTIVO', $mantenimiento->tipo_servicio);

        $this->assertDatabaseHas('mantenimientos', [
            'vehiculo_id' => $this->vehiculo->id,
            'descripcion' => 'Cambio de aceite y filtros completo',
            'kilometraje_servicio' => 150000,
        ]);
    }

    /**
     * Test de creación de mantenimiento mínimo (sin campos opcionales).
     */
    public function test_can_create_minimal_mantenimiento(): void
    {
        $mantenimientoData = [
            'vehiculo_id' => $this->vehiculo->id,
            'tipo_servicio' => 'CORRECTIVO',
            'descripcion' => 'Inspección rutinaria',
            'fecha_inicio' => '2025-07-15',
            'kilometraje_servicio' => 100000,
        ];

        $mantenimiento = Mantenimiento::create($mantenimientoData);

        $this->assertInstanceOf(Mantenimiento::class, $mantenimiento);
        $this->assertNull($mantenimiento->proveedor);
        $this->assertNull($mantenimiento->fecha_fin);
        $this->assertNull($mantenimiento->costo);

        $this->assertDatabaseHas('mantenimientos', [
            'vehiculo_id' => $this->vehiculo->id,
            'tipo_servicio' => 'CORRECTIVO',
            'descripcion' => 'Inspección rutinaria',
            'kilometraje_servicio' => 100000,
        ]);
    }

    /**
     * Test de lectura de mantenimientos.
     */
    public function test_can_read_mantenimientos(): void
    {
        $mantenimientos = Mantenimiento::factory(5)->create();

        $this->assertCount(5, Mantenimiento::all());

        $primerMantenimiento = Mantenimiento::first();
        $this->assertNotNull($primerMantenimiento->vehiculo_id);
        $this->assertNotNull($primerMantenimiento->tipo_servicio);
        $this->assertNotNull($primerMantenimiento->descripcion);
    }

    /**
     * Test de actualización de mantenimiento.
     */
    public function test_can_update_mantenimiento(): void
    {
        $mantenimiento = Mantenimiento::factory()->create([
            'descripcion' => 'Descripción original',
            'fecha_fin' => null,
        ]);

        $mantenimiento->update([
            'descripcion' => 'Mantenimiento completado - Cambio de aceite',
            'fecha_fin' => '2025-07-15',
            'costo' => 3500.00,
        ]);

        $mantenimientoActualizado = $mantenimiento->fresh();
        $this->assertEquals('Mantenimiento completado - Cambio de aceite', $mantenimientoActualizado->descripcion);
        $this->assertNotNull($mantenimientoActualizado->fecha_fin);
        $this->assertEquals(3500.00, $mantenimientoActualizado->costo);
    }

    /**
     * Test de eliminación de mantenimiento.
     */
    public function test_can_delete_mantenimiento(): void
    {
        $mantenimiento = Mantenimiento::factory()->create();
        $mantenimientoId = $mantenimiento->id;

        $mantenimiento->delete();

        // Verificar que el mantenimiento fue soft deleted
        $this->assertDatabaseHas('mantenimientos', [
            'id' => $mantenimientoId,
        ]);

        // Verificar que no aparece en consultas normales
        $this->assertNull(Mantenimiento::find($mantenimientoId));

        // Verificar que sí aparece con withTrashed
        $this->assertNotNull(Mantenimiento::withTrashed()->find($mantenimientoId));
    }

    /**
     * Test de relaciones del mantenimiento.
     */
    public function test_mantenimiento_relationships(): void
    {
        $mantenimiento = Mantenimiento::factory()->create([
            'vehiculo_id' => $this->vehiculo->id,
            'tipo_servicio' => 'CORRECTIVO',
        ]);

        // Test relación con Vehiculo
        $this->assertInstanceOf(Vehiculo::class, $mantenimiento->vehiculo);
        $this->assertEquals($this->vehiculo->id, $mantenimiento->vehiculo->id);

        // Test campo tipo_servicio
        $this->assertEquals('CORRECTIVO', $mantenimiento->tipo_servicio);

        // Test relación inversa desde Vehiculo
        $this->assertTrue($this->vehiculo->mantenimientos->contains($mantenimiento));
    }

    /**
     * Test de scopes para filtrado.
     */
    public function test_mantenimiento_scopes(): void
    {
        $vehiculo2 = Vehiculo::factory()->create();
        $tipoServicio2 = 'CORRECTIVO';

        // Crear mantenimientos de prueba
        $mantenimiento1 = Mantenimiento::factory()->create([
            'vehiculo_id' => $this->vehiculo->id,
            'tipo_servicio' => 'CORRECTIVO',
            'fecha_inicio' => '2025-07-01',
            'fecha_fin' => '2025-07-01',
        ]);

        $mantenimiento2 = Mantenimiento::factory()->create([
            'vehiculo_id' => $vehiculo2->id,
            'tipo_servicio' => 'PREVENTIVO',
            'fecha_inicio' => '2025-07-10',
            'fecha_fin' => null,
        ]);

        // Test filtro por vehículo
        $mantenimientosVehiculo1 = Mantenimiento::byVehiculo($this->vehiculo->id)->get();
        $this->assertCount(1, $mantenimientosVehiculo1);

        // Test filtro por tipo de servicio
        $mantenimientosTipo1 = Mantenimiento::byTipoServicio('CORRECTIVO')->get();
        $this->assertCount(1, $mantenimientosTipo1);

        // Test filtro completados
        $completados = Mantenimiento::completed()->get();
        $this->assertCount(1, $completados);

        // Test filtro pendientes
        $pendientes = Mantenimiento::pending()->get();
        $this->assertCount(1, $pendientes);
    }

    /**
     * Test de accessors del modelo.
     */
    public function test_mantenimiento_accessors(): void
    {
        // Mantenimiento completado
        $mantenimientoCompletado = Mantenimiento::factory()->create([
            'fecha_inicio' => '2025-07-01',
            'fecha_fin' => '2025-07-05',
        ]);

        $this->assertEquals(4, $mantenimientoCompletado->duracion_dias);
        $this->assertTrue($mantenimientoCompletado->is_completado);

        // Mantenimiento pendiente
        $mantenimientoPendiente = Mantenimiento::factory()->create([
            'fecha_inicio' => '2025-07-01',
            'fecha_fin' => null,
        ]);

        $this->assertNull($mantenimientoPendiente->duracion_dias);
        $this->assertFalse($mantenimientoPendiente->is_completado);
    }

    /**
     * Test de validaciones básicas.
     */
    public function test_required_fields_validation(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        // Intentar crear mantenimiento sin campos requeridos
        Mantenimiento::create([
            'descripcion' => 'Test sin vehiculo_id',
        ]);
    }

    /**
     * Test de factory con estados.
     */
    public function test_factory_states(): void
    {
        // Test estado completado
        $mantenimientoCompletado = Mantenimiento::factory()->completed()->create();
        $this->assertNotNull($mantenimientoCompletado->fecha_fin);
        $this->assertTrue($mantenimientoCompletado->is_completado);

        // Test estado pendiente
        $mantenimientoPendiente = Mantenimiento::factory()->pending()->create();
        $this->assertNull($mantenimientoPendiente->fecha_fin);
        $this->assertFalse($mantenimientoPendiente->is_completado);

        // Test estado costoso
        $mantenimientoCostoso = Mantenimiento::factory()->expensive()->create();
        $this->assertGreaterThanOrEqual(20000, $mantenimientoCostoso->costo);
    }

    /**
     * Test de filtro por rango de fechas.
     */
    public function test_date_range_filtering(): void
    {
        Mantenimiento::factory()->create(['fecha_inicio' => '2025-06-01']);
        Mantenimiento::factory()->create(['fecha_inicio' => '2025-07-01']);
        Mantenimiento::factory()->create(['fecha_inicio' => '2025-08-01']);

        // Filtrar por fecha de inicio
        $mantenimientosJulio = Mantenimiento::byDateRange('2025-07-01', '2025-07-31')->get();
        $this->assertCount(1, $mantenimientosJulio);

        // Filtrar desde julio en adelante
        $mantenimientosDesdeJulio = Mantenimiento::byDateRange('2025-07-01')->get();
        $this->assertCount(2, $mantenimientosDesdeJulio);
    }
}
