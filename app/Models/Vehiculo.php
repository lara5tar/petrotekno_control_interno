<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $marca
 * @property string $modelo
 * @property int $anio
 * @property string $n_serie
 * @property string $placas
 * @property int $estatus_id
 * @property int $kilometraje_actual
 * @property int|null $intervalo_km_motor Intervalo de cambio de aceite de motor
 * @property int|null $intervalo_km_transmision Intervalo de cambio de aceite de transmisión
 * @property int|null $intervalo_km_hidraulico Intervalo de cambio de aceite hidráulico
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property int|null $intervalo_km_hidraulico Intervalo de cambio de aceite hidráulico
 * @property string|null $observaciones
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $fecha_eliminacion
 * @property-read \App\Models\CatalogoEstatus $estatus
 * @property-read mixed $nombre_completo
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo buscar($termino)
 * @method static \Database\Factories\VehiculoFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo porAnio($anio_inicio, $anio_fin = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo porEstatus($estatus_id)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo porMarca($marca)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo porModelo($modelo)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo whereAnio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo whereEstatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo whereFechaEliminacion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo whereIntervaloKmHidraulico($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo whereIntervaloKmMotor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo whereIntervaloKmTransmision($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo whereKilometrajeActual($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo whereMarca($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo whereModelo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo whereNSerie($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo whereObservaciones($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo wherePlacas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Vehiculo extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nombre de la tabla
     */
    protected $table = 'vehiculos';

    /**
     * Campo de soft delete personalizado
     */
    const DELETED_AT = 'fecha_eliminacion';

    /**
     * Campos asignables masivamente
     */
    protected $fillable = [
        'marca',
        'modelo',
        'anio',
        'n_serie',
        'placas',
        'estatus_id',
        'kilometraje_actual',
        'intervalo_km_motor',
        'intervalo_km_transmision',
        'intervalo_km_hidraulico',
        'observaciones',
        'documentos_adicionales',
        'imagen',
    ];

    /**
     * Campos que deben ser convertidos a tipos nativos
     */
    protected $casts = [
        'anio' => 'integer',
        'kilometraje_actual' => 'integer',
        'intervalo_km_motor' => 'integer',
        'intervalo_km_transmision' => 'integer',
        'intervalo_km_hidraulico' => 'integer',
        'fecha_eliminacion' => 'datetime',
        'documentos_adicionales' => 'array',
    ];

    /**
     * Relación: Un vehículo pertenece a un estatus
     */
    public function estatus(): BelongsTo
    {
        return $this->belongsTo(CatalogoEstatus::class, 'estatus_id');
    }

    /**
     * Relación: Un vehículo tiene muchas obras (antes asignaciones)
     */
    public function obras(): HasMany
    {
        return $this->hasMany(Obra::class, 'vehiculo_id');
    }

    /**
     * Relación: Un vehículo puede tener asignaciones activas (obras no liberadas)
     */
    public function asignacionesActivas(): HasMany
    {
        return $this->obras()->whereNull('fecha_liberacion');
    }

    /**
     * Preparado para futuras relaciones con kilometrajes
     */
    /**
     * Relación con kilometrajes
     */
    public function kilometrajes(): HasMany
    {
        return $this->hasMany(Kilometraje::class);
    }

    /**
     * Relación con mantenimientos
     */
    public function mantenimientos(): HasMany
    {
        return $this->hasMany(Mantenimiento::class);
    }

    /**
     * Relación con documentos
     */
    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class);
    }

    /**
     * Relación: Un vehículo tiene muchas asignaciones de obra
     */
    public function asignacionesObra(): HasMany
    {
        return $this->hasMany(AsignacionObra::class);
    }

    /**
     * Relación: Asignaciones activas de obra
     */
    public function asignacionesObraActivas(): HasMany
    {
        return $this->asignacionesObra()->activas();
    }

    /**
     * Obtener la asignación de obra activa actual
     */
    public function asignacionObraActual()
    {
        return $this->asignacionesObraActivas()->latest('fecha_asignacion')->first();
    }

    /**
     * Verificar si tiene asignación de obra activa
     */
    public function tieneAsignacionObraActiva(): bool
    {
        return $this->asignacionesObraActivas()->exists();
    }

    /**
     * Scope para filtrar por marca
     */
    public function scopePorMarca($query, $marca)
    {
        return $query->where('marca', 'like', "%{$marca}%");
    }

    /**
     * Scope para filtrar por modelo
     */
    public function scopePorModelo($query, $modelo)
    {
        return $query->where('modelo', 'like', "%{$modelo}%");
    }

    /**
     * Scope para filtrar por estatus
     */
    public function scopePorEstatus($query, $estatus_id)
    {
        return $query->where('estatus_id', $estatus_id);
    }

    /**
     * Scope para filtrar por año
     */
    public function scopePorAnio($query, $anio_inicio, $anio_fin = null)
    {
        if ($anio_fin) {
            return $query->whereBetween('anio', [$anio_inicio, $anio_fin]);
        }

        return $query->where('anio', $anio_inicio);
    }

    /**
     * Scope para búsqueda general
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('marca', 'like', "%{$termino}%")
                ->orWhere('modelo', 'like', "%{$termino}%")
                ->orWhere('placas', 'like', "%{$termino}%")
                ->orWhere('n_serie', 'like', "%{$termino}%");
        });
    }

    /**
     * Scope para filtrar vehículos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estatus_id', 1); // Asumiendo que 1 es 'Activo'
    }

    /**
     * Scope para filtrar vehículos disponibles (sin asignaciones activas en el nuevo sistema)
     */
    public function scopeDisponibles($query)
    {
        return $query->whereHas('estatus', function ($q) {
            $q->where('nombre_estatus', 'Disponible')
                ->orWhere('nombre_estatus', 'Activo');
        })
            ->whereDoesntHave('asignacionesObraActivas');
    }

    /**
     * Obtener la obra actual del vehículo (obra activa sin liberar)
     */
    public function obraActual()
    {
        return $this->hasOne(Obra::class, 'vehiculo_id')
            ->whereNull('fecha_liberacion')
            ->whereIn('estatus', ['en_progreso', 'planificada'])
            ->latest('fecha_asignacion');
    }

    /**
     * Verificar si el vehículo tiene una obra actual activa
     */
    public function tieneObraActual(): bool
    {
        return $this->obraActual()->exists();
    }

    /**
     * Verificar si el vehículo está disponible para asignación
     */
    public function estaDisponible(): bool
    {
        // Verificar que el estatus sea disponible/activo y no tenga obra actual
        $estatusDisponible = $this->estatus && in_array(
            strtolower($this->estatus->nombre_estatus), 
            ['disponible', 'activo']
        );
        
        return $estatusDisponible && !$this->tieneObraActual();
    }

    /**
     * Obtener el operador actual del vehículo (si tiene obra activa)
     */
    public function operadorActual()
    {
        return $this->hasOneThrough(
            Personal::class,
            Obra::class,
            'vehiculo_id',    // Clave foránea en la tabla obras
            'id',             // Clave foránea en la tabla personal
            'id',             // Clave local en vehiculos
            'operador_id'     // Clave local en obras
        )->whereNull('obras.fecha_liberacion')
         ->whereIn('obras.estatus', ['en_progreso', 'planificada'])
         ->latest('obras.fecha_asignacion');
    }

    /**
     * Obtener información resumida del estado actual del vehículo
     */
    public function getEstadoActualAttribute(): array
    {
        $obraActual = $this->obraActual()->first();
        
        return [
            'tiene_obra_activa' => $this->tieneObraActual(),
            'obra_actual' => $obraActual ? [
                'id' => $obraActual->id,
                'nombre' => $obraActual->nombre_obra,
                'estatus' => $obraActual->estatus,
                'fecha_asignacion' => $obraActual->fecha_asignacion,
                'operador' => $obraActual->operador ? $obraActual->operador->nombre_completo : null,
            ] : null,
            'esta_disponible' => $this->estaDisponible(),
            'estatus_vehiculo' => $this->estatus ? $this->estatus->nombre_estatus : 'Sin estatus',
        ];
    }

    /**
     * Scope para filtrar vehículos con obra actual
     */
    public function scopeConObraActual($query)
    {
        return $query->whereHas('obras', function ($q) {
            $q->whereNull('fecha_liberacion')
              ->whereIn('estatus', ['en_progreso', 'planificada']);
        });
    }

    /**
     * Scope para filtrar vehículos sin obra actual
     */
    public function scopeSinObraActual($query)
    {
        return $query->whereDoesntHave('obras', function ($q) {
            $q->whereNull('fecha_liberacion')
              ->whereIn('estatus', ['en_progreso', 'planificada']);
        });
    }

    /**
     * Accessor para nombre completo del vehículo
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->marca} {$this->modelo} ({$this->anio})";
    }

    /**
     * Accessor para placas en mayúsculas
     */
    public function getPlacasAttribute($value)
    {
        return strtoupper($value);
    }

    /**
     * Mutator para placas en mayúsculas
     */
    public function setPlacasAttribute($value)
    {
        $this->attributes['placas'] = strtoupper($value);
    }

    /**
     * Mutator para marca en formato título
     */
    public function setMarcaAttribute($value)
    {
        $this->attributes['marca'] = ucwords(strtolower($value));
    }

    /**
     * Mutator para modelo en formato título
     */
    public function setModeloAttribute($value)
    {
        $this->attributes['modelo'] = ucwords(strtolower($value));
    }
}
