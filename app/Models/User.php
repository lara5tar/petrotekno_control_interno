<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @method bool hasPermission(string $permission)
 * @method bool hasRole(string $role)
 * @method \Illuminate\Support\Collection getPermissions()
 *
 * @property int $id
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property int|null $rol_id
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $personal_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LogAccion> $logAcciones
 * @property-read int|null $log_acciones_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Personal|null $personal
 * @property-read \App\Models\Role|null $rol
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 *
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNombreUsuario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePersonalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 *
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'personal_id',
        'email',
        'password',
        'rol_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relación con Personal
     */
    public function personal(): BelongsTo
    {
        return $this->belongsTo(Personal::class, 'personal_id');
    }

    /**
     * Relación con Role
     */
    public function rol(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }

    /**
     * Relación con LogAccion
     */
    public function logAcciones(): HasMany
    {
        return $this->hasMany(\App\Models\LogAccion::class, 'usuario_id');
    }

    /**
     * Relación con Obras (como encargado)
     */
    public function obrasComoEncargado(): HasMany
    {
        return $this->hasMany(Obra::class, 'encargado_id');
    }

    /**
     * Verificar si el usuario tiene un permiso específico
     */
    public function hasPermission(string $permission): bool
    {
        return $this->rol && $this->rol->hasPermission($permission);
    }

    /**
     * Verificar si el usuario tiene un rol específico
     */
    public function hasRole(string $role): bool
    {
        return $this->rol && $this->rol->nombre_rol === $role;
    }

    /**
     * Obtener todos los permisos del usuario
     */
    public function getPermissions(): \Illuminate\Support\Collection
    {
        return $this->rol ? $this->rol->permisos : collect();
    }

    /**
     * Accessor para obtener el nombre completo del usuario
     * Retorna el nombre del personal asociado o el email si no tiene personal
     */
    public function getNombreCompletoAttribute(): string
    {
        // Verificar que personal existe Y que nombre_completo no sea null/vacío
        if ($this->personal && !empty($this->personal->nombre_completo)) {
            return $this->personal->nombre_completo;
        }
        
        // Si no tiene personal o nombre_completo está vacío, usar el email
        return $this->email;
    }
}
