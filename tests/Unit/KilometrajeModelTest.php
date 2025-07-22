<?php

namespace Tests\Unit;

use App\Models\CatalogoEstatus;
use App\Models\CategoriaPersonal;
use App\Models\Kilometraje;
use App\Models\Obra;
use App\Models\Personal;
use App\Models\User;
use App\Models\Vehiculo;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KilometrajeModelTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Vehiculo $vehiculo;

    protected Obra $obra;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear categoría de personal y personal
        $categoria = CategoriaPersonal::factory()->create();
        $personal = Personal::factory()->create(['categoria_id' => $categoria->id]);
        $this->user = User::factory()->create(['personal_id' => $personal->id]);

        // Crear estatus y vehículo
        $estatus = CatalogoEstatus::factory()->create();
        $this->vehiculo = Vehiculo::factory()->create([
            'estatus_id' => $estatus->id,
            'intervalo_km_motor' => 5000,
            'intervalo_km_transmision' => 10000,
            'intervalo_km_hidraulico' => 15000,
        ]);

        $this->obra = Obra::factory()->create();
    }

    public function test_tiene_relaciones_correctas(): void
    {
        $kilometraje = Kilometraje::factory()->create([
            'vehiculo_id' => $this->vehiculo->id,
            'usuario_captura_id' => $this->user->id,
            'obra_id' => $this->obra->id,
        ]);

        // Verificar relaciones
        $this->assertInstanceOf(Vehiculo::class, $kilometraje->vehiculo);
        $this->assertInstanceOf(User::class, $kilometraje->usuarioCaptura);
        $this->assertInstanceOf(Obra::class, $kilometraje->obra);

        $this->assertEquals($this->vehiculo->id, $kilometraje->vehiculo->id);
        $this->assertEquals($this->user->id, $kilometraje->usuarioCaptura->id);
        $this->assertEquals($this->obra->id, $kilometraje->obra->id);
    }

    public function test_scope_by_vehiculo_funciona_correctamente(): void
    {
        $otroVehiculo = Vehiculo::factory()->create(['estatus_id' => $this->vehiculo->estatus_id]);

        Kilometraje::factory()->count(3)->create(['vehiculo_id' => $this->vehiculo->id]);
        Kilometraje::factory()->count(2)->create(['vehiculo_id' => $otroVehiculo->id]);

        $resultado = Kilometraje::byVehiculo($this->vehiculo->id)->get();

        $this->assertCount(3, $resultado);
        foreach ($resultado as $km) {
            $this->assertEquals($this->vehiculo->id, $km->vehiculo_id);
        }
    }

    public function test_scope_by_obra_funciona_correctamente(): void
    {
        $otraObra = Obra::factory()->create();

        Kilometraje::factory()->count(2)->create([
            'vehiculo_id' => $this->vehiculo->id,
            'obra_id' => $this->obra->id,
        ]);

        Kilometraje::factory()->count(1)->create([
            'vehiculo_id' => $this->vehiculo->id,
            'obra_id' => $otraObra->id,
        ]);

        $resultado = Kilometraje::byObra($this->obra->id)->get();

        $this->assertCount(2, $resultado);
        foreach ($resultado as $km) {
            $this->assertEquals($this->obra->id, $km->obra_id);
        }
    }

    public function test_scope_by_fechas_funciona_correctamente(): void
    {
        $fechaInicio = Carbon::now()->subDays(10);
        $fechaFin = Carbon::now()->subDays(5);

        // Crear kilometrajes en diferentes fechas
        Kilometraje::factory()->create([
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_captura' => $fechaInicio->copy()->subDays(1), // Fuera del rango
        ]);

        Kilometraje::factory()->create([
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_captura' => $fechaInicio->copy()->addDays(2), // Dentro del rango
        ]);

        Kilometraje::factory()->create([
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_captura' => $fechaFin->copy()->addDays(1), // Fuera del rango
        ]);

        $resultado = Kilometraje::byFechas($fechaInicio->format('Y-m-d'), $fechaFin->format('Y-m-d'))->get();

        $this->assertCount(1, $resultado);
    }

    public function test_accessor_fecha_captura_formatted_funciona(): void
    {
        $fecha = Carbon::create(2025, 7, 21);
        $kilometraje = Kilometraje::factory()->create([
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_captura' => $fecha,
        ]);

        $this->assertEquals('21/07/2025', $kilometraje->fecha_captura_formatted);
    }

    public function test_metodo_dias_desde_captura_funciona(): void
    {
        $fecha = Carbon::now()->subDays(5);
        $kilometraje = Kilometraje::factory()->create([
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_captura' => $fecha,
        ]);

        $this->assertEquals(5, $kilometraje->getDiasDesdeCaptura());
    }

    public function test_metodo_get_ultimo_kilometraje_funciona(): void
    {
        // Crear varios kilometrajes
        Kilometraje::factory()->create([
            'vehiculo_id' => $this->vehiculo->id,
            'kilometraje' => 10000,
        ]);

        $ultimoKm = Kilometraje::factory()->create([
            'vehiculo_id' => $this->vehiculo->id,
            'kilometraje' => 15000, // Este debe ser el último
        ]);

        Kilometraje::factory()->create([
            'vehiculo_id' => $this->vehiculo->id,
            'kilometraje' => 12000,
        ]);

        $resultado = Kilometraje::getUltimoKilometraje($this->vehiculo->id);

        $this->assertEquals($ultimoKm->id, $resultado->id);
        $this->assertEquals(15000, $resultado->kilometraje);
    }

    public function test_metodo_es_kilometraje_valido_funciona(): void
    {
        $kilometraje = new Kilometraje;

        // Caso 1: Primer kilometraje del vehículo
        $this->assertTrue($kilometraje->esKilometrajeValido(5000, $this->vehiculo->id));

        // Caso 2: Crear un kilometraje existente
        Kilometraje::factory()->create([
            'vehiculo_id' => $this->vehiculo->id,
            'kilometraje' => 10000,
        ]);

        // Kilometraje mayor al existente debe ser válido
        $this->assertTrue($kilometraje->esKilometrajeValido(12000, $this->vehiculo->id));

        // Kilometraje menor al existente debe ser inválido
        $this->assertFalse($kilometraje->esKilometrajeValido(9000, $this->vehiculo->id));

        // Kilometraje igual al existente debe ser inválido
        $this->assertFalse($kilometraje->esKilometrajeValido(10000, $this->vehiculo->id));
    }

    public function test_metodo_calcular_proximos_mantenimientos_funciona(): void
    {
        $kilometraje = Kilometraje::factory()->create([
            'vehiculo_id' => $this->vehiculo->id,
            'kilometraje' => 14500, // Cerca del siguiente mantenimiento
        ]);

        $alertas = $kilometraje->calcularProximosMantenimientos();

        $this->assertIsArray($alertas);
        $this->assertNotEmpty($alertas);

        // Verificar que contiene las alertas esperadas
        $tiposEsperados = ['Motor', 'Transmisión', 'Hidráulico'];
        foreach ($alertas as $alerta) {
            $this->assertContains($alerta['tipo'], $tiposEsperados);
            $this->assertArrayHasKey('proximo_km', $alerta);
            $this->assertArrayHasKey('km_restantes', $alerta);
            $this->assertArrayHasKey('urgente', $alerta);
            $this->assertIsBool($alerta['urgente']);
        }
    }

    public function test_cast_fecha_captura_funciona(): void
    {
        $kilometraje = Kilometraje::factory()->create([
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_captura' => '2025-07-21',
        ]);

        $this->assertInstanceOf(Carbon::class, $kilometraje->fecha_captura);
        $this->assertEquals('2025-07-21', $kilometraje->fecha_captura->format('Y-m-d'));
    }

    public function test_fillable_attributes_funcionan(): void
    {
        $data = [
            'vehiculo_id' => $this->vehiculo->id,
            'kilometraje' => 15000,
            'fecha_captura' => now()->format('Y-m-d'),
            'usuario_captura_id' => $this->user->id,
            'obra_id' => $this->obra->id,
            'observaciones' => 'Test fillable',
        ];

        $kilometraje = new Kilometraje;
        $kilometraje->fill($data);

        foreach ($data as $key => $value) {
            if ($key === 'fecha_captura') {
                // Para fechas, comparar solo la fecha sin hora
                $this->assertEquals($value, $kilometraje->$key->format('Y-m-d'));
            } else {
                $this->assertEquals($value, $kilometraje->$key);
            }
        }
    }
}
