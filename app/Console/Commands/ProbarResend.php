<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\AlertasMantenimientoMail;
use Resend\Laravel\Facades\Resend;
use Exception;

class ProbarResend extends Command
{
    protected $signature = 'test:resend {email} {--method=facade : Método a usar (facade|mail)}';
    protected $description = 'Probar envío de emails con Resend usando diferentes métodos';

    public function handle()
    {
        $email = $this->argument('email');
        $method = $this->option('method');

        $this->info("🚀 Probando Resend con método: {$method}");
        $this->info("📧 Email destino: {$email}");

        // Verificar configuración
        $this->newLine();
        $this->info("🔧 Verificando configuración...");

        $apiKey = config('services.resend.key');
        if (empty($apiKey)) {
            $this->error("❌ RESEND_API_KEY no está configurado en .env");
            $this->info("💡 Agrega: RESEND_API_KEY=re_xxxxxxxxx");
            return Command::FAILURE;
        }

        $this->table(
            ['Configuración', 'Valor'],
            [
                ['Default Mailer', config('mail.default')],
                ['Resend API Key', '***' . substr($apiKey, -6)],
                ['From Address', config('mail.from.address')],
                ['From Name', config('mail.from.name')],
            ]
        );

        try {
            $this->newLine();

            if ($method === 'facade') {
                $this->info("📧 Enviando con Resend Facade...");
                $this->enviarConFacade($email);
            } else {
                $this->info("📧 Enviando con Laravel Mail...");
                $this->enviarConMail($email);
            }

            $this->info("✅ ¡Email enviado exitosamente!");
        } catch (Exception $e) {
            $this->error("❌ Error enviando email:");
            $this->error($e->getMessage());

            $this->newLine();
            $this->warn("🔍 Posibles soluciones:");
            $this->line("   • Verifica que RESEND_API_KEY esté configurado correctamente");
            $this->line("   • Asegúrate de que el dominio esté verificado en Resend");
            $this->line("   • Revisa que el email FROM esté en un dominio verificado");

            return Command::FAILURE;
        }

        $this->newLine();
        $this->info("📋 Información:");
        $this->line("   🔍 Revisa tu bandeja de entrada en: {$email}");
        $this->line("   📂 También revisa carpetas de Spam, Promociones");
        $this->line("   ⚡ Los emails con Resend llegan en segundos");

        return Command::SUCCESS;
    }

    private function enviarConFacade(string $email): void
    {
        // Datos de prueba para el email
        $datosTest = [
            'alertas' => [
                [
                    'vehiculo_id' => 999,
                    'vehiculo_info' => [
                        'marca' => 'Tesla',
                        'modelo' => 'Cybertruck',
                        'placas' => 'RESEND-001',
                        'nombre_completo' => 'Tesla Cybertruck - RESEND-001',
                        'kilometraje_actual' => '50,000 km'
                    ],
                    'sistema_mantenimiento' => [
                        'nombre_sistema' => 'Motor Electrico',
                        'intervalo_km' => '10,000 km',
                        'tipo_mantenimiento' => 'Mantenimiento Preventivo de Motor Eléctrico',
                        'descripcion_sistema' => 'Revisión de batería, sistemas eléctricos y actualizaciones'
                    ],
                    'intervalo_alcanzado' => [
                        'intervalo_configurado' => 10000,
                        'kilometraje_base' => 45000,
                        'proximo_mantenimiento_esperado' => 55000,
                        'kilometraje_actual' => 50000,
                        'km_exceso' => 5000,
                        'porcentaje_sobrepaso' => '50.0%'
                    ],
                    'historial_mantenimientos' => [
                        'cantidad_encontrada' => 1,
                        'mantenimientos' => [
                            [
                                'fecha' => '15/06/2025',
                                'kilometraje' => 45000,
                                'tipo_servicio' => 'PREVENTIVO',
                                'descripcion' => 'Actualización de software y revisión de batería',
                                'proveedor' => 'Tesla Service Center',
                                'costo' => '$3,500.00'
                            ]
                        ]
                    ],
                    'urgencia' => 'medium',
                    'fecha_deteccion' => now()->format('d/m/Y H:i:s'),
                    'mensaje_resumen' => '🔋 El vehículo Tesla Cybertruck (RESEND-001) requiere revisión - Enviado con Resend!'
                ]
            ],
            'resumen' => [
                'total_alertas' => 1,
                'vehiculos_afectados' => 1,
                'por_urgencia' => ['critica' => 0, 'alta' => 0, 'media' => 1],
                'por_sistema' => ['Motor Eléctrico' => 1],
            ],
        ];

        // Renderizar el mailable para obtener el HTML
        $mailable = new AlertasMantenimientoMail($datosTest, true);
        $html = $mailable->render();

        // Enviar directamente con Resend Facade
        Resend::emails()->send([
            'from' => config('mail.from.name') . ' <' . config('mail.from.address') . '>',
            'to' => [$email],
            'subject' => '[TEST RESEND] Alertas de Mantenimiento - Petrotekno',
            'html' => $html,
            'tags' => [
                'environment' => config('app.env'),
                'type' => 'test',
                'service' => 'resend-facade'
            ]
        ]);
    }

    private function enviarConMail(string $email): void
    {
        // Forzar usar Resend como mailer
        config(['mail.default' => 'resend']);

        // Datos de prueba
        $alertasData = [
            'alertas' => [
                [
                    'vehiculo_info' => [
                        'marca' => 'Mercedes',
                        'modelo' => 'Actros',
                        'placas' => 'RSN-002',
                        'nombre_completo' => 'Mercedes Actros - RSN-002'
                    ],
                    'sistema' => 'Motor Diesel',
                    'urgencia' => 'critica',
                    'kilometraje_actual' => 120000,
                    'ultimo_mantenimiento' => [
                        'fecha' => '10/06/2025',
                        'kilometraje' => 100000,
                        'descripcion' => 'Cambio de aceite y filtros'
                    ],
                    'km_exceso' => 20000,
                    'mensaje' => '🚛 Mantenimiento urgente requerido - Enviado con Laravel Mail + Resend!'
                ]
            ],
            'resumen' => [
                'total_alertas' => 1,
                'vehiculos_afectados' => 1,
                'por_urgencia' => ['critica' => 1, 'alta' => 0, 'media' => 0],
                'por_sistema' => ['Motor Diesel' => 1],
            ],
        ];

        // Enviar con Laravel Mail
        Mail::to($email)->send(new AlertasMantenimientoMail($alertasData, true));
    }
}
