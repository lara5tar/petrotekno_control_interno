<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogAccion extends Model
{
    protected $table = 'log_acciones';

    protected $fillable = [
        'usuario_id',
        'fecha_hora',
        'accion',
        'tabla_afectada',
        'registro_id',
        'detalles',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_hora' => 'datetime',
        'detalles' => 'array',
    ];

    /**
     * Boot method to set fecha_hora automatically if not provided
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($logAccion) {
            if (!$logAccion->fecha_hora) {
                $logAccion->fecha_hora = now();
            }
        });
    }

    /**
     * Relación con User
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
