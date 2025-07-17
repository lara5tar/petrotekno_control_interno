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
        'detalles'
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'detalles' => 'array'
    ];

    /**
     * Relación con User
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
