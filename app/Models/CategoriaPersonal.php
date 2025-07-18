<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $nombre_categoria
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Personal> $personal
 * @property-read int|null $personal_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoriaPersonal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoriaPersonal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoriaPersonal query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoriaPersonal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoriaPersonal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoriaPersonal whereNombreCategoria($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoriaPersonal whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class CategoriaPersonal extends Model
{
    use HasFactory;
    
    protected $table = 'categorias_personal';

    protected $fillable = [
        'nombre_categoria',
    ];

    /**
     * Relación con Personal
     */
    public function personal(): HasMany
    {
        return $this->hasMany(Personal::class, 'categoria_id');
    }
}
