<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistorialOperadorVehiculo extends Model
{
    protected $table = 'historial_operador_vehiculo';

    protected $fillable = [
        'vehiculo_id',
        'operador_anterior_id',
        'operador_nuevo_id',
        'obra_id', // ← NUEVA COLUMNA
        'usuario_asigno_id',
        'fecha_asignacion',
        'tipo_movimiento',
        'observaciones',
        'motivo',
    ];

    protected $casts = [
        'fecha_asignacion' => 'datetime',
    ];

    /**
     * Tipos de movimiento disponibles
     */
    const TIPO_ASIGNACION_INICIAL = 'asignacion_inicial';
    const TIPO_CAMBIO_OPERADOR = 'cambio_operador';
    const TIPO_REMOCION_OPERADOR = 'remocion_operador';

    const TIPOS_MOVIMIENTO = [
        self::TIPO_ASIGNACION_INICIAL,
        self::TIPO_CAMBIO_OPERADOR,
        self::TIPO_REMOCION_OPERADOR,
    ];

    /**
     * Relación con el vehículo
     */
    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    /**
     * Relación con el operador anterior
     */
    public function operadorAnterior(): BelongsTo
    {
        return $this->belongsTo(Personal::class, 'operador_anterior_id');
    }

    /**
     * Relación con el operador nuevo
     */
    public function operadorNuevo(): BelongsTo
    {
        return $this->belongsTo(Personal::class, 'operador_nuevo_id');
    }

    /**
     * Relación con el usuario que hizo la asignación
     */
    public function usuarioAsigno(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'usuario_asigno_id');
    }

    /**
     * Relación con la obra
     */
    public function obra(): BelongsTo
    {
        return $this->belongsTo(Obra::class);
    }

    /**
     * Scope para filtrar por vehículo
     */
    public function scopePorVehiculo($query, $vehiculoId)
    {
        return $query->where('vehiculo_id', $vehiculoId);
    }

    /**
     * Scope para filtrar por tipo de movimiento
     */
    public function scopePorTipoMovimiento($query, $tipo)
    {
        return $query->where('tipo_movimiento', $tipo);
    }

    /**
     * Scope para ordenar por fecha más reciente
     */
    public function scopeRecientes($query)
    {
        return $query->orderBy('fecha_asignacion', 'desc');
    }

    /**
     * Obtiene una descripción legible del movimiento
     */
    public function getDescripcionMovimientoAttribute(): string
    {
        switch ($this->tipo_movimiento) {
            case self::TIPO_ASIGNACION_INICIAL:
                return 'Asignación inicial de operador';
            case self::TIPO_CAMBIO_OPERADOR:
                return 'Cambio de operador';
            case self::TIPO_REMOCION_OPERADOR:
                return 'Remoción de operador';
            default:
                return 'Movimiento desconocido';
        }
    }

    /**
     * Obtiene el nombre del operador anterior si existe
     */
    public function getNombreOperadorAnteriorAttribute(): ?string
    {
        return $this->operadorAnterior?->nombre_completo;
    }

    /**
     * Obtiene el nombre del operador nuevo si existe
     */
    public function getNombreOperadorNuevoAttribute(): ?string
    {
        return $this->operadorNuevo?->nombre_completo;
    }

    /**
     * Registra un movimiento en el historial
     */
    public static function registrarMovimiento(
        int $vehiculoId,
        ?int $operadorAnteriorId,
        ?int $operadorNuevoId,
        int $usuarioAsignoId,
        string $tipoMovimiento,
        ?int $obraId = null, // ← NUEVO PARÁMETRO
        ?string $observaciones = null,
        ?string $motivo = null
    ): self {
        return self::create([
            'vehiculo_id' => $vehiculoId,
            'operador_anterior_id' => $operadorAnteriorId,
            'operador_nuevo_id' => $operadorNuevoId,
            'obra_id' => $obraId, // ← NUEVA COLUMNA
            'usuario_asigno_id' => $usuarioAsignoId,
            'fecha_asignacion' => now(),
            'tipo_movimiento' => $tipoMovimiento,
            'observaciones' => $observaciones,
            'motivo' => $motivo,
        ]);
    }

    /**
     * Obtiene el historial de obras por operador
     */
    public static function historialObrasPorOperador(int $operadorId)
    {
        return self::where('operador_nuevo_id', $operadorId)
            ->whereNotNull('obra_id')
            ->with(['obra', 'vehiculo', 'usuarioAsigno'])
            ->orderBy('fecha_asignacion', 'desc')
            ->get()
            ->groupBy('obra_id')
            ->map(function ($historial) {
                $primera = $historial->first();
                return [
                    'obra' => $primera->obra,
                    'primera_asignacion' => $primera->fecha_asignacion,
                    'total_asignaciones' => $historial->count(),
                    'vehiculos_asignados' => $historial->pluck('vehiculo')->unique('id'),
                    'historial_completo' => $historial
                ];
            });
    }

    /**
     * Obtiene el historial de operadores por obra
     */
    public static function historialOperadoresPorObra(int $obraId)
    {
        return self::where('obra_id', $obraId)
            ->whereNotNull('operador_nuevo_id')
            ->with(['operadorNuevo', 'vehiculo', 'usuarioAsigno'])
            ->orderBy('fecha_asignacion', 'desc')
            ->get()
            ->groupBy('operador_nuevo_id')
            ->map(function ($historial) {
                $primera = $historial->first();
                return [
                    'operador' => $primera->operadorNuevo,
                    'primera_asignacion' => $primera->fecha_asignacion,
                    'total_asignaciones' => $historial->count(),
                    'vehiculos_operados' => $historial->pluck('vehiculo')->unique('id'),
                    'historial_completo' => $historial
                ];
            });
    }

    /**
     * Obtiene estadísticas de operador en una obra específica
     */
    public static function estadisticasOperadorEnObra(int $operadorId, int $obraId)
    {
        $historial = self::where('operador_nuevo_id', $operadorId)
            ->where('obra_id', $obraId)
            ->with(['vehiculo'])
            ->orderBy('fecha_asignacion', 'asc')
            ->get();

        if ($historial->isEmpty()) {
            return [
                'total_asignaciones' => 0,
                'primera_asignacion' => null,
                'ultima_asignacion' => null,
                'vehiculos_utilizados' => 0,
                'dias_trabajados' => 0,
                'vehiculos_operados' => collect(),
                'historial_detallado' => collect()
            ];
        }

        $primeraAsignacion = $historial->first()->fecha_asignacion;
        $ultimaAsignacion = $historial->last()->fecha_asignacion;
        $diasTrabajados = $primeraAsignacion->diffInDays($ultimaAsignacion) + 1;

        return [
            'total_asignaciones' => $historial->count(),
            'primera_asignacion' => $primeraAsignacion->format('d/m/Y H:i'),
            'ultima_asignacion' => $ultimaAsignacion->format('d/m/Y H:i'),
            'vehiculos_utilizados' => $historial->pluck('vehiculo_id')->unique()->count(),
            'dias_trabajados' => $diasTrabajados,
            'vehiculos_operados' => $historial->pluck('vehiculo')->unique('id'),
            'historial_detallado' => $historial
        ];
    }
}
