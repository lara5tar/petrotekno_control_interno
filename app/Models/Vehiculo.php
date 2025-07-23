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
    ];

    /**
     * Relación: Un vehículo pertenece a un estatus
     */
    public function estatus(): BelongsTo
    {
        return $this->belongsTo(CatalogoEstatus::class, 'estatus_id');
    }

    /**
     * Relación: Un vehículo tiene muchas asignaciones
     */
    public function asignaciones(): HasMany
    {
        return $this->hasMany(Asignacion::class, 'vehiculo_id');
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
     * Preparado para futuras relaciones con asignaciones
     */
    // public function asignaciones(): HasMany
    // {
    //     return $this->hasMany(Asignacion::class);
    // }

    /**
     * Relación con mantenimientos
     */
    public function mantenimientos(): HasMany
    {
        return $this->hasMany(Mantenimiento::class);
    }

    /**
     * Preparado para futuras relaciones con documentos
     */
    // public function documentos(): HasMany
    // {
    //     return $this->hasMany(Documento::class);
    // }

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
