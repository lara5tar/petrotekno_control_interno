<?php

namespace Tests\Unit;

use App\Models\CategoriaPersonal;
use App\Models\Personal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CategoriaPersonalModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function puede_crear_categoria_personal()
    {
        $categoria = CategoriaPersonal::factory()->create([
            'nombre_categoria' => 'Operador',
        ]);

        $this->assertInstanceOf(CategoriaPersonal::class, $categoria);
        $this->assertEquals('Operador', $categoria->nombre_categoria);
    }

    #[Test]
    public function categoria_personal_tiene_muchos_personal()
    {
        $categoria = CategoriaPersonal::factory()->create();
        $personal = Personal::factory()->create(['categoria_id' => $categoria->id]);

        $this->assertTrue($categoria->personal->contains($personal));
    }

    #[Test]
    public function fillable_attributes_estan_configurados()
    {
        $fillable = (new CategoriaPersonal())->getFillable();

        $this->assertContains('nombre_categoria', $fillable);
    }

    #[Test]
    public function tabla_personalizada_esta_configurada()
    {
        $categoria = new CategoriaPersonal();

        $this->assertEquals('categorias_personal', $categoria->getTable());
    }

    #[Test]
    public function timestamps_estan_habilitados()
    {
        $categoria = new CategoriaPersonal();

        $this->assertTrue($categoria->timestamps);
    }

    #[Test]
    public function puede_actualizar_categoria()
    {
        $categoria = CategoriaPersonal::factory()->create(['nombre_categoria' => 'Operador']);

        $categoria->update(['nombre_categoria' => 'Supervisor']);

        $this->assertEquals('Supervisor', $categoria->fresh()->nombre_categoria);
    }

    #[Test]
    public function puede_eliminar_categoria()
    {
        $categoria = CategoriaPersonal::factory()->create();

        $this->assertTrue($categoria->delete());
        $this->assertDatabaseMissing('categorias_personal', ['id' => $categoria->id]);
    }

    #[Test]
    public function nombre_categoria_es_requerido()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        CategoriaPersonal::create([]);
    }

    #[Test]
    public function puede_crear_categoria_con_factory()
    {
        $categoria = CategoriaPersonal::factory()->create();

        $this->assertInstanceOf(CategoriaPersonal::class, $categoria);
        $this->assertDatabaseHas('categorias_personal', ['id' => $categoria->id]);
    }
}
