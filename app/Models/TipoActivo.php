<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoActivo extends Model
{
    protected $fillable = [
        'nombre',
        'tiene_kilometraje'
    ];

    protected $casts = [
        'tiene_kilometraje' => 'boolean'
    ];

    /**
     * Relación con Vehiculos
     */
    public function vehiculos(): HasMany
    {
        return $this->hasMany(Vehiculo::class, 'tipo_activo_id');
    }
}
