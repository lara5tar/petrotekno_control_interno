<?php

namespace App\Services;

use App\Models\ConfiguracionAlerta;
use Illuminate\Support\Facades\Cache;

class ConfiguracionAlertasService
{
    /**
     * Obtener configuración por tipo y clave
     */
    public static function get(string $tipo, string $clave, $default = null)
    {
        $cacheKey = "config_alerta_{$tipo}_{$clave}";

        return Cache::remember($cacheKey, 300, function () use ($tipo, $clave, $default) {
            $config = ConfiguracionAlerta::tipo($tipo)
                ->clave($clave)
                ->activo()
                ->first();

            return $config ? $config->valor : $default;
        });
    }

    /**
     * Obtener emails de destino
     */
    public static function getEmailsDestino(): array
    {
        $principales = self::get('destinatarios', 'emails_principales', []);
        $copia = self::get('destinatarios', 'emails_copia', []);

        return [
            'to' => is_array($principales) ? $principales : [$principales],
            'cc' => is_array($copia) ? $copia : ($copia ? [$copia] : [])
        ];
    }

    /**
     * Verificar si debe enviar alerta inmediata
     */
    public static function debeEnviarAlertaInmediata(): bool
    {
        return self::get('general', 'alerta_inmediata', true);
    }

    /**
     * Verificar si debe enviar recordatorios
     */
    public static function debeEnviarRecordatorios(): bool
    {
        return self::get('general', 'recordatorios_activos', true);
    }

    /**
     * Obtener hora de envío diario
     */
    public static function getHoraEnvioDiario(): string
    {
        return self::get('horarios', 'hora_envio_diario', '08:00');
    }

    /**
     * Obtener días activos para envío
     */
    public static function getDiasActivosEnvio(): array
    {
        return self::get('horarios', 'dias_semana', ['lunes', 'martes', 'miercoles', 'jueves', 'viernes']);
    }

    /**
     * Obtener cooldown en horas
     */
    public static function getCooldownHoras(): int
    {
        return (int) self::get('general', 'cooldown_horas', 4);
    }

    /**
     * Verificar si hoy es día activo para envío
     */
    public static function esHoyDiaActivo(): bool
    {
        $diasActivos = self::getDiasActivosEnvio();
        $hoy = strtolower(now()->locale('es')->translatedFormat('l'));

        return in_array($hoy, $diasActivos);
    }

    /**
     * Actualizar configuración
     */
    public static function actualizar(string $tipo, string $clave, $valor, string $descripcion = null): bool
    {
        try {
            // Convertir array a JSON si es necesario
            if (is_array($valor)) {
                $valor = json_encode($valor);
            }

            ConfiguracionAlerta::updateOrCreate(
                [
                    'tipo_config' => $tipo,
                    'clave' => $clave
                ],
                [
                    'valor' => $valor,
                    'descripcion' => $descripcion,
                    'activo' => true
                ]
            );

            // Limpiar caché
            Cache::forget("config_alerta_{$tipo}_{$clave}");

            return true;
        } catch (\Exception $e) {
            \Log::error('Error actualizando configuración de alertas', [
                'tipo' => $tipo,
                'clave' => $clave,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Obtener todas las configuraciones agrupadas por tipo
     */
    public static function obtenerTodas(): array
    {
        $configuraciones = ConfiguracionAlerta::activo()->get()->groupBy('tipo_config');

        $resultado = [];
        foreach ($configuraciones as $tipo => $configs) {
            $resultado[$tipo] = [];
            foreach ($configs as $config) {
                $resultado[$tipo][$config->clave] = [
                    'valor' => $config->valor,
                    'descripcion' => $config->descripcion
                ];
            }
        }

        return $resultado;
    }
}
