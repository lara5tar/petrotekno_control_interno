<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
                'message' => 'Configuraciones generales actualizadas exitosamente'
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
    public function updateDestinatarios(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'emails_principales' => 'required|array|min:1',
            'emails_principales.*' => 'email',
            'emails_copia' => 'nullable|array',
            'emails_copia.*' => 'email',
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
                'destinatarios', 
                'emails_principales', 
                $configuraciones['emails_principales'],
                'Lista de emails principales para alertas'
            );
            
            ConfiguracionAlertasService::actualizar(
                'destinatarios', 
                'emails_copia', 
                $configuraciones['emails_copia'] ?? [],
                'Lista de emails en copia para alertas'
            );

            return response()->json([
                'success' => true,
                'message' => 'Configuraciones de destinatarios actualizadas exitosamente'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error actualizando configuraciones de destinatarios', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar configuraciones de destinatarios',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener resumen actual de alertas
     */
    public function resumenAlertas(): JsonResponse
    {
        try {
            $alertas = AlertasMantenimientoService::verificarTodosLosVehiculos();
            $resumen = AlertasMantenimientoService::obtenerResumen($alertas);
            
            return response()->json([
                'success' => true,
                'message' => 'Resumen de alertas obtenido exitosamente',
                'data' => [
                    'resumen' => $resumen,
                    'alertas' => $alertas
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
    public function probarEnvio(): JsonResponse
    {
        try {
            $alertas = AlertasMantenimientoService::verificarTodosLosVehiculos();
            $emails = ConfiguracionAlertasService::getEmailsDestino();
            
            if (empty($alertas)) {
                return response()->json([
                    'success' => true,
                    'message' => 'No hay alertas pendientes para enviar',
                    'data' => [
                        'alertas_count' => 0,
                        'emails_destino' => $emails
                    ]
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Simulación de envío completada',
                'data' => [
                    'alertas_count' => count($alertas),
                    'vehiculos_afectados' => count(array_unique(array_column($alertas, 'vehiculo_id'))),
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
