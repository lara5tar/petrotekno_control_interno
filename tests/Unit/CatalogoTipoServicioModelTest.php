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
    public function catalogo_tipo_servicio_tiene_muchos_mantenimientos()
    {
        $tipoServicio = CatalogoTipoServicio::factory()->create();
        $mantenimiento = Mantenimiento::factory()->create(['tipo_servicio_id' => $tipoServicio->id]);

        $this->assertTrue($tipoServicio->mantenimientos->contains($mantenimiento));
    }

    #[Test]
    public function fillable_attributes_estan_configurados_correctamente()
    {
        $fillable = (new CatalogoTipoServicio())->getFillable();

        $this->assertContains('nombre_tipo_servicio', $fillable);
    }

    #[Test]
    public function tabla_personalizada_esta_configurada()
    {
        $tipoServicio = new CatalogoTipoServicio();

        $this->assertEquals('catalogo_tipos_servicio', $tipoServicio->getTable());
    }

    #[Test]
    public function timestamps_estan_habilitados()
    {
        $tipoServicio = new CatalogoTipoServicio();

        $this->assertTrue($tipoServicio->timestamps);
    }

    #[Test]
    public function puede_actualizar_tipo_servicio()
    {
        $tipoServicio = CatalogoTipoServicio::factory()->create(['nombre_tipo_servicio' => 'Preventivo']);

        $tipoServicio->update(['nombre_tipo_servicio' => 'Correctivo']);

        $this->assertEquals('Correctivo', $tipoServicio->fresh()->nombre_tipo_servicio);
    }

    #[Test]
    public function puede_eliminar_tipo_servicio_sin_mantenimientos()
    {
        $tipoServicio = CatalogoTipoServicio::factory()->create();

        $this->assertTrue($tipoServicio->delete());
        $this->assertDatabaseMissing('catalogo_tipos_servicio', ['id' => $tipoServicio->id]);
    }

    #[Test]
    public function nombre_tipo_servicio_debe_ser_requerido()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        CatalogoTipoServicio::create([]);
    }

    #[Test]
    public function puede_crear_tipo_servicio_con_factory()
    {
        $tipoServicio = CatalogoTipoServicio::factory()->create();

        $this->assertInstanceOf(CatalogoTipoServicio::class, $tipoServicio);
        $this->assertDatabaseHas('catalogo_tipos_servicio', ['id' => $tipoServicio->id]);
    }
}
