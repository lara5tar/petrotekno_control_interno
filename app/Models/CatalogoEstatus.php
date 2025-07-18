<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $nombre_estatus
 * @property string|null $descripcion
 * @property bool $activo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Vehiculo> $vehiculos
 * @property-read int|null $vehiculos_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogoEstatus activos()
 * @method static \Database\Factories\CatalogoEstatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogoEstatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogoEstatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogoEstatus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogoEstatus whereActivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogoEstatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogoEstatus whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogoEstatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogoEstatus whereNombreEstatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogoEstatus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
