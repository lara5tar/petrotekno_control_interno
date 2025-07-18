<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $usuario_id
 * @property \Illuminate\Support\Carbon $fecha_hora
 * @property string $accion
 * @property string|null $tabla_afectada
 * @property int|null $registro_id
 * @property array<array-key, mixed>|null $detalles
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $usuario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogAccion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogAccion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogAccion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogAccion whereAccion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogAccion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogAccion whereDetalles($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogAccion whereFechaHora($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogAccion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogAccion whereRegistroId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogAccion whereTablaAfectada($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogAccion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogAccion whereUsuarioId($value)
 * @mixin \Eloquent
 */
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
            if (! $logAccion->fecha_hora) {
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
