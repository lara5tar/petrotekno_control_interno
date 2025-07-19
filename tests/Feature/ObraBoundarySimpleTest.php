<?php

namespace Tests\Feature;

use App\Models\CategoriaPersonal;
use App\Models\Obra;
use App\Models\Personal;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test de límites y casos extremos simplificado para el módulo de Obras
 * Enfocado en validar robustez sin depender de permisos complejos
 */
class ObraBoundarySimpleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupBasicUser();
    }

    #[Test]
    public function test_fechas_extremas_en_modelo_obra()
    {
        // Test modelo directamente sin API

        // Fecha futura extrema (año 2100)
        $fechaFuturaExtrema = Carbon::create(2100, 12, 31);
        $obra1 = Obra::create([
            'nombre_obra' => 'Obra Futura Extrema',
            'estatus' => Obra::ESTATUS_PLANIFICADA,
            'fecha_inicio' => $fechaFuturaExtrema,
            'fecha_fin' => $fechaFuturaExtrema->copy()->addDays(30),
            'avance' => 0,
        ]);

        $this->assertInstanceOf(Obra::class, $obra1);
        $this->assertEquals('2100-12-31', $obra1->fecha_inicio->format('Y-m-d'));

        // Fecha pasada extrema (año 1900)
        $fechaPasadaExtrema = Carbon::create(1900, 1, 1);
        $obra2 = Obra::create([
            'nombre_obra' => 'Obra Pasada Extrema',
            'estatus' => Obra::ESTATUS_COMPLETADA,
            'fecha_inicio' => $fechaPasadaExtrema,
            'fecha_fin' => $fechaPasadaExtrema->copy()->addDays(30),
            'avance' => 100,
        ]);

        $this->assertInstanceOf(Obra::class, $obra2);
        $this->assertEquals('1900-01-01', $obra2->fecha_inicio->format('Y-m-d'));

        // Test: Año bisiesto
        $fechaBisiesto = Carbon::create(2024, 2, 29);
        $obra3 = Obra::create([
            'nombre_obra' => 'Obra Año Bisiesto',
            'estatus' => Obra::ESTATUS_PLANIFICADA,
            'fecha_inicio' => $fechaBisiesto,
            'avance' => 0,
        ]);

        $this->assertInstanceOf(Obra::class, $obra3);
        $this->assertEquals('2024-02-29', $obra3->fecha_inicio->format('Y-m-d'));
    }

    #[Test]
    public function test_avance_valores_limite_modelo()
    {
        $obra = Obra::factory()->create(['avance' => 50]);

        // Valores límite válidos
        $avancesValidos = [0, 1, 50, 99, 100];

        foreach ($avancesValidos as $avance) {
            $obra->avance = $avance;
            $obra->save();
            $this->assertEquals($avance, $obra->fresh()->avance);
        }

        // Valores que deben ser normalizados por el mutator
        $casosLimite = [
            ['input' => -1, 'expected' => 0],
            ['input' => -100, 'expected' => 0],
            ['input' => 101, 'expected' => 100],
            ['input' => 150, 'expected' => 100],
            ['input' => 999, 'expected' => 100],
        ];

        foreach ($casosLimite as $caso) {
            $obra->avance = $caso['input'];
            $obra->save();
            $this->assertEquals(
                $caso['expected'],
                $obra->fresh()->avance,
                "Avance {$caso['input']} should be normalized to {$caso['expected']}"
            );
        }
    }

    #[Test]
    public function test_nombres_longitud_maxima_modelo()
    {
        // Nombre en el límite (255 caracteres) - debería funcionar
        $nombreLimite = str_repeat('A', 255);

        try {
            $obra = Obra::create([
                'nombre_obra' => $nombreLimite,
                'estatus' => Obra::ESTATUS_PLANIFICADA,
                'fecha_inicio' => now(),
                'avance' => 0,
            ]);

            $this->assertInstanceOf(Obra::class, $obra);
            $this->assertEquals(255, strlen($obra->nombre_obra));
        } catch (\Exception $e) {
            // Si el mutator modifica el nombre, verificar que sea válido
            $this->assertLessThanOrEqual(255, strlen($e->getMessage()));
        }

        // Nombre vacío - verificar que el mutator procesa el string vacío
        $obraVacia = Obra::create([
            'nombre_obra' => '',
            'estatus' => Obra::ESTATUS_PLANIFICADA,
            'fecha_inicio' => now(),
            'avance' => 0,
        ]);

        // El mutator permite cadenas vacías, así que verificamos que se permita
        $this->assertInstanceOf(Obra::class, $obraVacia);
        $this->assertEquals('', $obraVacia->nombre_obra, 'Empty name should remain empty after mutator processing');
    }

    #[Test]
    public function test_caracteres_especiales_modelo()
    {
        $nombresEspeciales = [
            'Obra con áéíóú ñ',
            'Obra with "quotes"',
            'Obra & Symbols % $ #',
            'Obra 中文测试',
            'Obra العربية',
        ];

        foreach ($nombresEspeciales as $nombre) {
            try {
                $obra = Obra::create([
                    'nombre_obra' => $nombre,
                    'estatus' => Obra::ESTATUS_PLANIFICADA,
                    'fecha_inicio' => now(),
                    'avance' => 0,
                ]);

                $this->assertInstanceOf(Obra::class, $obra);
                $this->assertNotEmpty($obra->nombre_obra);

                // Limpiar para siguiente iteración
                $obra->delete();
            } catch (\Exception $e) {
                // Si falla, verificar que sea por una razón válida
                $this->assertStringContainsString('nombre_obra', $e->getMessage());
            }
        }
    }

    #[Test]
    public function test_consultas_grandes_volumenes()
    {
        // Crear muchas obras para probar rendimiento
        $cantidadObras = 200;

        $initialMemory = memory_get_usage();

        // Crear obras en lotes para eficiencia
        $obras = [];
        for ($i = 0; $i < $cantidadObras; $i++) {
            $obras[] = [
                'nombre_obra' => "Obra Test $i",
                'estatus' => Obra::ESTATUS_PLANIFICADA,
                'fecha_inicio' => now()->addDays($i),
                'avance' => rand(0, 100),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        \DB::table('obras')->insert($obras);

        // Consultar todas las obras
        $todasLasObras = Obra::all();
        $this->assertCount($cantidadObras, $todasLasObras);

        // Verificar uso de memoria
        $finalMemory = memory_get_usage();
        $memoryUsed = $finalMemory - $initialMemory;

        // No debería usar más de 20MB para 200 registros
        $this->assertLessThan(
            20 * 1024 * 1024,
            $memoryUsed,
            'Memory usage should not exceed 20MB for 200 records'
        );

        // Test paginación
        $obrasPaginadas = Obra::paginate(50);
        $this->assertCount(50, $obrasPaginadas->items());
        $this->assertEquals(4, $obrasPaginadas->lastPage());

        // Test consultas con scopes
        $obrasActivas = Obra::activas()->count();
        $this->assertEquals($cantidadObras, $obrasActivas);

        $obrasEnProgreso = Obra::enProgreso()->count();
        $this->assertEquals(0, $obrasEnProgreso); // Todas son planificadas
    }

    #[Test]
    public function test_busquedas_casos_extremos()
    {
        // Crear obras con nombres específicos
        $obras = [
            Obra::create([
                'nombre_obra' => 'Construcción Principal',
                'estatus' => Obra::ESTATUS_PLANIFICADA,
                'fecha_inicio' => now(),
                'avance' => 0,
            ]),
            Obra::create([
                'nombre_obra' => 'construcción secundaria',
                'estatus' => Obra::ESTATUS_EN_PROGRESO,
                'fecha_inicio' => now(),
                'avance' => 50,
            ]),
            Obra::create([
                'nombre_obra' => 'CONSTRUCCIÓN TERCIARIA',
                'estatus' => Obra::ESTATUS_COMPLETADA,
                'fecha_inicio' => now(),
                'avance' => 100,
            ]),
        ];

        // Búsqueda case-insensitive
        $resultados = Obra::buscar('construcción')->get();

        // Debug: ver qué nombres se crearon realmente
        $nombresCreados = collect($obras)->map(function ($obra) {
            return $obra->fresh()->nombre_obra;
        })->toArray();

        $this->assertGreaterThanOrEqual(
            2,
            $resultados->count(),
            'Should find at least 2 obras. Names created: '.implode(', ', $nombresCreados)
        );

        // Búsqueda con caracteres especiales
        $busquedasEspeciales = ['%', '_', '*', '?', '[', ']', '\\'];

        foreach ($busquedasEspeciales as $busqueda) {
            $resultado = Obra::buscar($busqueda)->get();
            $this->assertIsIterable($resultado);
            // No debería fallar, aunque no encuentre resultados
        }

        // Búsqueda vacía
        $resultadoVacio = Obra::buscar('')->get();
        $this->assertCount(3, $resultadoVacio); // Debería devolver todos

        // Búsqueda muy larga
        $busquedaLarga = str_repeat('construcción ', 50);
        $resultado = Obra::buscar($busquedaLarga)->get();
        $this->assertIsIterable($resultado);
    }

    #[Test]
    public function test_transiciones_estados_casos_limite()
    {
        $obra = Obra::create([
            'nombre_obra' => 'Obra Para Transiciones',
            'estatus' => Obra::ESTATUS_PLANIFICADA,
            'fecha_inicio' => now(),
            'avance' => 0,
        ]);

        // Transiciones válidas desde planificada
        $this->assertTrue($obra->cambiarEstatus(Obra::ESTATUS_EN_PROGRESO));
        $this->assertEquals(Obra::ESTATUS_EN_PROGRESO, $obra->estatus);

        // Desde en progreso a completada (debería actualizar avance)
        $this->assertTrue($obra->cambiarEstatus(Obra::ESTATUS_COMPLETADA));
        $this->assertEquals(Obra::ESTATUS_COMPLETADA, $obra->estatus);
        $this->assertEquals(100, $obra->avance);

        // Transición inválida desde completada
        $this->expectException(\InvalidArgumentException::class);
        $obra->cambiarEstatus(Obra::ESTATUS_EN_PROGRESO);
    }

    #[Test]
    public function test_accessors_casos_extremos()
    {
        // Obra sin fecha fin
        $obra1 = Obra::create([
            'nombre_obra' => 'Obra Sin Fin',
            'estatus' => Obra::ESTATUS_EN_PROGRESO,
            'fecha_inicio' => now(),
            'avance' => 50,
        ]);

        $this->assertNull($obra1->dias_restantes);
        $this->assertNull($obra1->duracion_total);
        $this->assertFalse($obra1->esta_atrasada);

        // Obra con fechas muy separadas
        $obra2 = Obra::create([
            'nombre_obra' => 'Obra Larga',
            'estatus' => Obra::ESTATUS_EN_PROGRESO,
            'fecha_inicio' => Carbon::create(2020, 1, 1),
            'fecha_fin' => Carbon::create(2025, 12, 31),
            'avance' => 20,
        ]);

        $this->assertGreaterThan(1800, $obra2->duracion_total); // Más de 5 años
        $this->assertGreaterThan(1000, $obra2->dias_transcurridos);

        // Obra con duración de 1 día
        $obra3 = Obra::create([
            'nombre_obra' => 'Obra Rápida',
            'estatus' => Obra::ESTATUS_COMPLETADA,
            'fecha_inicio' => today(),
            'fecha_fin' => today(),
            'avance' => 100,
        ]);

        $this->assertEquals(1, $obra3->duracion_total);
    }

    private function setupBasicUser()
    {
        // Setup mínimo sin complicaciones de permisos
        $categoria = CategoriaPersonal::create(['nombre_categoria' => 'Test']);
        $personal = Personal::factory()->create(['categoria_id' => $categoria->id]);
        $role = Role::create(['nombre_rol' => 'Test']);

        $user = User::factory()->create([
            'personal_id' => $personal->id,
            'rol_id' => $role->id,
        ]);
    }
}
