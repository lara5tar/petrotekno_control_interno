<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
     * Preparado para futuras relaciones con kilometrajes
     */
    // public function kilometrajes(): HasMany
    // {
    //     return $this->hasMany(Kilometraje::class);
    // }

    /**
     * Preparado para futuras relaciones con asignaciones
     */
    // public function asignaciones(): HasMany
    // {
    //     return $this->hasMany(Asignacion::class);
    // }

    /**
     * Preparado para futuras relaciones con mantenimientos
     */
    // public function mantenimientos(): HasMany
    // {
    //     return $this->hasMany(Mantenimiento::class);
    // }

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
