<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConfiguracionDestinatariosRequest;
use App\Services\AlertasMantenimientoService;
use App\Services\ConfiguracionAlertasService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ConfiguracionAlertasController extends Controller
{
    /**
     * Obtener todas las configuraciones de alertas
     */
    public function index(): JsonResponse
    {
        try {
            $configuraciones = ConfiguracionAlertasService::obtenerTodas();

            return response()->json([
                'success' => true,
                'message' => 'Configuraciones obtenidas exitosamente',
                'data' => $configuraciones
            ]);
        } catch (\Exception $e) {
            Log::error('Error obteniendo configuraciones de alertas', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener configuraciones',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar configuraciones generales
     */
    public function updateGeneral(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'alerta_inmediata' => 'required|boolean',
            'recordatorios_activos' => 'required|boolean',
            'cooldown_horas' => 'required|integer|min:1|max:24',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $configuraciones = $validator->validated();

            foreach ($configuraciones as $clave => $valor) {
                ConfiguracionAlertasService::actualizar(
                    'general',
                    $clave,
                    $valor ? 'true' : 'false',
                    $this->getDescripcionConfig('general', $clave)
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Configuraciones generales actualizadas exitosamente',
                'data' => $configuraciones
            ]);
        } catch (\Exception $e) {
            Log::error('Error actualizando configuraciones generales', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar configuraciones generales',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar configuraciones de horarios
     */
    public function updateHorarios(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'hora_envio_diario' => 'required|date_format:H:i',
            'dias_semana' => 'required|array|min:1',
            'dias_semana.*' => 'in:lunes,martes,miercoles,jueves,viernes,sabado,domingo',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $configuraciones = $validator->validated();

            ConfiguracionAlertasService::actualizar(
                'horarios',
                'hora_envio_diario',
                $configuraciones['hora_envio_diario'],
                'Hora del día para envío de recordatorios'
            );

            ConfiguracionAlertasService::actualizar(
                'horarios',
                'dias_semana',
                $configuraciones['dias_semana'],
                'Días de la semana para enviar alertas'
            );

            return response()->json([
                'success' => true,
                'message' => 'Configuraciones de horarios actualizadas exitosamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error actualizando configuraciones de horarios', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar configuraciones de horarios',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar configuraciones de destinatarios
     */
    public function updateDestinatarios(ConfiguracionDestinatariosRequest $request): JsonResponse
    {
        try {
            $configuraciones = $request->validated();

            // Actualizar emails principales
            ConfiguracionAlertasService::actualizar(
                'destinatarios',
                'emails_principales',
                $configuraciones['emails_principales'],
                'Lista de emails principales para alertas de mantenimiento'
            );

            // Actualizar emails de copia (puede ser vacío)
            ConfiguracionAlertasService::actualizar(
                'destinatarios',
                'emails_copia',
                $configuraciones['emails_copia'] ?? [],
                'Lista de emails en copia para alertas de mantenimiento'
            );

            // Opcional: configuraciones adicionales
            if (isset($configuraciones['notificar_inmediato'])) {
                ConfiguracionAlertasService::actualizar(
                    'destinatarios',
                    'notificar_inmediato',
                    $configuraciones['notificar_inmediato'],
                    'Notificar inmediatamente a estos destinatarios'
                );
            }

            if (isset($configuraciones['incluir_en_copia_diaria'])) {
                ConfiguracionAlertasService::actualizar(
                    'destinatarios',
                    'incluir_en_copia_diaria',
                    $configuraciones['incluir_en_copia_diaria'],
                    'Incluir en el reporte diario automático'
                );
            }

            Log::info('Configuración de destinatarios actualizada', [
                'emails_principales_count' => count($configuraciones['emails_principales']),
                'emails_copia_count' => count($configuraciones['emails_copia'] ?? []),
                'user_id' => auth('sanctum')->id() ?? 'system',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Configuraciones de destinatarios actualizadas exitosamente',
                'data' => [
                    'emails_principales' => $configuraciones['emails_principales'],
                    'emails_copia' => $configuraciones['emails_copia'] ?? [],
                    'total_destinatarios' => count($configuraciones['emails_principales']) + count($configuraciones['emails_copia'] ?? []),
                    'fecha_actualizacion' => now()->toISOString(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error actualizando configuraciones de destinatarios', [
                'error' => $e->getMessage(),
                'data' => $request->validated(),
                'user_id' => auth('sanctum')->id() ?? 'system',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar configuraciones de destinatarios',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Obtener resumen actual de alertas
     */
    public function resumenAlertas(): JsonResponse
    {
        try {
            $resultado = AlertasMantenimientoService::verificarTodosLosVehiculos();

            return response()->json([
                'success' => true,
                'message' => 'Resumen de alertas obtenido exitosamente',
                'data' => [
                    'resumen' => $resultado['resumen'],
                    'alertas' => $resultado['alertas']
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error obteniendo resumen de alertas', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener resumen de alertas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Probar envío de alertas (dry run)
     */
    public function probarEnvio(Request $request): JsonResponse
    {
        try {
            $email = $request->input('email');
            $mailer = $request->input('mailer', 'log');
            $enviarReal = $request->input('enviar_real', false);

            // Validar email si se proporciona
            if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El email proporcionado no es válido'
                ], 400);
            }

            $resultado = AlertasMantenimientoService::verificarTodosLosVehiculos();
            $alertas = $resultado['alertas'];
            $emails = AlertasMantenimientoService::obtenerDestinatarios();

            if (empty($alertas)) {
                return response()->json([
                    'success' => true,
                    'message' => 'No hay alertas pendientes para enviar',
                    'data' => [
                        'alertas_count' => 0,
                        'vehiculos_afectados' => 0,
                        'emails_destino' => $emails
                    ]
                ]);
            }

            // Si se solicita envío real
            if ($enviarReal && $email) {
                // Configurar mailer temporalmente
                $originalMailer = config('mail.default');
                config(['mail.default' => $mailer]);

                // Enviar correo usando el Job (solo al email específico para test)
                \App\Jobs\EnviarAlertaMantenimiento::dispatch(
                    true, // Es test
                    [$email] // Email específico de prueba
                )->onQueue('default');

                // Restaurar mailer original
                config(['mail.default' => $originalMailer]);

                return response()->json([
                    'success' => true,
                    'message' => 'Correo de prueba enviado exitosamente',
                    'data' => [
                        'email_enviado_a' => $email,
                        'mailer_usado' => $mailer,
                        'alertas_count' => count($alertas),
                        'vehiculos_afectados' => empty($alertas) ? 0 : count(array_unique(array_column($alertas, 'vehiculo_id'))),
                        'alertas_preview' => array_slice($alertas, 0, 3),
                        'emails_destino_configurados' => $emails
                    ]
                ]);
            }

            // Si se solicita envío real sin email específico, usar emails de prueba
            if ($enviarReal && !$email) {
                $emailsPrueba = AlertasMantenimientoService::obtenerEmailsPrueba();

                if (empty($emailsPrueba)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No hay emails de prueba configurados en MAIL_TEST_RECIPIENTS'
                    ], 400);
                }

                // Configurar mailer temporalmente
                $originalMailer = config('mail.default');
                config(['mail.default' => $mailer]);

                // Enviar correo usando el Job con emails de prueba
                \App\Jobs\EnviarAlertaMantenimiento::dispatch(
                    true, // Es test
                    $emailsPrueba // Emails de prueba del .env
                )->onQueue('default');

                // Restaurar mailer original
                config(['mail.default' => $originalMailer]);

                return response()->json([
                    'success' => true,
                    'message' => 'Correos de prueba enviados exitosamente',
                    'data' => [
                        'emails_enviados_a' => $emailsPrueba,
                        'mailer_usado' => $mailer,
                        'alertas_count' => count($alertas),
                        'vehiculos_afectados' => empty($alertas) ? 0 : count(array_unique(array_column($alertas, 'vehiculo_id'))),
                        'alertas_preview' => array_slice($alertas, 0, 3),
                        'emails_destino_configurados' => $emails
                    ]
                ]);
            }

            // Modo simulación (por defecto)
            return response()->json([
                'success' => true,
                'message' => 'Simulación de envío completada (usar enviar_real=true para envío real)',
                'data' => [
                    'alertas_count' => count($alertas),
                    'vehiculos_afectados' => empty($alertas) ? 0 : count(array_unique(array_column($alertas, 'vehiculo_id'))),
                    'emails_destino' => $emails,
                    'alertas_preview' => array_slice($alertas, 0, 5) // Primeras 5 alertas
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error en prueba de envío de alertas', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error en la prueba de envío',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enviar correo de prueba real
     */
    public function enviarCorreoPrueba(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'mailer' => 'sometimes|string|in:resend,log,array',
            'method' => 'sometimes|string|in:facade,mail',
        ]);

        try {
            $email = $request->input('email');
            $mailer = $request->input('mailer', config('mail.default'));
            $method = $request->input('method', 'mail');

            // Configurar mailer temporalmente si se especifica
            $originalMailer = config('mail.default');
            if ($mailer !== $originalMailer) {
                config(['mail.default' => $mailer]);
            }

            if ($method === 'facade' && $mailer === 'resend') {
                // Usar Resend Facade directamente
                $this->enviarConResendFacade($email);
            } else {
                // Usar Laravel Mail tradicional
                $job = new \App\Jobs\EnviarAlertaMantenimiento(true, [$email]);
                $job->handle();
            }

            // Restaurar configuración original
            if ($mailer !== $originalMailer) {
                config(['mail.default' => $originalMailer]);
            }

            Log::info('Correo de prueba enviado desde API', [
                'email' => $email,
                'mailer' => $mailer,
                'method' => $method,
                'user_id' => auth('sanctum')->id() ?? 'unknown',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Correo de prueba enviado exitosamente',
                'data' => [
                    'email_destino' => $email,
                    'mailer_usado' => $mailer,
                    'method_usado' => $method,
                    'fecha_envio' => now()->toISOString(),
                    'tipo' => 'prueba',
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error enviando correo de prueba desde API', [
                'error' => $e->getMessage(),
                'email' => $request->input('email'),
                'user_id' => auth('sanctum')->id() ?? 'unknown',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al enviar correo de prueba',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Enviar email usando Resend Facade directamente
     */
    private function enviarConResendFacade(string $email): void
    {
        $alertasData = [
            'alertas' => [
                [
                    'vehiculo_info' => [
                        'marca' => 'Volvo',
                        'modelo' => 'FH16',
                        'placas' => 'API-001',
                        'nombre_completo' => 'Volvo FH16 - API-001'
                    ],
                    'sistema' => 'Motor Turbo',
                    'urgencia' => 'alta',
                    'kilometraje_actual' => 80000,
                    'ultimo_mantenimiento' => [
                        'fecha' => '20/06/2025',
                        'kilometraje' => 70000,
                        'descripcion' => 'Mantenimiento de turbo'
                    ],
                    'km_exceso' => 10000,
                    'mensaje' => '🚛 Enviado desde API con Resend Facade'
                ]
            ],
            'resumen' => [
                'total_alertas' => 1,
                'vehiculos_afectados' => 1,
                'por_urgencia' => ['critica' => 0, 'alta' => 1, 'media' => 0],
                'por_sistema' => ['Motor Turbo' => 1],
            ],
        ];

        $mailable = new \App\Mail\AlertasMantenimientoMail($alertasData, true);
        $html = $mailable->render();

        \Resend\Laravel\Facades\Resend::emails()->send([
            'from' => config('mail.from.name') . ' <' . config('mail.from.address') . '>',
            'to' => [$email],
            'subject' => '[API TEST] Alertas de Mantenimiento - Solupatch',
            'html' => $html,
            'tags' => [
                'environment' => config('app.env'),
                'type' => 'api-test',
                'service' => 'resend-facade'
            ]
        ]);
    }

    /**
     * Obtener descripción de configuración
     */
    private function getDescripcionConfig(string $tipo, string $clave): string
    {
        $descripciones = [
            'general' => [
                'alerta_inmediata' => 'Enviar alerta al momento de detectar vencimiento',
                'recordatorios_activos' => 'Enviar recordatorios diarios',
                'cooldown_horas' => 'Horas de espera entre alertas del mismo vehículo'
            ],
            'horarios' => [
                'hora_envio_diario' => 'Hora del día para envío de recordatorios',
                'dias_semana' => 'Días de la semana para enviar alertas'
            ],
            'destinatarios' => [
                'emails_principales' => 'Lista de emails principales para alertas',
                'emails_copia' => 'Lista de emails en copia para alertas'
            ]
        ];

        return $descripciones[$tipo][$clave] ?? 'Configuración de alertas';
    }
}
