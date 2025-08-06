<?php

namespace Tests\Feature;

use App\Models\Mantenimiento;
use App\Models\Vehiculo;
use App\Models\User;
use App\Models\CatalogoTipoServicio;
use App\Models\CatalogoEstatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MantenimientoTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $vehiculo;
    protected $tipoServicio;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear usuario de prueba
        $this->user = User::factory()->create();
        
        // Crear estatus de prueba
        $estatus = CatalogoEstatus::factory()->create([
            'nombre_estatus' => 'Activo',
            'activo' => true
        ]);
        
        // Crear vehículo de prueba
        $this->vehiculo = Vehiculo::factory()->create([
            'estatus_id' => $estatus->id
        ]);
        
        // Crear tipo de servicio de prueba
        $this->tipoServicio = CatalogoTipoServicio::factory()->create();
    }

    /** @test */
    public function usuario_puede_ver_listado_de_mantenimientos()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('mantenimientos.index'));

        $response->assertOk();
        $response->assertViewIs('mantenimientos.index');
    }

    /** @test */
    public function usuario_puede_crear_mantenimiento()
    {
        $this->actingAs($this->user);

        $datosMantenimiento = [
            'vehiculo_id' => $this->vehiculo->id,
            'tipo_servicio_id' => $this->tipoServicio->id,
            'tipo_servicio' => 'PREVENTIVO',
            'sistema_vehiculo' => 'motor',
            'descripcion_servicio' => 'Cambio de aceite y filtros',
            'proveedor' => 'Taller Mecánico ABC',
            'fecha_inicio' => now()->format('Y-m-d'),
            'kilometraje_servicio' => 50000,
            'costo' => 1500.00,
            'observaciones' => 'Mantenimiento programado'
        ];

        $response = $this->post(route('mantenimientos.store'), $datosMantenimiento);

        $response->assertRedirect();
        $this->assertDatabaseHas('mantenimientos', [
            'vehiculo_id' => $this->vehiculo->id,
            'descripcion_servicio' => 'Cambio de aceite y filtros',
            'proveedor' => 'Taller Mecánico ABC'
        ]);
    }

    /** @test */
    public function usuario_puede_ver_detalles_de_mantenimiento()
    {
        $this->actingAs($this->user);

        $mantenimiento = Mantenimiento::factory()->create([
            'vehiculo_id' => $this->vehiculo->id,
            'tipo_servicio_id' => $this->tipoServicio->id
        ]);

        $response = $this->get(route('mantenimientos.show', $mantenimiento->id));

        $response->assertOk();
        $response->assertViewIs('mantenimientos.show');
        $response->assertViewHas('mantenimiento', $mantenimiento);
    }

    /** @test */
    public function usuario_puede_actualizar_mantenimiento()
    {
        $this->actingAs($this->user);

        $mantenimiento = Mantenimiento::factory()->create([
            'vehiculo_id' => $this->vehiculo->id,
            'tipo_servicio_id' => $this->tipoServicio->id
        ]);

        $datosActualizados = [
            'vehiculo_id' => $this->vehiculo->id,
            'tipo_servicio_id' => $this->tipoServicio->id,
            'tipo_servicio' => 'CORRECTIVO',
            'sistema_vehiculo' => 'transmision',
            'descripcion_servicio' => 'Reparación de transmisión',
            'proveedor' => 'Taller Especializado XYZ',
            'fecha_inicio' => now()->format('Y-m-d'),
            'kilometraje_servicio' => 55000,
            'costo' => 8500.00,
            'observaciones' => 'Reparación urgente'
        ];

        $response = $this->put(route('mantenimientos.update', $mantenimiento->id), $datosActualizados);

        $response->assertRedirect();
        $this->assertDatabaseHas('mantenimientos', [
            'id' => $mantenimiento->id,
            'descripcion_servicio' => 'Reparación de transmisión',
            'proveedor' => 'Taller Especializado XYZ'
        ]);
    }

    /** @test */
    public function usuario_puede_eliminar_mantenimiento()
    {
        $this->actingAs($this->user);

        $mantenimiento = Mantenimiento::factory()->create([
            'vehiculo_id' => $this->vehiculo->id,
            'tipo_servicio_id' => $this->tipoServicio->id
        ]);

        $response = $this->delete(route('mantenimientos.destroy', $mantenimiento->id));

        $response->assertRedirect();
        $this->assertSoftDeleted('mantenimientos', [
            'id' => $mantenimiento->id
        ]);
    }

    /** @test */
    public function mantenimiento_requiere_datos_obligatorios()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('mantenimientos.store'), []);

        $response->assertSessionHasErrors([
            'vehiculo_id',
            'tipo_servicio',
            'descripcion_servicio',
            'fecha_inicio',
            'kilometraje_servicio'
        ]);
    }

    /** @test */
    public function kilometraje_debe_ser_numero_positivo()
    {
        $this->actingAs($this->user);

        $datosMantenimiento = [
            'vehiculo_id' => $this->vehiculo->id,
            'tipo_servicio' => 'PREVENTIVO',
            'descripcion_servicio' => 'Cambio de aceite',
            'fecha_inicio' => now()->format('Y-m-d'),
            'kilometraje_servicio' => -1000
        ];

        $response = $this->post(route('mantenimientos.store'), $datosMantenimiento);

        $response->assertSessionHasErrors(['kilometraje_servicio']);
    }

    /** @test */
    public function costo_debe_ser_numero_positivo()
    {
        $this->actingAs($this->user);

        $datosMantenimiento = [
            'vehiculo_id' => $this->vehiculo->id,
            'tipo_servicio' => 'PREVENTIVO',
            'descripcion_servicio' => 'Cambio de aceite',
            'fecha_inicio' => now()->format('Y-m-d'),
            'kilometraje_servicio' => 50000,
            'costo' => -100
        ];

        $response = $this->post(route('mantenimientos.store'), $datosMantenimiento);

        $response->assertSessionHasErrors(['costo']);
    }

    /** @test */
    public function fecha_fin_debe_ser_posterior_a_fecha_inicio()
    {
        $this->actingAs($this->user);

        $fechaInicio = now();
        $fechaFin = $fechaInicio->copy()->subDay();

        $datosMantenimiento = [
            'vehiculo_id' => $this->vehiculo->id,
            'tipo_servicio' => 'PREVENTIVO',
            'descripcion_servicio' => 'Cambio de aceite',
            'fecha_inicio' => $fechaInicio->format('Y-m-d'),
            'fecha_fin' => $fechaFin->format('Y-m-d'),
            'kilometraje_servicio' => 50000
        ];

        $response = $this->post(route('mantenimientos.store'), $datosMantenimiento);

        $response->assertSessionHasErrors(['fecha_fin']);
    }

    /** @test */
    public function puede_filtrar_mantenimientos_por_vehiculo()
    {
        $this->actingAs($this->user);

        $vehiculo2 = Vehiculo::factory()->create();
        
        $mantenimiento1 = Mantenimiento::factory()->create([
            'vehiculo_id' => $this->vehiculo->id
        ]);
        
        $mantenimiento2 = Mantenimiento::factory()->create([
            'vehiculo_id' => $vehiculo2->id
        ]);

        $response = $this->get(route('mantenimientos.index', ['vehiculo_id' => $this->vehiculo->id]));

        $response->assertOk();
        $response->assertViewHas('mantenimientos');
        
        $mantenimientos = $response->viewData('mantenimientos');
        $this->assertTrue($mantenimientos->contains($mantenimiento1));
        $this->assertFalse($mantenimientos->contains($mantenimiento2));
    }

    /** @test */
    public function puede_buscar_mantenimientos_por_proveedor()
    {
        $this->actingAs($this->user);

        $mantenimiento1 = Mantenimiento::factory()->create([
            'vehiculo_id' => $this->vehiculo->id,
            'proveedor' => 'Taller ABC'
        ]);
        
        $mantenimiento2 = Mantenimiento::factory()->create([
            'vehiculo_id' => $this->vehiculo->id,
            'proveedor' => 'Taller XYZ'
        ]);

        $response = $this->get(route('mantenimientos.index', ['buscar' => 'ABC']));

        $response->assertOk();
        $mantenimientos = $response->viewData('mantenimientos');
        $this->assertTrue($mantenimientos->contains($mantenimiento1));
        $this->assertFalse($mantenimientos->contains($mantenimiento2));
    }
}