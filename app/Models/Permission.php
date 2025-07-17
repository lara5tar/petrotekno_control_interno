<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $table = 'permisos';
    
    protected $fillable = [
        'nombre_permiso',
        'descripcion'
    ];

    public $timestamps = false;

    /**
     * Relación con Role (many to many)
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'roles_permisos', 'permiso_id', 'rol_id');
    }
}
