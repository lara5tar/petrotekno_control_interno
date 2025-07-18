<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Mantenimiento extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'mantenimientos';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'vehiculo_id',
        'tipo_servicio_id',
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
     * Get the vehiculo that owns the mantenimiento.
     */
    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    /**
     * Get the tipo servicio that owns the mantenimiento.
     */
    public function tipoServicio(): BelongsTo
    {
        return $this->belongsTo(CatalogoTipoServicio::class, 'tipo_servicio_id');
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
     * Scope a query to only include mantenimientos by tipo servicio.
     */
    public function scopeByTipoServicio(Builder $query, int $tipoServicioId): Builder
    {
        return $query->where('tipo_servicio_id', $tipoServicioId);
    }

    /**
     * Scope a query to only include mantenimientos within a date range.
     */
    public function scopeByDateRange(Builder $query, ?string $startDate = null, ?string $endDate = null): Builder
    {
        if ($startDate) {
            $query->where('fecha_inicio', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('fecha_inicio', '<=', $endDate);
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
        if (!$this->fecha_fin) {
            return null;
        }

        return $this->fecha_inicio->diffInDays($this->fecha_fin);
    }

    /**
     * Check if the mantenimiento is completed.
     */
    public function getIsCompletadoAttribute(): bool
    {
        return !is_null($this->fecha_fin);
    }
}
