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
        return $this->belongsTo(User::class, 'usuario_asigno_id');
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
        ?string $observaciones = null,
        ?string $motivo = null
    ): self {
        return self::create([
            'vehiculo_id' => $vehiculoId,
            'operador_anterior_id' => $operadorAnteriorId,
            'operador_nuevo_id' => $operadorNuevoId,
            'usuario_asigno_id' => $usuarioAsignoId,
            'fecha_asignacion' => now(),
            'tipo_movimiento' => $tipoMovimiento,
            'observaciones' => $observaciones,
            'motivo' => $motivo,
        ]);
    }
}
