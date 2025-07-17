<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Personal extends Model
{
    use SoftDeletes;

    protected $table = 'personal';
    
    protected $fillable = [
        'nombre_completo',
        'estatus',
        'categoria_id'
    ];

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
