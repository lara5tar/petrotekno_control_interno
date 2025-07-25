<?php

namespace App\Jobs;

use App\Mail\AlertasMantenimientoMail;
use App\Services\AlertasMantenimientoService;
use App\Services\ConfiguracionAlertasService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EnviarAlertaMantenimiento implements ShouldQueue
{
    use Queueable;

    public bool $esTest;
    public ?array $emailsTest;

    /**
     * Create a new job instance.
     */
    public function __construct(bool $esTest = false, ?array $emailsTest = null)
    {
        $this->esTest = $esTest;
        $this->emailsTest = $emailsTest;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Obtener las alertas del sistema
            $alertasData = AlertasMantenimientoService::verificarTodosLosVehiculos();

            // Si es test y no hay alertas reales, crear datos simulados
            if ($this->esTest && empty($alertasData['alertas'])) {
                $alertasData = $this->generarDatosTest();
            }

            // Obtener destinatarios de correo
            $destinatarios = $this->obtenerDestinatarios();

            if (empty($destinatarios)) {
                Log::warning('No se encontraron destinatarios para enviar alertas de mantenimiento');
                return;
            }

            // Crear y enviar el correo
            $mail = new AlertasMantenimientoMail($alertasData, $this->esTest);

            foreach ($destinatarios as $email) {
                Mail::to($email)->send($mail);

                Log::info($this->esTest ? 'Email de prueba enviado' : 'Alerta de mantenimiento enviada', [
                    'destinatario' => $email,
                    'total_alertas' => $alertasData['resumen']['total_alertas'] ?? 0,
                    'vehiculos_afectados' => $alertasData['resumen']['vehiculos_afectados'] ?? 0,
                    'es_test' => $this->esTest,
                ]);
            }

            // Actualizar timestamp del último envío si no es test
            if (!$this->esTest) {
                ConfiguracionAlertasService::actualizar('general', 'ultimo_envio', now()->toISOString());
            }
        } catch (\Exception $e) {
            Log::error('Error al enviar alerta de mantenimiento', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'es_test' => $this->esTest,
            ]);
            throw $e;
        }
    }

    /**
     * Obtener lista de destinatarios para el correo
     */
    private function obtenerDestinatarios(): array
    {
        // Si es test y se proporcionaron emails específicos, usar esos
        if ($this->esTest && !empty($this->emailsTest)) {
            return $this->emailsTest;
        }

        // Obtener emails de la configuración
        $emailsPrincipales = ConfiguracionAlertasService::get('destinatarios', 'emails_principales');
        $emailsCopia = ConfiguracionAlertasService::get('destinatarios', 'emails_copia');

        $destinatarios = [];

        // Agregar emails principales
        if (is_array($emailsPrincipales)) {
            $destinatarios = array_merge($destinatarios, $emailsPrincipales);
        }

        // Agregar emails de copia solo si no es test
        if (!$this->esTest && is_array($emailsCopia)) {
            $destinatarios = array_merge($destinatarios, $emailsCopia);
        }

        // Filtrar emails válidos y únicos
        $destinatarios = array_unique(array_filter($destinatarios, function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        }));

        return array_values($destinatarios);
    }

    /**
     * Generar datos de prueba para el correo test
     */
    private function generarDatosTest(): array
    {
        return [
            'alertas' => [
                [
                    'vehiculo_id' => 999,
                    'vehiculo_info' => [
                        'marca' => 'Caterpillar',
                        'modelo' => '320D',
                        'placas' => 'TEST-001',
                        'nombre_completo' => 'Caterpillar 320D - TEST-001'
                    ],
                    'sistema' => 'Motor',
                    'urgencia' => 'critica',
                    'kilometraje_actual' => 15000,
                    'intervalo_configurado' => 10000,
                    'ultimo_mantenimiento' => [
                        'fecha' => '01/01/2025',
                        'kilometraje' => 5000,
                        'descripcion' => 'Cambio de aceite y filtros'
                    ],
                    'proximo_mantenimiento_km' => 15000,
                    'km_exceso' => 5000,
                    'dias_exceso' => 15,
                    'mensaje' => 'Mantenimiento de motor vencido hace 5000 km (50% de exceso)',
                ],
                [
                    'vehiculo_id' => 998,
                    'vehiculo_info' => [
                        'marca' => 'Komatsu',
                        'modelo' => 'PC200',
                        'placas' => 'TEST-002',
                        'nombre_completo' => 'Komatsu PC200 - TEST-002'
                    ],
                    'sistema' => 'Hidráulico',
                    'urgencia' => 'alta',
                    'kilometraje_actual' => 32000,
                    'intervalo_configurado' => 10000,
                    'ultimo_mantenimiento' => [
                        'fecha' => '15/12/2024',
                        'kilometraje' => 20000,
                        'descripcion' => 'Cambio de aceite hidráulico'
                    ],
                    'proximo_mantenimiento_km' => 30000,
                    'km_exceso' => 2000,
                    'dias_exceso' => 8,
                    'mensaje' => 'Mantenimiento hidráulico próximo a vencer (2000 km de exceso)',
                ],
            ],
            'resumen' => [
                'total_alertas' => 2,
                'vehiculos_afectados' => 2,
                'por_urgencia' => [
                    'critica' => 1,
                    'alta' => 1,
                    'media' => 0,
                ],
                'por_sistema' => [
                    'Motor' => 1,
                    'Hidráulico' => 1,
                ],
            ],
        ];
    }
}
