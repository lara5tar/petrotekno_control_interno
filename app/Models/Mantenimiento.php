<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mantenimiento extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'mantenimientos';

    /**
     * Tipos de servicio disponibles.
     */
    const TIPO_CORRECTIVO = 'CORRECTIVO';
    const TIPO_PREVENTIVO = 'PREVENTIVO';

    /**
     * Sistemas de vehículo disponibles.
     */
    const SISTEMA_MOTOR = 'motor';
    const SISTEMA_TRANSMISION = 'transmision';
    const SISTEMA_HIDRAULICO = 'hidraulico';
    const SISTEMA_GENERAL = 'general';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'vehiculo_id',
        'tipo_servicio',
        'sistema_vehiculo',
        'proveedor',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'kilometraje_servicio',
        'costo',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'costo' => 'decimal:2',
        'kilometraje_servicio' => 'integer',
    ];

    /**
     * Get available tipos de servicio.
     */
    public static function getTiposServicio(): array
    {
        return [
            self::TIPO_CORRECTIVO,
            self::TIPO_PREVENTIVO,
        ];
    }

    /**
     * Get available sistemas de vehículo.
     */
    public static function getSistemasVehiculo(): array
    {
        return [
            self::SISTEMA_MOTOR,
            self::SISTEMA_TRANSMISION,
            self::SISTEMA_HIDRAULICO,
            self::SISTEMA_GENERAL,
        ];
    }

    /**
     * Get the vehiculo that owns the mantenimiento.
     */
    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    /**
     * Get the documentos for the mantenimiento.
     */
    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class);
    }

    /**
     * Scope a query to only include mantenimientos for a specific vehiculo.
     */
    public function scopeByVehiculo(Builder $query, int $vehiculoId): Builder
    {
        return $query->where('vehiculo_id', $vehiculoId);
    }

    /**
     * Scope a query to only include mantenimientos for a specific vehiculo.
     * Alias for backwards compatibility with tests.
     */
    public function scopePorVehiculo(Builder $query, int $vehiculoId): Builder
    {
        return $this->scopeByVehiculo($query, $vehiculoId);
    }

    /**
     * Scope a query to filter by fecha.
     */
    public function scopePorFecha(Builder $query, $fecha): Builder
    {
        return $query->whereDate('fecha_inicio', $fecha);
    }

    /**
     * Scope a query to filter between dates.
     */
    public function scopeEntreFechas(Builder $query, $fechaInicio, $fechaFin): Builder
    {
        return $query->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin]);
    }

    /**
     * Scope a query to only include mantenimientos by tipo servicio.
     */
    public function scopeByTipoServicio(Builder $query, string $tipoServicio): Builder
    {
        return $query->where('tipo_servicio', $tipoServicio);
    }

    /**
     * Scope a query to only include mantenimientos correctivos.
     */
    public function scopeCorrectivos(Builder $query): Builder
    {
        return $query->where('tipo_servicio', self::TIPO_CORRECTIVO);
    }

    /**
     * Scope a query to only include mantenimientos preventivos.
     */
    public function scopePreventivos(Builder $query): Builder
    {
        return $query->where('tipo_servicio', self::TIPO_PREVENTIVO);
    }

    /**
     * Scope a query to filter by sistema de vehículo.
     */
    public function scopeBySistemaVehiculo(Builder $query, string $sistema): Builder
    {
        return $query->where('sistema_vehiculo', $sistema);
    }

    /**
     * Scope a query to only include mantenimientos de motor.
     */
    public function scopeMotor(Builder $query): Builder
    {
        return $query->where('sistema_vehiculo', self::SISTEMA_MOTOR);
    }

    /**
     * Scope a query to only include mantenimientos de transmisión.
     */
    public function scopeTransmision(Builder $query): Builder
    {
        return $query->where('sistema_vehiculo', self::SISTEMA_TRANSMISION);
    }

    /**
     * Scope a query to only include mantenimientos hidráulicos.
     */
    public function scopeHidraulico(Builder $query): Builder
    {
        return $query->where('sistema_vehiculo', self::SISTEMA_HIDRAULICO);
    }

    /**
     * Scope a query to only include mantenimientos generales.
     */
    public function scopeGeneral(Builder $query): Builder
    {
        return $query->where('sistema_vehiculo', self::SISTEMA_GENERAL);
    }

    /**
     * Scope a query to only include mantenimientos within a date range.
     */
    public function scopeByDateRange(Builder $query, ?string $startDate = null, ?string $endDate = null): Builder
    {
        if ($startDate) {
            $query->whereDate('fecha_inicio', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('fecha_inicio', '<=', $endDate);
        }

        return $query;
    }

    /**
     * Scope a query to only include completed mantenimientos.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->whereNotNull('fecha_fin');
    }

    /**
     * Scope a query to only include pending mantenimientos.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->whereNull('fecha_fin');
    }

    /**
     * Get the duration in days.
     */
    public function getDuracionDiasAttribute(): ?int
    {
        if (! $this->fecha_fin) {
            return null;
        }

        return (int) $this->fecha_inicio->diffInDays($this->fecha_fin);
    }

    /**
     * Get formatted cost.
     */
    public function getCostoFormateadoAttribute(): ?string
    {
        if (is_null($this->costo)) {
            return null;
        }

        return '$' . number_format((float) $this->costo, 2);
    }

    /**
     * Check if the mantenimiento is completed.
     */
    public function getIsCompletadoAttribute(): bool
    {
        return ! is_null($this->fecha_fin);
    }

    /**
     * Check if the mantenimiento is preventivo.
     */
    public function getIsPreventivo(): bool
    {
        return $this->tipo_servicio === self::TIPO_PREVENTIVO;
    }

    /**
     * Check if the mantenimiento is correctivo.
     */
    public function getIsCorrectivo(): bool
    {
        return $this->tipo_servicio === self::TIPO_CORRECTIVO;
    }

    /**
     * Check if the mantenimiento is de motor.
     */
    public function getIsMotor(): bool
    {
        return $this->sistema_vehiculo === self::SISTEMA_MOTOR;
    }

    /**
     * Check if the mantenimiento is de transmisión.
     */
    public function getIsTransmision(): bool
    {
        return $this->sistema_vehiculo === self::SISTEMA_TRANSMISION;
    }

    /**
     * Check if the mantenimiento is hidráulico.
     */
    public function getIsHidraulico(): bool
    {
        return $this->sistema_vehiculo === self::SISTEMA_HIDRAULICO;
    }

    /**
     * Check if the mantenimiento is general.
     */
    public function getIsGeneral(): bool
    {
        return $this->sistema_vehiculo === self::SISTEMA_GENERAL;
    }

    /**
     * Get sistema vehículo formatted.
     */
    public function getSistemaVehiculoFormateadoAttribute(): string
    {
        return match ($this->sistema_vehiculo) {
            self::SISTEMA_MOTOR => 'Motor',
            self::SISTEMA_TRANSMISION => 'Transmisión',
            self::SISTEMA_HIDRAULICO => 'Hidráulico',
            self::SISTEMA_GENERAL => 'General',
            default => ucfirst($this->sistema_vehiculo)
        };
    }
}
