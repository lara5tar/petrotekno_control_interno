<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $nombre_completo
 * @property string $estatus
 * @property int $categoria_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\CategoriaPersonal $categoria
 * @property-read \App\Models\User|null $usuario
 *
 * @method static \Database\Factories\PersonalFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Personal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Personal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Personal onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Personal query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Personal whereCategoriaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Personal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Personal whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Personal whereEstatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Personal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Personal whereNombreCompleto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Personal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Personal withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Personal withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Personal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'personal';

    protected $fillable = [
        'nombre_completo',
        'estatus',
        'categoria_id',
    ];

    /**
     * @var array<string>
     */
    protected $dates = ['deleted_at'];

    /**
     * Relación con CategoriaPersonal
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaPersonal::class, 'categoria_id');
    }

    /**
     * Relación con User
     */
    public function usuario(): HasOne
    {
        return $this->hasOne(User::class, 'personal_id');
    }
}
