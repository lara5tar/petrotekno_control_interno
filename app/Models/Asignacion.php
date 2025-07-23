<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asignacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'asignaciones';

    protected $fillable = [
        'vehiculo_id',
        'obra_id',
        'personal_id',
        'creado_por_id',
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

    protected $dates = [
        'fecha_asignacion',
        'fecha_liberacion',
    ];

    protected $appends = [
        'esta_activa',
        'duracion_en_dias',
        'kilometraje_recorrido',
        'combustible_consumido',
        'eficiencia_combustible',
    ];

    protected $casts = [
        'fecha_asignacion' => 'datetime',
        'fecha_liberacion' => 'datetime',
        'kilometraje_inicial' => 'integer',
        'kilometraje_final' => 'integer',
        'combustible_inicial' => 'decimal:2',
        'combustible_final' => 'decimal:2',
        'combustible_suministrado' => 'decimal:2',
        'costo_combustible' => 'decimal:2',
        'historial_combustible' => 'array',
    ];

    /**
     * Relaciones
     */
    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function obra(): BelongsTo
    {
        return $this->belongsTo(Obra::class);
    }

    public function personal(): BelongsTo
    {
        return $this->belongsTo(Personal::class);
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por_id');
    }

    /**
     * Scopes
     */
    public function scopeActivas($query)
    {
        return $query->whereNull('fecha_liberacion');
    }

    public function scopeLiberadas($query)
    {
        return $query->whereNotNull('fecha_liberacion');
    }

    public function scopePorFecha($query, $fechaInicio, $fechaFin = null)
    {
        $query->where('fecha_asignacion', '>=', $fechaInicio);
        if ($fechaFin) {
            $query->where('fecha_asignacion', '<=', $fechaFin);
        }

        return $query;
    }

    public function scopePorVehiculo($query, $vehiculoId)
    {
        return $query->where('vehiculo_id', $vehiculoId);
    }

    public function scopePorObra($query, $obraId)
    {
        return $query->where('obra_id', $obraId);
    }

    public function scopePorOperador($query, $personalId)
    {
        return $query->where('personal_id', $personalId);
    }

    /**
     * Accessors
     */
    public function getEstaActivaAttribute(): bool
    {
        return is_null($this->fecha_liberacion);
    }

    public function getDuracionEnDiasAttribute(): ?int
    {
        if (! $this->fecha_liberacion) {
            return (int) Carbon::parse($this->fecha_asignacion)->diffInDays(Carbon::now());
        }

        return (int) Carbon::parse($this->fecha_asignacion)->diffInDays(Carbon::parse($this->fecha_liberacion));
    }

    public function getKilometrajeRecorridoAttribute(): ?int
    {
        if (! $this->kilometraje_final) {
            return null;
        }

        return $this->kilometraje_final - $this->kilometraje_inicial;
    }

    /**
     * Métodos de validación de negocio
     */
    public static function vehiculoTieneAsignacionActiva($vehiculoId): bool
    {
        return self::where('vehiculo_id', $vehiculoId)
            ->activas()
            ->exists();
    }

    public static function operadorTieneAsignacionActiva($personalId): bool
    {
        return self::where('personal_id', $personalId)
            ->activas()
            ->exists();
    }

    /**
     * Método para liberar asignación
     */
    public function liberar($kilometrajeFinal, $observaciones = null): bool
    {
        $this->fecha_liberacion = Carbon::now();
        $this->kilometraje_final = $kilometrajeFinal;

        if ($observaciones) {
            $this->observaciones = $this->observaciones
                ? $this->observaciones . "\n\nLiberación: " . $observaciones
                : 'Liberación: ' . $observaciones;
        }

        return $this->save();
    }

    /**
     * Calcular combustible consumido
     */
    public function getCombustibleConsumidoAttribute(): ?float
    {
        if ($this->combustible_inicial && $this->combustible_final) {
            // Combustible consumido = inicial - final + suministrado
            return (float) (($this->combustible_inicial - $this->combustible_final) + ($this->combustible_suministrado ?? 0));
        }

        return $this->combustible_suministrado ? (float) $this->combustible_suministrado : null;
    }

    /**
     * Calcular eficiencia de combustible (km por litro)
     */
    public function getEficienciaCombustibleAttribute(): ?float
    {
        $kilometraje = $this->kilometraje_recorrido;
        $combustible = $this->combustible_consumido;

        if ($kilometraje && $combustible && $combustible > 0) {
            return round($kilometraje / $combustible, 2);
        }

        return null;
    }

    /**
     * Agregar combustible al historial
     */
    public function agregarCombustible(float $litros, float $costo, ?string $estacion = null, array $metadata = []): void
    {
        $historial = $this->historial_combustible ?? [];

        $registro = [
            'fecha' => now()->toISOString(),
            'litros' => $litros,
            'costo' => $costo,
            'precio_por_litro' => $litros > 0 ? round($costo / $litros, 2) : 0,
            'estacion' => $estacion,
            'kilometraje_actual' => $this->vehiculo->kilometraje_actual ?? null,
            'metadata' => $metadata,
        ];

        $historial[] = $registro;

        $this->update([
            'historial_combustible' => $historial,
            'combustible_suministrado' => ($this->combustible_suministrado ?? 0) + $litros,
            'costo_combustible' => ($this->costo_combustible ?? 0) + $costo,
        ]);
    }

    /**
     * Obtener resumen de combustible
     */
    public function getResumenCombustible(): array
    {
        return [
            'inicial' => $this->combustible_inicial,
            'final' => $this->combustible_final,
            'suministrado' => $this->combustible_suministrado,
            'consumido' => $this->combustible_consumido,
            'costo_total' => $this->costo_combustible,
            'eficiencia' => $this->eficiencia_combustible,
            'total_recargas' => count($this->historial_combustible ?? []),
            'promedio_precio_litro' => $this->combustible_suministrado > 0
                ? round($this->costo_combustible / $this->combustible_suministrado, 2)
                : null,
        ];
    }

    /**
     * Boot method para eventos del modelo
     */
    protected static function boot()
    {
        parent::boot();

        // Validar que no exista asignación activa antes de crear SÓLO si la nueva asignación es activa
        static::creating(function ($asignacion) {
            // Solo validar si la asignación que se está creando es activa (sin fecha de liberación)
            if (is_null($asignacion->fecha_liberacion)) {
                if (self::vehiculoTieneAsignacionActiva($asignacion->vehiculo_id)) {
                    throw new \Exception('El vehículo ya tiene una asignación activa');
                }

                if (self::operadorTieneAsignacionActiva($asignacion->personal_id)) {
                    throw new \Exception('El operador ya tiene una asignación activa');
                }
            }
        });

        // Log de creación
        static::created(function ($asignacion) {
            LogAccion::create([
                'usuario_id' => $asignacion->creado_por_id,
                'accion' => 'crear_asignacion',
                'tabla_afectada' => 'asignaciones',
                'registro_id' => $asignacion->id,
                'detalles' => "Asignación creada: Vehículo {$asignacion->vehiculo_id} -> Obra {$asignacion->obra_id} -> Operador {$asignacion->personal_id}",
            ]);
        });
    }
}
