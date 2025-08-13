<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Vehiculo;

class CrearVehiculoTest extends DuskTestCase
{
    /**
     * Prueba la creación de un vehículo y verifica que el estatus se establezca automáticamente como disponible.
     *
     * @return void
     */
    public function testCrearVehiculoConEstatusDisponible()
    {
        $this->browse(function (Browser $browser) {
            // Obtenemos un usuario con permisos para crear vehículos
            $user = User::where('email', 'admin@example.com')->first();
            
            if (!$user) {
                $this->markTestSkipped('No se encontró un usuario para la prueba.');
            }
            
            $browser->loginAs($user)
                   ->visit('/vehiculos/create')
                   ->assertSee('Agregar Nuevo Vehículo')
                   ->type('marca', 'Toyota')
                   ->type('modelo', 'Corolla')
                   ->type('anio', '2025')
                   ->type('n_serie', 'TEST'.time())
                   ->type('placas', 'TEST'.time())
                   ->type('kilometraje_actual', '5000')
                   ->type('observaciones', 'Vehículo de prueba creado con Playwright')
                   ->press('Guardar Vehículo')
                   ->assertPathIs('/vehiculos/*/show') // Patrón para cualquier ID
                   ->assertSee('Vehículo creado exitosamente');

            // Verificar que el último vehículo creado tenga el estado "disponible"
            $ultimoVehiculo = Vehiculo::latest('id')->first();
            $this->assertEquals('disponible', $ultimoVehiculo->estatus);
        });
    }
}
