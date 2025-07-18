<?php

namespace Tests\Unit;

use App\Models\CatalogoEstatus;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehiculoModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_vehiculo_can_be_created(): void
    {
        $estatus = CatalogoEstatus::first();

        $vehiculo = Vehiculo::create([
            'marca' => 'Toyota',
            'modelo' => 'Hilux',
            'anio' => 2020,
            'n_serie' => 'ABC123456',
            'placas' => 'XYZ-123',
            'estatus_id' => $estatus->id,
            'kilometraje_actual' => 50000,
        ]);

        $this->assertInstanceOf(Vehiculo::class, $vehiculo);
        $this->assertEquals('Toyota', $vehiculo->marca);
        $this->assertEquals('Hilux', $vehiculo->modelo);
        $this->assertEquals(2020, $vehiculo->anio);
        $this->assertEquals('XYZ-123', $vehiculo->placas);
    }

    public function test_vehiculo_belongs_to_estatus(): void
    {
        $estatus = CatalogoEstatus::first();

        $vehiculo = Vehiculo::create([
            'marca' => 'Ford',
            'modelo' => 'F-150',
            'anio' => 2019,
            'n_serie' => 'DEF789012',
            'placas' => 'ABC-456',
            'estatus_id' => $estatus->id,
            'kilometraje_actual' => 30000,
        ]);

        $this->assertInstanceOf(CatalogoEstatus::class, $vehiculo->estatus);
        $this->assertEquals($estatus->id, $vehiculo->estatus->id);
    }

    public function test_vehiculo_nombre_completo_accessor(): void
    {
        $estatus = CatalogoEstatus::first();

        $vehiculo = Vehiculo::create([
            'marca' => 'Chevrolet',
            'modelo' => 'Silverado',
            'anio' => 2021,
            'n_serie' => 'GHI345678',
            'placas' => 'DEF-789',
            'estatus_id' => $estatus->id,
            'kilometraje_actual' => 15000,
        ]);

        $this->assertEquals('Chevrolet Silverado (2021)', $vehiculo->nombre_completo);
    }

    public function test_vehiculo_placas_are_uppercase(): void
    {
        $estatus = CatalogoEstatus::first();

        $vehiculo = Vehiculo::create([
            'marca' => 'Nissan',
            'modelo' => 'Frontier',
            'anio' => 2018,
            'n_serie' => 'JKL901234',
            'placas' => 'ghi-012',
            'estatus_id' => $estatus->id,
            'kilometraje_actual' => 80000,
        ]);

        $this->assertEquals('GHI-012', $vehiculo->placas);
    }

    public function test_vehiculo_scopes_work_correctly(): void
    {
        $estatus = CatalogoEstatus::first();

        $toyota = Vehiculo::create([
            'marca' => 'Toyota',
            'modelo' => 'Corolla',
            'anio' => 2020,
            'n_serie' => 'TOY123456',
            'placas' => 'TOY-001',
            'estatus_id' => $estatus->id,
            'kilometraje_actual' => 25000,
        ]);

        $ford = Vehiculo::create([
            'marca' => 'Ford',
            'modelo' => 'Explorer',
            'anio' => 2019,
            'n_serie' => 'FOR789012',
            'placas' => 'FOR-001',
            'estatus_id' => $estatus->id,
            'kilometraje_actual' => 35000,
        ]);

        // Test scope por marca
        $toyotas = Vehiculo::porMarca('Toyota')->get();
        $this->assertCount(1, $toyotas);
        $this->assertEquals('Toyota', $toyotas->first()->marca);

        // Test scope buscar
        $resultados = Vehiculo::buscar('TOY')->get();
        $this->assertCount(1, $resultados);
        $this->assertEquals('TOY-001', $resultados->first()->placas);

        // Test scope por año
        $vehiculos2020 = Vehiculo::porAnio(2020)->get();
        $this->assertCount(1, $vehiculos2020);
        $this->assertEquals(2020, $vehiculos2020->first()->anio);
    }

    public function test_vehiculo_soft_deletes(): void
    {
        $estatus = CatalogoEstatus::first();

        $vehiculo = Vehiculo::create([
            'marca' => 'Honda',
            'modelo' => 'Civic',
            'anio' => 2017,
            'n_serie' => 'HON567890',
            'placas' => 'HON-001',
            'estatus_id' => $estatus->id,
            'kilometraje_actual' => 60000,
        ]);

        $vehiculo->delete();

        $this->assertSoftDeleted($vehiculo);
        $this->assertNotNull($vehiculo->fresh()->fecha_eliminacion);
    }
}
