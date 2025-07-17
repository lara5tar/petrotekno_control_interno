<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $table = 'roles';
    
    protected $fillable = [
        'nombre_rol',
        'descripcion'
    ];

    /**
     * Relación con Permission (many to many)
     */
    public function permisos(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'roles_permisos', 'rol_id', 'permiso_id');
    }

    /**
     * Relación con User
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'rol_id');
    }

    /**
     * Verificar si el rol tiene un permiso específico
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permisos()->where('nombre_permiso', $permission)->exists();
    }
}
