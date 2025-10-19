<?php

namespace App\Models;

use App\Enums\EstadoVehiculo;
use App\Traits\UppercaseAttributes;
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
 * @property string $estado Estado del vehículo usando enum
 * @property int $kilometraje_actual
 * @property int|null $intervalo_km_motor Intervalo de cambio de aceite de motor
 * @property int|null $intervalo_km_transmision Intervalo de cambio de aceite de transmisión
 * @property int|null $intervalo_km_hidraulico Intervalo de cambio de aceite hidráulico
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property string|null $observaciones
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $fecha_eliminacion
 * @property-read mixed $nombre_completo
 * @property-read EstadoVehiculo $estado_enum
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo buscar($termino)
 * @method static \Database\Factories\VehiculoFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo porAnio($anio_inicio, $anio_fin = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo porMarca($marca)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo porModelo($modelo)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo porEstado($estado)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo whereAnio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehiculo whereEstado($value)
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
    use HasFactory, SoftDeletes, UppercaseAttributes;

    /**
     * Campos que se convertirán automáticamente a MAYÚSCULAS
     */
    protected $uppercaseFields = [
        'marca',
        'modelo',
        'n_serie',
        'placas',
        'observaciones',
        'estado',      // Estado de la República (ej: NUEVO LEÓN, JALISCO)
        'municipio',   // Municipio/Ciudad (ej: MONTERREY, GUADALAJARA)
        'numero_poliza',
    ];

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
        'tipo_activo_id',
        'marca',
        'modelo',
        'anio',
        'n_serie',
        'placas',
        'estatus',
        'kilometraje_actual',
        'intervalo_km_motor',
        'intervalo_km_transmision',
        'intervalo_km_hidraulico',
        'observaciones',
        'operador_id',
        'estado',
        'municipio',
        'poliza_vencimiento',
        'derecho_vencimiento',
        'numero_poliza',
        'poliza_url',
        'derecho_url',
        'factura_url',
        'url_imagen',
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
        'estatus' => EstadoVehiculo::class, // Cambio temporal: usar 'estatus'
        // Nuevas fechas de vencimiento
        'poliza_vencimiento' => 'date',
        'derecho_vencimiento' => 'date',
    ];

    /**
     * Relación: Un vehículo tiene muchas obras a través de asignaciones
     */
    public function obras()
    {
        return $this->belongsToMany(Obra::class, 'asignaciones_obra', 'vehiculo_id', 'obra_id')
                   ->whereNull('asignaciones_obra.deleted_at');
    }

    /**
     * Relación: Un vehículo puede tener asignaciones activas (obras no liberadas)
     */
    public function asignacionesActivas()
    {
        return $this->obras()->whereNull('fecha_liberacion');
    }

    /**
     * Relación con kilometrajes
     */
    public function kilometrajes(): HasMany
    {
        return $this->hasMany(Kilometraje::class);
    }

    /**
     * Relación con mantenimientos
    /**
     * Relación con mantenimientos
     */
    public function mantenimientos(): HasMany
    {
        return $this->hasMany(Mantenimiento::class)->orderBy('id', 'desc');
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
     * Relación: Un vehículo pertenece a un operador (personal)
     */
    public function operador(): BelongsTo
    {
        return $this->belongsTo(Personal::class, 'operador_id');
    }

    /**
     * Relación: Un vehículo pertenece a un tipo de activo
     */
    public function tipoActivo(): BelongsTo
    {
        return $this->belongsTo(TipoActivo::class, 'tipo_activo_id');
    }

    /**
     * Relación: Un vehículo tiene muchos registros en el historial de operadores
     */
    public function historialOperadores(): HasMany
    {
        return $this->hasMany(HistorialOperadorVehiculo::class);
    }

    /**
     * Obtener el historial de operadores ordenado por fecha más reciente
     */
    public function getHistorialOperadoresOrdenadoAttribute()
    {
        return $this->historialOperadores()
            ->with(['operadorAnterior', 'operadorNuevo', 'usuarioAsigno'])
            ->recientes()
            ->get();
    }

    /**
     * Obtener el último cambio de operador
     */
    public function getUltimoCambioOperadorAttribute()
    {
        return $this->historialOperadores()
            ->with(['operadorAnterior', 'operadorNuevo', 'usuarioAsigno'])
            ->recientes()
            ->first();
    }

    /**
     * Verificar si el vehículo ha tenido operadores asignados anteriormente
     */
    public function haTenidoOperadoresAttribute(): bool
    {
        return $this->historialOperadores()->exists();
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
                ->orWhere('n_serie', 'like', "%{$termino}%")
                ->orWhere('anio', 'like', "%{$termino}%");
        });
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopePorEstado($query, $estado)
    {
        if ($estado instanceof EstadoVehiculo) {
            return $query->where('estatus', $estado->value);
        }
        
        return $query->where('estatus', $estado);
    }

    /**
     * Scope para filtrar vehículos disponibles
     */
    public function scopeDisponibles($query)
    {
        return $query->where('estatus', EstadoVehiculo::DISPONIBLE->value);
    }

    /**
     * Scope para filtrar vehículos activos (disponibles o asignados)
     */
    public function scopeActivos($query)
    {
        return $query->whereIn('estatus', [
            EstadoVehiculo::DISPONIBLE->value,
            EstadoVehiculo::ASIGNADO->value
        ]);
    }

    /**
     * Scope para filtrar vehículos en mantenimiento
     */
    public function scopeEnMantenimiento($query)
    {
        return $query->where('estatus', EstadoVehiculo::EN_MANTENIMIENTO->value);
    }

    /**
     * Verificar si el vehículo está disponible para asignación
     */
    public function estaDisponible(): bool
    {
        return $this->estatus === EstadoVehiculo::DISPONIBLE || 
               ($this->estatus instanceof EstadoVehiculo && $this->estatus === EstadoVehiculo::DISPONIBLE);
    }

    /**
     * Verificar si el vehículo está asignado
     */
    public function estaAsignado(): bool
    {
        return $this->estatus === EstadoVehiculo::ASIGNADO ||
               ($this->estatus instanceof EstadoVehiculo && $this->estatus === EstadoVehiculo::ASIGNADO);
    }

    /**
     * Verificar si el vehículo está en mantenimiento
     */
    public function estaEnMantenimiento(): bool
    {
        return $this->estatus === EstadoVehiculo::EN_MANTENIMIENTO ||
               ($this->estatus instanceof EstadoVehiculo && $this->estatus === EstadoVehiculo::EN_MANTENIMIENTO);
    }

    /**
     * Cambiar el estado del vehículo
     */
    public function cambiarEstado(EstadoVehiculo $nuevoEstado): void
    {
        $this->update(['estatus' => $nuevoEstado->value]);
    }

    /**
     * Verificar si tiene obra actual
     */
    public function tieneObraActual(): bool
    {
        return $this->obras()->whereNull('fecha_liberacion')
            ->whereIn('estatus', ['en_progreso', 'planificada'])
            ->exists();
    }

    /**
     * Obtener obra actual
     */
    public function obraActual()
    {
        return $this->obras()->whereNull('fecha_liberacion')
            ->whereIn('estatus', ['en_progreso', 'planificada'])
            ->latest('fecha_asignacion');
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
                'operador' => $obraActual->operador?->nombre_completo ?? null,
            ] : null,
            'esta_disponible' => $this->estaDisponible(),
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
     * Accessor para obtener el enum del estado
     */
    public function getEstadoEnumAttribute(): EstadoVehiculo
    {
        // Si ya es un enum (due to cast), devolverlo directamente
        if ($this->estatus instanceof EstadoVehiculo) {
            return $this->estatus;
        }
        
        // Si es un string, convertirlo a enum
        return EstadoVehiculo::fromValue($this->estatus);
    }

    /**
     * Accessor para nombre completo del vehículo
     */
    public function getNombreCompletoAttribute()
    {
        $marca = $this->marca ?? 'Sin marca';
        $modelo = $this->modelo ?? 'Sin modelo';
        $anio = $this->anio ?? 'Sin año';
        
        return "{$marca} {$modelo} ({$anio})";
    }

    /**
     * Accessor para obtener la ubicación completa (estado y municipio)
     */
    public function getUbicacionAttribute()
    {
        $estado = $this->estado ?? 'Sin estado';
        $municipio = $this->municipio ?? 'Sin municipio';
        
        return "{$estado}, {$municipio}";
    }
}
