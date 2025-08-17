<?php

namespace App\Jobs;

use App\Models\Vehiculo;
use App\Services\AlertasMantenimientoService;
use App\Services\ConfiguracionAlertasService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class VerificarAlertasVehiculo implements ShouldQueue
{
    use Queueable;

    public int $vehiculoId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $vehiculoId)
    {
        $this->vehiculoId = $vehiculoId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Verificando alertas para vehículo', ['vehiculo_id' => $this->vehiculoId]);

            // Verificar si las alertas inmediatas están habilitadas
            if (!ConfiguracionAlertasService::debeEnviarAlertaInmediata()) {
                Log::info('Alertas inmediatas deshabilitadas, omitiendo verificación', [
                    'vehiculo_id' => $this->vehiculoId
                ]);
                return;
            }

            // Verificar alertas para este vehículo específico
            $alertas = AlertasMantenimientoService::verificarVehiculo($this->vehiculoId);

            if (empty($alertas)) {
                Log::info('No se encontraron alertas para el vehículo', [
                    'vehiculo_id' => $this->vehiculoId
                ]);
                return;
            }

            // Verificar cooldown para evitar spam de emails
            $cooldownHoras = ConfiguracionAlertasService::getCooldownHoras();
            $ultimoEnvio = ConfiguracionAlertasService::get('general', 'ultimo_envio_inmediato_' . $this->vehiculoId);

            if ($ultimoEnvio) {
                $tiempoTranscurrido = now()->diffInHours($ultimoEnvio);
                if ($tiempoTranscurrido < $cooldownHoras) {
                    Log::info('Alerta omitida por cooldown', [
                        'vehiculo_id' => $this->vehiculoId,
                        'tiempo_transcurrido_horas' => $tiempoTranscurrido,
                        'cooldown_requerido' => $cooldownHoras
                    ]);
                    return;
                }
            }

            Log::info('Enviando alerta inmediata', [
                'vehiculo_id' => $this->vehiculoId,
                'total_alertas' => count($alertas)
            ]);

            // Preparar datos para el email
            $datosAlertas = [
                'alertas' => $alertas,
                'resumen' => AlertasMantenimientoService::obtenerResumen($alertas)
            ];

            // Enviar email inmediato
            EnviarAlertaMantenimiento::dispatch(false, null, $datosAlertas)
                ->onQueue('emails');

            // Actualizar timestamp del último envío inmediato para este vehículo
            ConfiguracionAlertasService::actualizar(
                'general',
                'ultimo_envio_inmediato_' . $this->vehiculoId,
                now()->toISOString(),
                'Último envío de alerta inmediata para vehículo ' . $this->vehiculoId
            );

            Log::info('Alerta inmediata enviada exitosamente', [
                'vehiculo_id' => $this->vehiculoId,
                'alertas_enviadas' => count($alertas)
            ]);

        } catch (\Exception $e) {
            Log::error('Error verificando alertas de vehículo', [
                'vehiculo_id' => $this->vehiculoId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }
}
