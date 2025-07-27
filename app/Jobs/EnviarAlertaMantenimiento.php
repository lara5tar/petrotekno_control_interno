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

        // Si es test sin emails específicos, usar solo emails de prueba
        if ($this->esTest) {
            return AlertasMantenimientoService::obtenerEmailsPrueba();
        }

        // Para alertas reales, usar todos los destinatarios (configuración + prueba)
        return AlertasMantenimientoService::obtenerDestinatarios();
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
                        'nombre_completo' => 'Caterpillar 320D - TEST-001',
                        'kilometraje_actual' => '15,000 km'
                    ],
                    'sistema_mantenimiento' => [
                        'nombre_sistema' => 'Motor',
                        'intervalo_km' => '10,000 km',
                        'tipo_mantenimiento' => 'Mantenimiento Preventivo de Motor',
                        'descripcion_sistema' => 'Cambio de aceite, filtros y revisión general del motor'
                    ],
                    'intervalo_alcanzado' => [
                        'intervalo_configurado' => 10000,
                        'kilometraje_base' => 5000,
                        'proximo_mantenimiento_esperado' => 15000,
                        'kilometraje_actual' => 15000,
                        'km_exceso' => 5000,
                        'porcentaje_sobrepaso' => '50.0%'
                    ],
                    'historial_mantenimientos' => [
                        'cantidad_encontrada' => 1,
                        'mantenimientos' => [
                            [
                                'fecha' => '01/01/2025',
                                'kilometraje' => 5000,
                                'tipo_servicio' => 'PREVENTIVO',
                                'descripcion' => 'Cambio de aceite y filtros de motor',
                                'proveedor' => 'Taller de Prueba',
                                'costo' => '$1,500.00'
                            ]
                        ]
                    ],
                    'urgencia' => 'high',
                    'fecha_deteccion' => now()->format('d/m/Y H:i:s'),
                    'mensaje_resumen' => 'El vehículo Caterpillar 320D (TEST-001) ha superado su intervalo de mantenimiento del Motor por 5,000 km. Se esperaba mantenimiento en los 15,000 km, pero actualmente tiene 15,000 km.'
                ],
                [
                    'vehiculo_id' => 998,
                    'vehiculo_info' => [
                        'marca' => 'Komatsu',
                        'modelo' => 'PC200',
                        'placas' => 'TEST-002',
                        'nombre_completo' => 'Komatsu PC200 - TEST-002',
                        'kilometraje_actual' => '32,000 km'
                    ],
                    'sistema_mantenimiento' => [
                        'nombre_sistema' => 'Hidraulico',
                        'intervalo_km' => '10,000 km',
                        'tipo_mantenimiento' => 'Mantenimiento Preventivo del Sistema Hidráulico',
                        'descripcion_sistema' => 'Cambio de aceite hidráulico, filtros y revisión de mangueras'
                    ],
                    'intervalo_alcanzado' => [
                        'intervalo_configurado' => 10000,
                        'kilometraje_base' => 20000,
                        'proximo_mantenimiento_esperado' => 30000,
                        'kilometraje_actual' => 32000,
                        'km_exceso' => 2000,
                        'porcentaje_sobrepaso' => '20.0%'
                    ],
                    'historial_mantenimientos' => [
                        'cantidad_encontrada' => 2,
                        'mantenimientos' => [
                            [
                                'fecha' => '15/12/2024',
                                'kilometraje' => 20000,
                                'tipo_servicio' => 'PREVENTIVO',
                                'descripcion' => 'Cambio de aceite hidráulico y filtros',
                                'proveedor' => 'Servicio Especializado',
                                'costo' => '$2,800.00'
                            ],
                            [
                                'fecha' => '15/06/2024',
                                'kilometraje' => 10000,
                                'tipo_servicio' => 'PREVENTIVO',
                                'descripcion' => 'Mantenimiento preventivo del sistema hidráulico',
                                'proveedor' => 'Servicio Especializado',
                                'costo' => '$2,500.00'
                            ]
                        ]
                    ],
                    'urgencia' => 'medium',
                    'fecha_deteccion' => now()->format('d/m/Y H:i:s'),
                    'mensaje_resumen' => 'El vehículo Komatsu PC200 (TEST-002) ha superado su intervalo de mantenimiento del Hidráulico por 2,000 km. Se esperaba mantenimiento en los 30,000 km, pero actualmente tiene 32,000 km.'
                ],
            ],
            'resumen' => [
                'total_alertas' => 2,
                'vehiculos_afectados' => 2,
                'por_urgencia' => [
                    'critica' => 0,
                    'alta' => 1,
                    'media' => 1,
                ],
                'por_sistema' => [
                    'Motor' => 1,
                    'Hidráulico' => 1,
                ],
            ],
        ];
    }
}
