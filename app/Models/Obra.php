<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo para la gestión de obras del sistema
 *
 * @property int $id
 * @property string $nombre_obra
 * @property string $estatus
 * @property int|null $avance
 * @property \Carbon\Carbon $fecha_inicio
 * @property \Carbon\Carbon|null $fecha_fin
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $fecha_eliminacion
 */
class Obra extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nombre de la tabla en la base de datos
     */
    protected $table = 'obras';

    /**
     * Nombre de la columna para soft deletes
     */
    const DELETED_AT = 'fecha_eliminacion';

    /**
     * Estados válidos para las obras
     */
    const ESTATUS_PLANIFICADA = 'planificada';

    const ESTATUS_EN_PROGRESO = 'en_progreso';

    const ESTATUS_SUSPENDIDA = 'suspendida';

    const ESTATUS_COMPLETADA = 'completada';

    const ESTATUS_CANCELADA = 'cancelada';

    /**
     * Array de estados válidos
     */
    const ESTADOS_VALIDOS = [
        self::ESTATUS_PLANIFICADA,
        self::ESTATUS_EN_PROGRESO,
        self::ESTATUS_SUSPENDIDA,
        self::ESTATUS_COMPLETADA,
        self::ESTATUS_CANCELADA,
    ];

    /**
     * Campos que se pueden asignar masivamente
     */
    protected $fillable = [
        'nombre_obra',
        'estatus',
        'avance',
        'fecha_inicio',
        'fecha_fin',
        'vehiculo_id',
        'operador_id',
        'encargado_id',
        'fecha_asignacion',
        'fecha_liberacion',
        'kilometraje_inicial',
        'kilometraje_final',
        'combustible_inicial',
        'combustible_final',
        'combustible_suministrado',
        'costo_combustible',
        'historial_combustible',
        'observaciones',
    ];

    /**
     * Campos que deben ser convertidos a tipos nativos
     */
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'fecha_asignacion' => 'datetime',
        'fecha_liberacion' => 'datetime',
        'avance' => 'integer',
        'kilometraje_inicial' => 'integer',
        'kilometraje_final' => 'integer',
        'combustible_inicial' => 'decimal:2',
        'combustible_final' => 'decimal:2',
        'combustible_suministrado' => 'decimal:2',
        'costo_combustible' => 'decimal:2',
        'historial_combustible' => 'json',
        'fecha_eliminacion' => 'datetime',
    ];

    /**
     * Atributos adicionales que deben agregarse al array/JSON
     */
    protected $appends = [
        'estatus_descripcion',
        'dias_transcurridos',
        'dias_restantes',
        'duracion_total',
        'esta_atrasada',
        'porcentaje_tiempo_transcurrido',
    ];

    /**
     * Relación con asignaciones
     */
    public function asignaciones(): HasMany
    {
        return $this->hasMany(Asignacion::class);
    }

    /**
     * Relación con documentos asociados a la obra
     */
    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class);
    }

    /**
     * Relación con el vehículo asignado
     */
    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    /**
     * Relación con el operador asignado
     */
    public function operador(): BelongsTo
    {
        return $this->belongsTo(Personal::class, 'operador_id');
    }

    /**
     * Relación con el personal (alias para operador)
     */
    public function personal(): BelongsTo
    {
        return $this->belongsTo(Personal::class, 'operador_id');
    }

    /**
     * Relación con el usuario encargado
     */
    public function encargado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'encargado_id');
    }

    /**
     * Relación con el usuario que creó la asignación (alias para encargado)
     */
    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'encargado_id');
    }

    /**
     * Scope para filtrar por estatus
     */
    public function scopePorEstatus($query, $estatus)
    {
        return $query->where('estatus', $estatus);
    }

    /**
     * Scope para filtrar obras activas (no canceladas)
     */
    public function scopeActivas($query)
    {
        return $query->whereNotIn('estatus', [self::ESTATUS_CANCELADA]);
    }

    /**
     * Scope para filtrar obras en progreso
     */
    public function scopeEnProgreso($query)
    {
        return $query->where('estatus', self::ESTATUS_EN_PROGRESO);
    }

    /**
     * Scope para filtrar obras completadas
     */
    public function scopeCompletadas($query)
    {
        return $query->where('estatus', self::ESTATUS_COMPLETADA);
    }

    /**
     * Scope para búsqueda por nombre
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where('nombre_obra', 'like', "%{$termino}%");
    }

    /**
     * Scope para filtrar por rango de fechas
     */
    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin]);
    }

    /**
     * Mutator para el nombre de obra (formato título y sanitización XSS)
     */
    public function setNombreObraAttribute($value)
    {
        // Sanitizar contenido peligroso (XSS)
        $value = strip_tags($value); // Remover todas las etiquetas HTML
        $value = str_replace(['javascript:', 'vbscript:', 'onload=', 'onerror='], '', $value);

        $this->attributes['nombre_obra'] = trim(ucwords(strtolower($value)));
    }

    /**
     * Mutator para el estatus (validación)
     */
    public function setEstatusAttribute($value)
    {
        if (! in_array($value, self::ESTADOS_VALIDOS)) {
            throw new \InvalidArgumentException("Estatus '{$value}' no es válido.");
        }
        $this->attributes['estatus'] = $value;
    }

    /**
     * Mutator para el avance (validación 0-100)
     */
    public function setAvanceAttribute($value)
    {
        if ($value !== null) {
            $value = max(0, min(100, (int) $value));
        }
        $this->attributes['avance'] = $value;
    }

    /**
     * Accessor para obtener días transcurridos desde el inicio
     */
    public function getDiasTranscurridosAttribute()
    {
        $fechaInicio = Carbon::parse($this->fecha_inicio)->startOfDay();
        $fechaActual = Carbon::now()->startOfDay();

        return (int) $fechaInicio->diffInDays($fechaActual);
    }

    /**
     * Accessor para obtener días restantes (si hay fecha fin)
     */
    public function getDiasRestantesAttribute()
    {
        if (! $this->fecha_fin) {
            return null;
        }

        $fechaFin = Carbon::parse($this->fecha_fin)->endOfDay();
        $fechaActual = Carbon::now()->startOfDay();

        if ($fechaFin->isPast()) {
            return 0;
        }

        return (int) $fechaActual->diffInDays($fechaFin);
    }

    /**
     * Accessor para obtener duración total en días
     */
    public function getDuracionTotalAttribute()
    {
        if (! $this->fecha_fin) {
            return null;
        }

        $fechaInicio = Carbon::parse($this->fecha_inicio)->startOfDay();
        $fechaFin = Carbon::parse($this->fecha_fin)->endOfDay();

        return (int) ($fechaInicio->diffInDays($fechaFin) + 1);
    }

    /**
     * Accessor para determinar si la obra está atrasada
     */
    public function getEstaAtrasadaAttribute()
    {
        if (! $this->fecha_fin || $this->estatus === self::ESTATUS_COMPLETADA) {
            return false;
        }

        return Carbon::parse($this->fecha_fin)->isPast() &&
            in_array($this->estatus, [self::ESTATUS_PLANIFICADA, self::ESTATUS_EN_PROGRESO]);
    }

    /**
     * Accessor para obtener el porcentaje de tiempo transcurrido
     */
    public function getPorcentajeTiempoTranscurridoAttribute()
    {
        if (! $this->duracion_total || $this->duracion_total <= 0) {
            return 0;
        }

        return min(100, round(($this->dias_transcurridos / $this->duracion_total) * 100, 1));
    }

    /**
     * Accessor para obtener descripción amigable del estatus
     */
    public function getEstatusDescripcionAttribute()
    {
        $descripciones = [
            self::ESTATUS_PLANIFICADA => 'Obra programada pero no iniciada',
            self::ESTATUS_EN_PROGRESO => 'Obra activa en desarrollo',
            self::ESTATUS_SUSPENDIDA => 'Obra temporalmente detenida',
            self::ESTATUS_COMPLETADA => 'Obra finalizada exitosamente',
            self::ESTATUS_CANCELADA => 'Obra cancelada antes de completarse',
        ];

        return $descripciones[$this->estatus] ?? 'Estado desconocido';
    }

    /**
     * Método para cambiar el estatus con validaciones
     */
    public function cambiarEstatus($nuevoEstatus, $motivo = null)
    {
        $estatusAnterior = $this->estatus;

        // Validar transiciones permitidas
        $transicionesPermitidas = [
            self::ESTATUS_PLANIFICADA => [self::ESTATUS_EN_PROGRESO, self::ESTATUS_CANCELADA],
            self::ESTATUS_EN_PROGRESO => [self::ESTATUS_SUSPENDIDA, self::ESTATUS_COMPLETADA, self::ESTATUS_CANCELADA],
            self::ESTATUS_SUSPENDIDA => [self::ESTATUS_EN_PROGRESO, self::ESTATUS_CANCELADA],
            self::ESTATUS_COMPLETADA => [], // No se puede cambiar desde completada
            self::ESTATUS_CANCELADA => [], // No se puede cambiar desde cancelada
        ];

        if (! in_array($nuevoEstatus, $transicionesPermitidas[$estatusAnterior] ?? [])) {
            throw new \InvalidArgumentException(
                "No se puede cambiar de '{$estatusAnterior}' a '{$nuevoEstatus}'"
            );
        }

        $this->estatus = $nuevoEstatus;

        // Si se completa, establecer avance al 100%
        if ($nuevoEstatus === self::ESTATUS_COMPLETADA) {
            $this->avance = 100;
        }

        return $this->save();
    }
}
