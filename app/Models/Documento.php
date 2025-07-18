<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Documento extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Atributos que se pueden asignar masivamente
     */
    protected $fillable = [
        'tipo_documento_id',
        'descripcion',
        'ruta_archivo',
        'fecha_vencimiento',
        'vehiculo_id',
        'personal_id',
        'obra_id',
        'mantenimiento_id',
    ];

    /**
     * Atributos que deben ser convertidos a tipos nativos
     */
    protected $casts = [
        'fecha_vencimiento' => 'date',
    ];

    /**
     * Relación con tipo de documento
     */
    public function tipoDocumento(): BelongsTo
    {
        return $this->belongsTo(CatalogoTipoDocumento::class, 'tipo_documento_id');
    }

    /**
     * Relación con vehículo (opcional)
     */
    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    /**
     * Relación con personal (opcional)
     */
    public function personal(): BelongsTo
    {
        return $this->belongsTo(Personal::class);
    }

    /**
     * Relación con obra (opcional)
     */
    public function obra(): BelongsTo
    {
        return $this->belongsTo(Obra::class);
    }

    /**
     * Relación con mantenimiento (opcional)
     */
    public function mantenimiento(): BelongsTo
    {
        return $this->belongsTo(Mantenimiento::class);
    }

    /**
     * Scope para documentos vencidos
     */
    public function scopeVencidos($query)
    {
        return $query->whereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento', '<', now());
    }

    /**
     * Scope para documentos próximos a vencer (30 días)
     */
    public function scopeProximosAVencer($query, $dias = 30)
    {
        $dias = (int) $dias; // Asegurar que sea entero
        $fechaLimite = now()->addDays($dias);

        return $query->whereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento', '<=', $fechaLimite)
            ->where('fecha_vencimiento', '>=', now());
    }

    /**
     * Scope para documentos por tipo
     */
    public function scopePorTipo($query, $tipoId)
    {
        return $query->where('tipo_documento_id', $tipoId);
    }

    /**
     * Scope para documentos de vehículo
     */
    public function scopeDeVehiculo($query, $vehiculoId)
    {
        return $query->where('vehiculo_id', $vehiculoId);
    }

    /**
     * Scope para documentos de personal
     */
    public function scopeDePersonal($query, $personalId)
    {
        return $query->where('personal_id', $personalId);
    }

    /**
     * Scope para documentos de obra
     */
    public function scopeDeObra($query, $obraId)
    {
        return $query->where('obra_id', $obraId);
    }

    /**
     * Scope para documentos de mantenimiento
     */
    public function scopeDeMantenimiento($query, $mantenimientoId)
    {
        return $query->where('mantenimiento_id', $mantenimientoId);
    }

    /**
     * Accessor para verificar si está vencido
     */
    public function getEstaVencidoAttribute(): bool
    {
        if (! $this->fecha_vencimiento) {
            return false;
        }

        return $this->fecha_vencimiento->isPast();
    }

    /**
     * Accessor para días hasta vencimiento
     */
    public function getDiasHastaVencimientoAttribute(): ?int
    {
        if (! $this->fecha_vencimiento) {
            return null;
        }

        return (int) now()->diffInDays($this->fecha_vencimiento, false);
    }

    /**
     * Accessor para estado del documento
     */
    public function getEstadoAttribute(): string
    {
        if (! $this->fecha_vencimiento) {
            return 'vigente';
        }

        $diasHastaVencimiento = $this->dias_hasta_vencimiento;

        if ($diasHastaVencimiento < 0) {
            return 'vencido';
        } elseif ($diasHastaVencimiento <= 30) {
            return 'proximo_a_vencer';
        } else {
            return 'vigente';
        }
    }
}
