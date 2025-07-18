<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatalogoTipoServicio extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'catalogo_tipos_servicio';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'nombre_tipo_servicio',
    ];

    /**
     * Get the mantenimientos for the tipo servicio.
     */
    public function mantenimientos(): HasMany
    {
        return $this->hasMany(Mantenimiento::class, 'tipo_servicio_id');
    }
}
