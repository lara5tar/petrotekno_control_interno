<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class GeneradorReportesPDF
{
    /**
     * Generar reporte PDF de alertas de mantenimiento
     */
    public static function generarReporteAlertas(array $alertas, array $resumen): string
    {
        try {
            $fechaGeneracion = now()->format('d/m/Y H:i:s');
            $nombreArchivo = 'alertas-mantenimiento-' . now()->format('Y-m-d-His') . '.pdf';
            $rutaCompleta = storage_path('app/reportes/' . $nombreArchivo);

            // Crear directorio si no existe
            $directorioReportes = storage_path('app/reportes');
            if (!is_dir($directorioReportes)) {
                mkdir($directorioReportes, 0755, true);
            }

            // Configurar datos para la vista
            $data = [
                'alertas' => $alertas,
                'resumen' => $resumen,
                'fechaGeneracion' => $fechaGeneracion,
                'totalPaginas' => ceil(count($alertas) / 2) ?: 1
            ];

            // Generar PDF
            $pdf = PDF::loadView('pdf.alertas-mantenimiento', $data);
            
            // Configurar opciones del PDF
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'isRemoteEnabled' => true,
                'dpi' => 150,
                'defaultMediaType' => 'print',
                'isFontSubsettingEnabled' => true
            ]);

            // Guardar el PDF
            $pdf->save($rutaCompleta);

            Log::info('Reporte PDF generado exitosamente', [
                'archivo' => $nombreArchivo,
                'ruta' => $rutaCompleta,
                'total_alertas' => count($alertas),
                'vehiculos_afectados' => $resumen['vehiculos_afectados'] ?? 0,
                'tamaño_archivo' => filesize($rutaCompleta) . ' bytes'
            ]);

            return $rutaCompleta;

        } catch (\Exception $e) {
            Log::error('Error generando reporte PDF', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'total_alertas' => count($alertas)
            ]);

            // Fallback: crear archivo de texto
            return self::generarReporteTextoFallback($alertas, $resumen);
        }
    }

    /**
     * Generar reporte de texto como fallback
     */
    private static function generarReporteTextoFallback(array $alertas, array $resumen): string
    {
        $fechaGeneracion = now()->format('d/m/Y H:i:s');
        $nombreArchivo = 'alertas-mantenimiento-fallback-' . now()->format('Y-m-d-His') . '.txt';
        $rutaCompleta = storage_path('app/reportes/' . $nombreArchivo);

        $contenido = "REPORTE DE ALERTAS DE MANTENIMIENTO - PETROTEKNO\n";
        $contenido .= "=" . str_repeat("=", 60) . "\n";
        $contenido .= "Fecha: {$fechaGeneracion}\n\n";

        $contenido .= "RESUMEN EJECUTIVO:\n";
        $contenido .= "- Total de alertas: " . ($resumen['total_alertas'] ?? 0) . "\n";
        $contenido .= "- Vehículos afectados: " . ($resumen['vehiculos_afectados'] ?? 0) . "\n";
        $contenido .= "- Alertas críticas: " . ($resumen['por_urgencia']['critica'] ?? 0) . "\n";
        $contenido .= "- Alertas altas: " . ($resumen['por_urgencia']['alta'] ?? 0) . "\n";
        $contenido .= "- Alertas normales: " . ($resumen['por_urgencia']['normal'] ?? 0) . "\n\n";

        if (count($alertas) > 0) {
            $contenido .= "DETALLE DE ALERTAS:\n";
            $contenido .= str_repeat("-", 60) . "\n\n";

            foreach ($alertas as $index => $alerta) {
                $contenido .= ($index + 1) . ". VEHÍCULO: " . $alerta['vehiculo_info']['nombre_completo'] . "\n";
                $contenido .= "   Sistema: " . $alerta['sistema_mantenimiento']['nombre_sistema'] . "\n";
                $contenido .= "   Urgencia: " . strtoupper($alerta['urgencia']) . "\n";
                $contenido .= "   Kilometraje actual: " . $alerta['vehiculo_info']['kilometraje_actual'] . "\n";
                $contenido .= "   Vencido por: " . number_format($alerta['intervalo_alcanzado']['km_exceso']) . " km\n";
                $contenido .= "   Próximo mantenimiento esperado: " . number_format($alerta['intervalo_alcanzado']['proximo_mantenimiento_esperado']) . " km\n";
                $contenido .= "   Descripción: " . $alerta['mensaje_resumen'] . "\n";
                $contenido .= "\n";
            }
        } else {
            $contenido .= "✅ NO HAY ALERTAS PENDIENTES\n";
            $contenido .= "Todos los vehículos están al día con sus mantenimientos.\n\n";
        }

        $contenido .= str_repeat("=", 60) . "\n";
        $contenido .= "Sistema de Control Interno - PetroTekno\n";
        $contenido .= "Reporte generado automáticamente\n";

        file_put_contents($rutaCompleta, $contenido);

        Log::warning('Se generó reporte de texto como fallback', [
            'archivo_fallback' => $nombreArchivo,
            'motivo' => 'Error en generación de PDF'
        ]);

        return $rutaCompleta;
    }

    /**
     * Limpiar reportes antiguos (más de 30 días)
     */
    public static function limpiarReportesAntiguos(): int
    {
        try {
            $directorioReportes = storage_path('app/reportes');
            if (!is_dir($directorioReportes)) {
                return 0;
            }

            $archivos = glob($directorioReportes . '/alertas-mantenimiento-*.{pdf,txt}', GLOB_BRACE);
            $eliminados = 0;
            $fechaLimite = now()->subDays(30)->timestamp;

            foreach ($archivos as $archivo) {
                if (filemtime($archivo) < $fechaLimite) {
                    if (unlink($archivo)) {
                        $eliminados++;
                    }
                }
            }

            if ($eliminados > 0) {
                Log::info('Reportes antiguos eliminados', [
                    'archivos_eliminados' => $eliminados,
                    'dias_antiguedad' => 30
                ]);
            }

            return $eliminados;

        } catch (\Exception $e) {
            Log::error('Error limpiando reportes antiguos', [
                'error' => $e->getMessage()
            ]);

            return 0;
        }
    }
}
