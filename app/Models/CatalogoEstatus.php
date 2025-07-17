<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatalogoEstatus extends Model
{
    use HasFactory;
    /**
     * Nombre de la tabla
     */
    protected $table = 'catalogo_estatus';

    /**
     * Campos asignables masivamente
     */
    protected $fillable = [
        'nombre_estatus',
        'descripcion',
        'activo',
    ];

    /**
     * Campos que deben ser convertidos a tipos nativos
     */
    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Relación: Un estatus puede tener muchos vehículos
     */
    public function vehiculos(): HasMany
    {
        return $this->hasMany(Vehiculo::class, 'estatus_id');
    }

    /**
     * Scope para obtener solo estatus activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Accessor para el nombre del estatus en mayúsculas
     */
    public function getNombreEstatusAttribute($value)
    {
        return ucfirst($value);
    }
}
