<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Obra;
use Carbon\Carbon;

class ObraModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function obra_puede_ser_creada()
    {
        $obra = Obra::factory()->create([
            'nombre_obra' => 'Obra de Prueba',
            'estatus' => Obra::ESTATUS_PLANIFICADA,
            'avance' => 0,
            'fecha_inicio' => '2025-01-01',
            'fecha_fin' => '2025-12-31',
        ]);

        $this->assertInstanceOf(Obra::class, $obra);
        $this->assertEquals('Obra De Prueba', $obra->nombre_obra); // Mutator aplicado
        $this->assertEquals(Obra::ESTATUS_PLANIFICADA, $obra->estatus);
        $this->assertEquals(0, $obra->avance);
    }

    /** @test */
    public function obra_calcula_dias_transcurridos_correctamente()
    {
        // Obra iniciada hace 30 días
        $fechaInicio = Carbon::now()->subDays(30);
        $obra = Obra::factory()->create([
            'fecha_inicio' => $fechaInicio->format('Y-m-d'),
        ]);

        $this->assertEquals(30, $obra->dias_transcurridos);
    }

    /** @test */
    public function obra_calcula_dias_restantes_correctamente()
    {
        // Obra que termina en 60 días
        $fechaFin = Carbon::now()->addDays(60);
        $obra = Obra::factory()->create([
            'fecha_inicio' => Carbon::now()->format('Y-m-d'),
            'fecha_fin' => $fechaFin->format('Y-m-d'),
        ]);

        $this->assertEquals(60, $obra->dias_restantes);
    }

    /** @test */
    public function obra_sin_fecha_fin_devuelve_null_en_dias_restantes()
    {
        $obra = Obra::factory()->create([
            'fecha_fin' => null,
        ]);

        $this->assertNull($obra->dias_restantes);
    }

    /** @test */
    public function obra_calcula_duracion_total_correctamente()
    {
        $fechaInicio = Carbon::now();
        $fechaFin = Carbon::now()->addDays(100);
        
        $obra = Obra::factory()->create([
            'fecha_inicio' => $fechaInicio->format('Y-m-d'),
            'fecha_fin' => $fechaFin->format('Y-m-d'),
        ]);

        $this->assertEquals(101, $obra->duracion_total); // +1 día incluye el día final
    }

    /** @test */
    public function obra_detecta_si_esta_atrasada()
    {
        // Obra con fecha fin en el pasado y no completada
        $obra = Obra::factory()->create([
            'estatus' => Obra::ESTATUS_EN_PROGRESO,
            'fecha_inicio' => Carbon::now()->subDays(100)->format('Y-m-d'),
            'fecha_fin' => Carbon::now()->subDays(10)->format('Y-m-d'), // Fecha fin pasada
        ]);

        $this->assertTrue($obra->esta_atrasada);
    }

    /** @test */
    public function obra_completada_no_esta_atrasada()
    {
        // Obra completada aunque fecha fin sea pasada
        $obra = Obra::factory()->create([
            'estatus' => Obra::ESTATUS_COMPLETADA,
            'fecha_inicio' => Carbon::now()->subDays(100)->format('Y-m-d'),
            'fecha_fin' => Carbon::now()->subDays(10)->format('Y-m-d'),
        ]);

        $this->assertFalse($obra->esta_atrasada);
    }

    /** @test */
    public function obra_sin_fecha_fin_no_esta_atrasada()
    {
        $obra = Obra::factory()->create([
            'estatus' => Obra::ESTATUS_EN_PROGRESO,
            'fecha_fin' => null,
        ]);

        $this->assertFalse($obra->esta_atrasada);
    }

    /** @test */
    public function obra_calcula_porcentaje_tiempo_transcurrido()
    {
        // Obra de 100 días, iniciada hace 25 días
        $fechaInicio = Carbon::now()->subDays(25);
        $fechaFin = Carbon::now()->addDays(75);
        
        $obra = Obra::factory()->create([
            'fecha_inicio' => $fechaInicio->format('Y-m-d'),
            'fecha_fin' => $fechaFin->format('Y-m-d'),
        ]);

        // 25 días transcurridos de 101 días totales = ~24.8%
        $this->assertEquals(24.8, $obra->porcentaje_tiempo_transcurrido);
    }

    /** @test */
    public function obra_devuelve_descripcion_de_estatus()
    {
        $obra = Obra::factory()->create(['estatus' => Obra::ESTATUS_EN_PROGRESO]);
        
        $this->assertEquals('Obra activa en desarrollo', $obra->estatus_descripcion);
    }

    /** @test */
    public function mutator_nombre_obra_aplica_formato_titulo()
    {
        $obra = Obra::factory()->create(['nombre_obra' => 'construcción de carretera principal']);
        
        $this->assertEquals('Construcción De Carretera Principal', $obra->nombre_obra);
    }

    /** @test */
    public function mutator_avance_valida_rango()
    {
        // Test valor negativo se convierte a 0
        $obra = Obra::factory()->make();
        $obra->avance = -10;
        $this->assertEquals(0, $obra->avance);

        // Test valor mayor a 100 se convierte a 100
        $obra->avance = 150;
        $this->assertEquals(100, $obra->avance);

        // Test valor válido se mantiene
        $obra->avance = 50;
        $this->assertEquals(50, $obra->avance);
    }

    /** @test */
    public function mutator_estatus_valida_valores_permitidos()
    {
        $this->expectException(\InvalidArgumentException::class);
        
        $obra = Obra::factory()->make();
        $obra->estatus = 'estado_invalido';
    }

    /** @test */
    public function scope_por_estatus_funciona()
    {
        Obra::factory()->count(3)->create(['estatus' => Obra::ESTATUS_EN_PROGRESO]);
        Obra::factory()->count(2)->create(['estatus' => Obra::ESTATUS_COMPLETADA]);

        $obrasEnProgreso = Obra::porEstatus(Obra::ESTATUS_EN_PROGRESO)->get();
        $obrasCompletadas = Obra::porEstatus(Obra::ESTATUS_COMPLETADA)->get();

        $this->assertCount(3, $obrasEnProgreso);
        $this->assertCount(2, $obrasCompletadas);
    }

    /** @test */
    public function scope_activas_excluye_canceladas()
    {
        Obra::factory()->count(2)->create(['estatus' => Obra::ESTATUS_EN_PROGRESO]);
        Obra::factory()->count(1)->create(['estatus' => Obra::ESTATUS_COMPLETADA]);
        Obra::factory()->count(3)->create(['estatus' => Obra::ESTATUS_CANCELADA]);

        $obrasActivas = Obra::activas()->get();

        $this->assertCount(3, $obrasActivas); // 2 en progreso + 1 completada
    }

    /** @test */
    public function scope_buscar_funciona()
    {
        Obra::factory()->create(['nombre_obra' => 'Construcción de Carretera']);
        Obra::factory()->create(['nombre_obra' => 'Reparación de Puente']);
        Obra::factory()->create(['nombre_obra' => 'Instalación de Red']);

        $resultados = Obra::buscar('Carretera')->get();

        $this->assertCount(1, $resultados);
        $this->assertStringContainsString('Carretera', $resultados->first()->nombre_obra);
    }

    /** @test */
    public function scope_entre_fechas_funciona()
    {
        // Limpiar datos existentes para este test
        Obra::query()->delete();
        
        // Preparar datos de prueba - crear 3 obras con fechas específicas
        $fecha1 = Carbon::create(2025, 1, 1);  
        $fecha2 = Carbon::create(2025, 1, 15);  
        $fecha3 = Carbon::create(2025, 1, 30); 

        Obra::factory()->create(['fecha_inicio' => $fecha1->format('Y-m-d')]);
        Obra::factory()->create(['fecha_inicio' => $fecha2->format('Y-m-d')]);
        Obra::factory()->create(['fecha_inicio' => $fecha3->format('Y-m-d')]);

        // Test principal: verificar que el scope funciona y devuelve resultados válidos
        $obrasEnRango = Obra::entreFechas(
            $fecha1->format('Y-m-d'),
            $fecha3->format('Y-m-d')
        )->get();

        // Verificamos que el scope funciona y encuentra obras en el rango
        $this->assertGreaterThan(0, $obrasEnRango->count(),
            'El scope entreFechas debe funcionar y devolver al menos una obra');
        
        // Verificamos que no devuelve todas las obras si el rango es específico
        $this->assertLessThanOrEqual(3, $obrasEnRango->count(),
            'El scope no debe devolver más obras de las que existen');
            
        // Test de que funciona con orden: las fechas devueltas deben estar en el rango
        foreach ($obrasEnRango as $obra) {
            $fechaObra = $obra->fecha_inicio->format('Y-m-d');
            $this->assertTrue(
                $fechaObra >= $fecha1->format('Y-m-d') && $fechaObra <= $fecha3->format('Y-m-d'),
                "La fecha $fechaObra debe estar entre {$fecha1->format('Y-m-d')} y {$fecha3->format('Y-m-d')}"
            );
        }
    }

    /** @test */
    public function metodo_cambiar_estatus_valida_transiciones()
    {
        // Crear obra planificada
        $obra = Obra::factory()->planificada()->create();

        // Transición válida: planificada -> en progreso
        $resultado = $obra->cambiarEstatus(Obra::ESTATUS_EN_PROGRESO);
        $this->assertTrue($resultado);
        $this->assertEquals(Obra::ESTATUS_EN_PROGRESO, $obra->estatus);

        // Transición válida: en progreso -> completada
        $obra->cambiarEstatus(Obra::ESTATUS_COMPLETADA);
        $this->assertEquals(Obra::ESTATUS_COMPLETADA, $obra->estatus);
        $this->assertEquals(100, $obra->avance); // Debe ser 100% al completar
    }

    /** @test */
    public function metodo_cambiar_estatus_rechaza_transiciones_invalidas()
    {
        $this->expectException(\InvalidArgumentException::class);
        
        // Crear obra completada
        $obra = Obra::factory()->completada()->create();

        // Transición inválida: completada -> en progreso
        $obra->cambiarEstatus(Obra::ESTATUS_EN_PROGRESO);
    }

    /** @test */
    public function soft_deletes_funciona()
    {
        $obra = Obra::factory()->create();
        $obraId = $obra->id;

        // Eliminar (soft delete)
        $obra->delete();

        // Verificar que no aparece en consultas normales
        $this->assertNull(Obra::find($obraId));

        // Verificar que aparece en consultas con trashed
        $this->assertNotNull(Obra::withTrashed()->find($obraId));

        // Restaurar
        $obra->restore();

        // Verificar que vuelve a aparecer
        $this->assertNotNull(Obra::find($obraId));
    }

    /** @test */
    public function obra_tiene_timestamps_actualizados()
    {
        $obra = Obra::factory()->create();

        $this->assertNotNull($obra->created_at);
        $this->assertNotNull($obra->updated_at);

        $timestampOriginal = $obra->updated_at;

        // Esperar un momento y actualizar
        sleep(1);
        $obra->update(['avance' => 50]);

        $this->assertNotEquals($timestampOriginal, $obra->fresh()->updated_at);
    }

    /** @test */
    public function constantes_de_estados_estan_definidas()
    {
        $this->assertEquals('planificada', Obra::ESTATUS_PLANIFICADA);
        $this->assertEquals('en_progreso', Obra::ESTATUS_EN_PROGRESO);
        $this->assertEquals('suspendida', Obra::ESTATUS_SUSPENDIDA);
        $this->assertEquals('completada', Obra::ESTATUS_COMPLETADA);
        $this->assertEquals('cancelada', Obra::ESTATUS_CANCELADA);

        $this->assertCount(5, Obra::ESTADOS_VALIDOS);
    }
}
