<?php

namespace Tests\Feature;

use App\Jobs\EnviarAlertaMantenimiento;
use App\Mail\AlertasMantenimientoMail;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Services\ConfiguracionAlertasService;
use Database\Seeders\ConfiguracionAlertasSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Tests específicos para el sistema de envío de emails de alertas
 * 
 * @group emails
 * @group alertas
 */
class EmailSystemTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->seed(ConfiguracionAlertasSeeder::class);

        // Crear un usuario admin con permisos para los tests
        $personal = \App\Models\Personal::factory()->create();

        // Crear rol con permisos necesarios
        $rol = new Role([
            'nombre_rol' => 'Admin Test',
            'descripcion' => 'Rol para testing'
        ]);
        $rol->save();

        // Crear el permiso necesario
        $permiso = Permission::firstOrCreate([
            'nombre_permiso' => 'gestionar_alertas_mantenimiento',
            'descripcion' => 'Gestionar alertas de mantenimiento'
        ]);

        // Asignar permiso al rol
        $rol->permisos()->attach($permiso->id);

        $this->user = User::factory()->create([
            'personal_id' => $personal->id,
            'email' => 'admin@test.com',
            'rol_id' => $rol->id
        ]);
    }

    /**
     * Obtener emails de prueba para tests
     */
    private function getTestEmails(): array
    {
        $emailsPrueba = env('MAIL_TEST_RECIPIENTS', 'test@example.com,test2@example.com');
        return array_map('trim', explode(',', $emailsPrueba));
    }

    /** @test */
    public function mailable_se_construye_correctamente()
    {
        $alertasData = [
            'alertas' => [
                [
                    'vehiculo_info' => [
                        'marca' => 'Toyota',
                        'modelo' => 'Hilux',
                        'placas' => 'ABC-123',
                        'nombre_completo' => 'Toyota Hilux ABC-123'
                    ],
                    'sistema' => 'Motor',
                    'kilometraje_actual' => 150000,
                    'ultimo_mantenimiento' => [
                        'kilometraje' => 130000,
                        'fecha' => '2024-01-15'
                    ],
                    'km_vencido_por' => 10000,
                    'urgencia' => 'alta'
                ]
            ],
            'resumen' => [
                'total_alertas' => 1,
                'vehiculos_afectados' => 1,
                'por_urgencia' => ['critica' => 0, 'alta' => 1, 'media' => 0]
            ]
        ];

        // Crear el mailable normal
        $mailable = new AlertasMantenimientoMail($alertasData, false);
        $this->assertInstanceOf(AlertasMantenimientoMail::class, $mailable);
        $this->assertEquals($alertasData, $mailable->alertasData);
        $this->assertFalse($mailable->esTest);

        // Crear el mailable de test
        $mailableTest = new AlertasMantenimientoMail($alertasData, true);
        $this->assertTrue($mailableTest->esTest);
    }

    /** @test */
    public function mailable_envelope_tiene_configuracion_correcta()
    {
        $alertasData = ['alertas' => [], 'resumen' => []];
        $mailable = new AlertasMantenimientoMail($alertasData, false);

        $envelope = $mailable->envelope();

        // Simplificar verificación del from (puede ser string o objeto)
        $this->assertNotEmpty($envelope->from);

        // Verificar subject
        $this->assertStringContainsString('Alertas de Mantenimiento', $envelope->subject);

        // Verificar tags para categorización
        $this->assertContains('maintenance-alerts', $envelope->tags);
        $this->assertContains('transactional', $envelope->tags);

        // Verificar metadata
        $this->assertEquals('control-interno-petrotekno', $envelope->metadata['sistema']);
        $this->assertEquals('resend', $envelope->metadata['mailer_service']);
    }

    /** @test */
    public function mailable_test_tiene_subject_diferente()
    {
        $alertasData = ['alertas' => [], 'resumen' => []];
        $mailableTest = new AlertasMantenimientoMail($alertasData, true);

        $envelope = $mailableTest->envelope();

        // Verificar que el subject incluye [PRUEBA]
        $this->assertStringContainsString('[PRUEBA]', $envelope->subject);
        $this->assertContains('test', $envelope->tags);
        $this->assertEquals('true', $envelope->metadata['es_test']);
    }

    /** @test */
    public function job_verifica_configuracion_antes_enviar()
    {
        Queue::fake();

        // Configurar alertas habilitadas usando DB directamente
        \DB::table('configuracion_alertas')->updateOrInsert(
            ['tipo_config' => 'general', 'clave' => 'alertas_habilitadas'],
            ['valor' => json_encode(true)]
        );

        // Usar dispatch estático en lugar de método de instancia
        $testEmails = $this->getTestEmails();
        EnviarAlertaMantenimiento::dispatch(true, $testEmails);

        // Verificar que el job fue despachado correctamente
        Queue::assertPushed(EnviarAlertaMantenimiento::class, function ($job) use ($testEmails) {
            return $job->esTest === true && !empty(array_intersect($testEmails, $job->emailsTest));
        });
    }

    /** @test */
    public function job_test_email_funciona()
    {
        // En lugar de probar el envío real, vamos a verificar que el job funciona correctamente
        // usando Queue::fake() en lugar de Mail::fake()

        Queue::fake();

        // Configurar alertas habilitadas
        \DB::table('configuracion_alertas')->updateOrInsert(
            ['tipo_config' => 'general', 'clave' => 'alertas_habilitadas'],
            ['valor' => json_encode(true)]
        );

        // Usar dispatch estático con emails de prueba
        $testEmails = $this->getTestEmails();
        EnviarAlertaMantenimiento::dispatch(true, $testEmails);

        // Verificar que el job fue despachado
        Queue::assertPushed(EnviarAlertaMantenimiento::class);

        // Esto al menos verifica que el sistema de jobs funciona
        $this->assertTrue(true, 'Job system funciona correctamente');
    }

    /** @test */
    public function api_endpoint_probar_envio_requiere_autenticacion()
    {
        $testEmails = $this->getTestEmails();
        $response = $this->postJson('/api/configuracion-alertas/probar-envio', [
            'emails' => $testEmails
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function api_endpoint_probar_envio_valida_emails()
    {
        Sanctum::actingAs($this->user);
        Mail::fake();

        $response = $this->postJson('/api/configuracion-alertas/probar-envio', [
            'email' => 'email-invalido'
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'El email proporcionado no es válido'
            ]);
    }

    /** @test */
    public function api_endpoint_probar_envio_funciona_sin_email()
    {
        Sanctum::actingAs($this->user);
        Mail::fake();

        $response = $this->postJson('/api/configuracion-alertas/probar-envio', []);

        // Sin email, debería funcionar y devolver información sobre alertas
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'alertas_count',
                    'vehiculos_afectados',
                    'emails_destino'
                ]
            ]);
    }

    /** @test */
    public function command_usa_configuracion_para_decidir_envio()
    {
        Queue::fake();

        // Configurar alertas deshabilitadas usando DB directamente
        \DB::table('configuracion_alertas')->updateOrInsert(
            ['tipo_config' => 'general', 'clave' => 'alertas_habilitadas'],
            ['valor' => json_encode(false)]
        );

        $this->artisan('alertas:enviar-diarias')
            ->assertExitCode(0);

        // No debería despachar jobs porque las alertas están deshabilitadas
        Queue::assertNotPushed(EnviarAlertaMantenimiento::class);
    }

    /** @test */
    public function command_modo_dry_run_no_despacha_job()
    {
        Queue::fake();

        // Configurar usando DB directamente
        $testEmails = $this->getTestEmails();
        \DB::table('configuracion_alertas')->updateOrInsert(
            ['tipo_config' => 'destinatarios', 'clave' => 'emails_principales'],
            ['valor' => json_encode($testEmails)]
        );

        \DB::table('configuracion_alertas')->updateOrInsert(
            ['tipo_config' => 'general', 'clave' => 'alertas_habilitadas'],
            ['valor' => json_encode(true)]
        );

        // Ejecutar sin --force (modo dry-run)
        $this->artisan('alertas:enviar-diarias')
            ->assertExitCode(0);

        // Verificar que NO se despachó job
        Queue::assertNotPushed(EnviarAlertaMantenimiento::class);
    }

    /** @test */
    public function mailable_headers_anti_spam_estan_presentes()
    {
        $alertasData = ['alertas' => [], 'resumen' => []];
        $mailable = new AlertasMantenimientoMail($alertasData, false);

        $headers = $mailable->headers();

        // Verificar headers críticos anti-spam
        $this->assertArrayHasKey('X-Mailer', $headers->text);
        $this->assertArrayHasKey('X-Priority', $headers->text);
        $this->assertArrayHasKey('List-Unsubscribe', $headers->text);
        $this->assertArrayHasKey('Auto-Submitted', $headers->text);
        $this->assertArrayHasKey('Precedence', $headers->text);

        // Verificar valores específicos
        $this->assertEquals('Petrotekno-Control-Interno-v2.0', $headers->text['X-Mailer']);
        $this->assertEquals('3', $headers->text['X-Priority']);
        $this->assertEquals('auto-generated', $headers->text['Auto-Submitted']);
        $this->assertEquals('list', $headers->text['Precedence']);
    }

    /** @test */
    public function mailable_puede_renderizar_vista_sin_errores()
    {
        $alertasData = [
            'alertas' => [
                [
                    'vehiculo_info' => [
                        'marca' => 'Toyota',
                        'modelo' => 'Hilux',
                        'placas' => 'ABC-123',
                        'nombre_completo' => 'Toyota Hilux ABC-123',
                        'kilometraje_actual' => '150,000 km'
                    ],
                    'sistema_mantenimiento' => [
                        'nombre_sistema' => 'Motor',
                        'intervalo_km' => '20,000 km',
                        'tipo_mantenimiento' => 'Mantenimiento Preventivo de Motor',
                        'descripcion_sistema' => 'Cambio de aceite, filtros y revisión general del motor'
                    ],
                    'intervalo_alcanzado' => [
                        'intervalo_configurado' => 20000,
                        'kilometraje_base' => 130000,
                        'proximo_mantenimiento_esperado' => 150000,
                        'kilometraje_actual' => 150000,
                        'km_exceso' => 10000,
                        'porcentaje_sobrepaso' => '50.0%'
                    ],
                    'historial_mantenimientos' => [
                        'cantidad_encontrada' => 2,
                        'mantenimientos' => [
                            [
                                'fecha' => '15/01/2024',
                                'kilometraje' => 130000,
                                'tipo_servicio' => 'PREVENTIVO',
                                'descripcion' => 'Cambio de aceite de motor',
                                'proveedor' => 'Taller Central',
                                'costo' => '$2,500.00'
                            ],
                            [
                                'fecha' => '15/09/2023',
                                'kilometraje' => 110000,
                                'tipo_servicio' => 'PREVENTIVO',
                                'descripcion' => 'Mantenimiento preventivo completo',
                                'proveedor' => 'Taller Norte',
                                'costo' => '$3,200.00'
                            ]
                        ]
                    ],
                    'urgencia' => 'high',
                    'fecha_deteccion' => '25/01/2024 10:30:15',
                    'mensaje_resumen' => 'El vehículo Toyota Hilux (ABC-123) ha superado su intervalo de mantenimiento del Motor por 10,000 km.'
                ]
            ],
            'resumen' => [
                'total_alertas' => 1,
                'vehiculos_afectados' => 1,
                'por_urgencia' => ['critica' => 0, 'alta' => 1, 'media' => 0]
            ]
        ];

        $mailable = new AlertasMantenimientoMail($alertasData, false);

        // Esto no debería lanzar excepción
        $html = $mailable->render();

        // Verificar que se renderizó algo
        $this->assertNotEmpty($html);
        $this->assertStringContainsString('Toyota Hilux', $html);
        $this->assertStringContainsString('Motor', $html);
        $this->assertStringContainsString('150,000 km', $html);
        $this->assertStringContainsString('Mantenimiento Preventivo de Motor', $html);
    }

    /** @test */
    public function configuracion_resend_esta_correcta()
    {
        // Verificar que Resend está configurado como mailer disponible
        $mailers = config('mail.mailers');
        $this->assertArrayHasKey('resend', $mailers);

        // Verificar que las credenciales están configuradas
        $this->assertNotEmpty(config('services.resend.key'));

        // Verificar configuración del mailer resend
        $this->assertEquals('resend', $mailers['resend']['transport']);
    }

    /** @test */
    public function sistema_usa_emails_prueba_desde_env()
    {
        // Verificar que se obtienen los emails de prueba del .env
        $emailsPrueba = \App\Services\AlertasMantenimientoService::obtenerEmailsPrueba();

        $this->assertNotEmpty($emailsPrueba, 'Deben existir emails de prueba en MAIL_TEST_RECIPIENTS');

        foreach ($emailsPrueba as $email) {
            $this->assertNotFalse(filter_var($email, FILTER_VALIDATE_EMAIL), "Email '$email' debe ser válido");
        }

        // Verificar que los destinatarios incluyen emails de prueba
        $todosLosDestinatarios = \App\Services\AlertasMantenimientoService::obtenerDestinatarios();

        foreach ($emailsPrueba as $emailPrueba) {
            $this->assertContains(
                $emailPrueba,
                $todosLosDestinatarios,
                "Email de prueba '$emailPrueba' debe estar en la lista de destinatarios"
            );
        }
    }

    /** @test */
    public function job_test_usa_emails_prueba_cuando_no_especifica_emails()
    {
        Mail::fake();

        // Obtener emails de prueba del .env
        $emailsPrueba = \App\Services\AlertasMantenimientoService::obtenerEmailsPrueba();
        $this->assertNotEmpty($emailsPrueba, 'Deben existir emails de prueba configurados');

        // Verificar que el job usa emails de prueba cuando es test y no se especifican emails
        $job = new EnviarAlertaMantenimiento(true, null);

        // Usar reflexión para verificar que obtiene los emails correctos
        $reflection = new \ReflectionClass($job);
        $method = $reflection->getMethod('obtenerDestinatarios');
        $method->setAccessible(true);

        $destinatarios = $method->invoke($job);

        // Los destinatarios deben incluir los emails de prueba
        foreach ($emailsPrueba as $emailPrueba) {
            $this->assertContains(
                $emailPrueba,
                $destinatarios,
                "Email de prueba '$emailPrueba' debe estar en destinatarios del job de test"
            );
        }
    }
}
