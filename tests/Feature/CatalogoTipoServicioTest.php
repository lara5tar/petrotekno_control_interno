<?php

namespace Tests\Feature;

use App\Models\CatalogoTipoServicio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CatalogoTipoServicioTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear usuario autenticado para las pruebas
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'sanctum');
    }

    /**
     * Test de creación de tipo de servicio.
     */
    public function test_can_create_tipo_servicio(): void
    {
        $tipoServicioData = [
            'nombre_tipo_servicio' => 'Mantenimiento Preventivo Motor'
        ];

        $tipoServicio = CatalogoTipoServicio::create($tipoServicioData);

        $this->assertInstanceOf(CatalogoTipoServicio::class, $tipoServicio);
        $this->assertEquals('Mantenimiento Preventivo Motor', $tipoServicio->nombre_tipo_servicio);
        
        $this->assertDatabaseHas('catalogo_tipos_servicio', [
            'nombre_tipo_servicio' => 'Mantenimiento Preventivo Motor'
        ]);
    }

    /**
     * Test de lectura de tipos de servicio.
     */
    public function test_can_read_tipos_servicio(): void
    {
        $tiposServicio = CatalogoTipoServicio::factory(3)->create();

        $this->assertCount(3, CatalogoTipoServicio::all());
        
        $primerTipo = CatalogoTipoServicio::first();
        $this->assertNotNull($primerTipo->nombre_tipo_servicio);
        $this->assertNotNull($primerTipo->created_at);
    }

    /**
     * Test de actualización de tipo de servicio.
     */
    public function test_can_update_tipo_servicio(): void
    {
        $tipoServicio = CatalogoTipoServicio::factory()->create([
            'nombre_tipo_servicio' => 'Servicio Original'
        ]);

        $tipoServicio->update([
            'nombre_tipo_servicio' => 'Servicio Actualizado'
        ]);

        $this->assertEquals('Servicio Actualizado', $tipoServicio->fresh()->nombre_tipo_servicio);
        
        $this->assertDatabaseHas('catalogo_tipos_servicio', [
            'id' => $tipoServicio->id,
            'nombre_tipo_servicio' => 'Servicio Actualizado'
        ]);
    }

    /**
     * Test de eliminación de tipo de servicio.
     */
    public function test_can_delete_tipo_servicio(): void
    {
        $tipoServicio = CatalogoTipoServicio::factory()->create();
        $tipoServicioId = $tipoServicio->id;

        $tipoServicio->delete();

        $this->assertDatabaseMissing('catalogo_tipos_servicio', [
            'id' => $tipoServicioId
        ]);
    }

    /**
     * Test de relación con mantenimientos.
     */
    public function test_tipo_servicio_has_mantenimientos_relationship(): void
    {
        $tipoServicio = CatalogoTipoServicio::factory()->create();

        // Verificar que la relación existe y está vacía inicialmente
        $this->assertCount(0, $tipoServicio->mantenimientos);
        
        // Crear mantenimientos asociados
        $mantenimientos = \App\Models\Mantenimiento::factory(2)->create([
            'tipo_servicio_id' => $tipoServicio->id
        ]);

        // Refrescar el modelo y verificar la relación
        $tipoServicio->refresh();
        $this->assertCount(2, $tipoServicio->mantenimientos);
        
        // Verificar que los mantenimientos pertenecen al tipo correcto
        foreach ($tipoServicio->mantenimientos as $mantenimiento) {
            $this->assertEquals($tipoServicio->id, $mantenimiento->tipo_servicio_id);
        }
    }

    /**
     * Test de validación - nombre requerido.
     */
    public function test_nombre_tipo_servicio_is_required(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        CatalogoTipoServicio::create([
            'nombre_tipo_servicio' => null
        ]);
    }

    /**
     * Test de factory funcionando correctamente.
     */
    public function test_factory_creates_valid_tipo_servicio(): void
    {
        $tipoServicio = CatalogoTipoServicio::factory()->create();

        $this->assertNotNull($tipoServicio->id);
        $this->assertNotNull($tipoServicio->nombre_tipo_servicio);
        $this->assertNotNull($tipoServicio->created_at);
        $this->assertNotNull($tipoServicio->updated_at);
        
        // Verificar que el nombre no está vacío
        $this->assertNotEmpty($tipoServicio->nombre_tipo_servicio);
        $this->assertIsString($tipoServicio->nombre_tipo_servicio);
    }

    /**
     * Test de múltiples tipos de servicio únicos.
     */
    public function test_can_create_multiple_unique_tipos_servicio(): void
    {
        $tipos = [
            'Mantenimiento Preventivo Motor',
            'Mantenimiento Preventivo Transmisión',
            'Reparación General',
            'Cambio de Aceite'
        ];

        foreach ($tipos as $nombreTipo) {
            CatalogoTipoServicio::create([
                'nombre_tipo_servicio' => $nombreTipo
            ]);
        }

        $this->assertCount(4, CatalogoTipoServicio::all());
        
        // Verificar que cada tipo existe en la base de datos
        foreach ($tipos as $nombreTipo) {
            $this->assertDatabaseHas('catalogo_tipos_servicio', [
                'nombre_tipo_servicio' => $nombreTipo
            ]);
        }
    }
}
