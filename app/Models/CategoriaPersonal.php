<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaPersonal extends Model
{
    protected $table = 'categorias_personal';
    
    protected $fillable = [
        'nombre_categoria'
    ];

    /**
     * Relación con Personal
     */
    public function personal(): HasMany
    {
        return $this->hasMany(Personal::class, 'categoria_id');
    }
}