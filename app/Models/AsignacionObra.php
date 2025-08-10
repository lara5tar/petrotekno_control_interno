<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class AsignacionObra extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'asignaciones_obra';

    protected $fillable = [
        'obra_id',
        'vehiculo_id',
        'operador_id',
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
        'estado',
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

    protected $appends = [
        'esta_activa',
        'duracion_en_dias',
        'kilometraje_recorrido',
        'combustible_consumido',
        'eficiencia_combustible',
    ];

    /**
     * Estados disponibles para las asignaciones
     */
    const ESTADO_ACTIVA = 'activa';
    const ESTADO_LIBERADA = 'liberada';
    const ESTADO_TRANSFERIDA = 'transferida';

    const ESTADOS_VALIDOS = [
        self::ESTADO_ACTIVA,
        self::ESTADO_LIBERADA,
        self::ESTADO_TRANSFERIDA,
    ];

    /**
     * Relaciones
     */
    public function obra(): BelongsTo
    {
        return $this->belongsTo(Obra::class);
    }

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function operador(): BelongsTo
    {
        return $this->belongsTo(Personal::class, 'operador_id');
    }

    public function personal(): BelongsTo
    {
        return $this->belongsTo(Personal::class, 'operador_id');
    }

    /**
     * Obtener el encargado de la asignación a través de la obra
     */
    public function getEncargadoAttribute()
    {
        return $this->obra?->encargado;
    }

    /**
     * Scopes
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', self::ESTADO_ACTIVA);
    }

    public function scopeLiberadas($query)
    {
        return $query->where('estado', self::ESTADO_LIBERADA);
    }

    public function scopePorObra($query, $obraId)
    {
        return $query->where('obra_id', $obraId);
    }

    public function scopePorVehiculo($query, $vehiculoId)
    {
        return $query->where('vehiculo_id', $vehiculoId);
    }

    public function scopePorOperador($query, $operadorId)
    {
        return $query->where('operador_id', $operadorId);
    }

    public function scopePorFecha($query, $fechaInicio, $fechaFin = null)
    {
        $query->where('fecha_asignacion', '>=', $fechaInicio);
        if ($fechaFin) {
            $query->where('fecha_asignacion', '<=', $fechaFin);
        }
        return $query;
    }

    /**
     * Accessors
     */
    public function getEstaActivaAttribute(): bool
    {
        return $this->estado === self::ESTADO_ACTIVA;
    }

    public function getDuracionEnDiasAttribute(): ?int
    {
        if (!$this->fecha_liberacion) {
            return $this->fecha_asignacion ? 
                Carbon::parse($this->fecha_asignacion)->diffInDays(Carbon::now()) : null;
        }

        return $this->fecha_asignacion ? 
            Carbon::parse($this->fecha_asignacion)->diffInDays(Carbon::parse($this->fecha_liberacion)) : null;
    }

    public function getKilometrajeRecorridoAttribute(): ?int
    {
        if (!$this->kilometraje_final || !$this->kilometraje_inicial) {
            return null;
        }
        return $this->kilometraje_final - $this->kilometraje_inicial;
    }

    public function getCombustibleConsumidoAttribute(): ?float
    {
        if (!$this->combustible_final || !$this->combustible_inicial) {
            return null;
        }
        return $this->combustible_inicial - $this->combustible_final + ($this->combustible_suministrado ?? 0);
    }

    public function getEficienciaCombustibleAttribute(): ?float
    {
        $combustibleConsumido = $this->combustible_consumido;
        $kilometrajeRecorrido = $this->kilometraje_recorrido;

        if (!$combustibleConsumido || !$kilometrajeRecorrido || $combustibleConsumido <= 0) {
            return null;
        }

        return round($kilometrajeRecorrido / $combustibleConsumido, 2);
    }

    /**
     * Métodos de validación de negocio
     */
    public static function vehiculoTieneAsignacionActiva($vehiculoId, $exceptoId = null): bool
    {
        $query = self::where('vehiculo_id', $vehiculoId)->activas();
        
        if ($exceptoId) {
            $query->where('id', '!=', $exceptoId);
        }
        
        return $query->exists();
    }

    public static function operadorTieneAsignacionActiva($operadorId, $exceptoId = null): bool
    {
        // IMPORTANTE: Los operadores SÍ pueden tener múltiples asignaciones activas
        // ya que un operador puede manejar varios vehículos en la misma obra o diferentes obras
        // Solo verificamos disponibilidad si se requiere explícitamente
        return false; // Permitir múltiples asignaciones por operador por defecto
    }

    public static function validarAsignacionUnicaVehiculo($vehiculoId, $exceptoId = null): void
    {
        if (self::vehiculoTieneAsignacionActiva($vehiculoId, $exceptoId)) {
            throw new \Exception('El vehículo ya tiene una asignación activa. Un vehículo solo puede estar asignado a una obra a la vez.');
        }
    }

    public static function obraTieneCapacidadParaNuevaAsignacion($obraId): bool
    {
        $obra = Obra::find($obraId);
        if (!$obra) {
            return false;
        }

        // Si no permite múltiples asignaciones y ya tiene alguna activa
        if (!$obra->permite_multiples_asignaciones && $obra->total_asignaciones_activas > 0) {
            return false;
        }

        $asignacionesActivas = self::where('obra_id', $obraId)->activas()->count();

        // Verificar límite de vehículos
        if ($obra->max_vehiculos && $asignacionesActivas >= $obra->max_vehiculos) {
            return false;
        }

        return true;
    }

    /**
     * Método para liberar asignación
     */
    public function liberar($kilometrajeFinal, $combustibleFinal = null, $observaciones = null): bool
    {
        $this->update([
            'fecha_liberacion' => Carbon::now(),
            'kilometraje_final' => $kilometrajeFinal,
            'combustible_final' => $combustibleFinal,
            'estado' => self::ESTADO_LIBERADA,
            'observaciones' => $observaciones ? 
                ($this->observaciones ? $this->observaciones . "\n\n" . $observaciones : $observaciones) :
                $this->observaciones,
        ]);

        // Actualizar kilometraje del vehículo
        if ($this->vehiculo && $kilometrajeFinal > $this->vehiculo->kilometraje_actual) {
            $this->vehiculo->update(['kilometraje_actual' => $kilometrajeFinal]);
        }

        return true;
    }

    /**
     * Método para transferir asignación a otro operador
     */
    public function transferir($nuevoOperadorId, $kilometrajeTransferencia, $observaciones = null): bool
    {
        $operadorAnterior = $this->operador;
        
        $this->update([
            'operador_id' => $nuevoOperadorId,
            'estado' => self::ESTADO_TRANSFERIDA,
            'observaciones' => $this->observaciones . "\n\n[TRANSFERENCIA " . now()->format('d/m/Y H:i') . '] ' .
                "De: {$operadorAnterior->nombre_completo} | " .
                "Nuevo operador: " . Personal::find($nuevoOperadorId)->nombre_completo . " | " .
                "Km: {$kilometrajeTransferencia}" . 
                ($observaciones ? " | Observaciones: {$observaciones}" : ""),
        ]);

        // Actualizar kilometraje del vehículo
        if ($this->vehiculo && $kilometrajeTransferencia > $this->vehiculo->kilometraje_actual) {
            $this->vehiculo->update(['kilometraje_actual' => $kilometrajeTransferencia]);
        }

        return true;
    }

    /**
     * Registrar suministro de combustible
     */
    public function registrarCombustible($litros, $costo, $estacion = null, $metadata = []): void
    {
        $historial = $this->historial_combustible ?? [];
        
        $registro = [
            'fecha' => now()->toDateTimeString(),
            'litros' => $litros,
            'precio_por_litro' => $litros > 0 ? round($costo / $litros, 2) : 0,
            'costo_total' => $costo,
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
            'promedio_precio_litro' => $this->combustible_suministrado > 0 ? 
                round($this->costo_combustible / $this->combustible_suministrado, 2) : null,
        ];
    }

    /**
     * Boot method para eventos del modelo
     */
    protected static function boot()
    {
        parent::boot();

        // Validar que no exista asignación activa antes de crear
        static::creating(function ($asignacion) {
            if ($asignacion->estado === self::ESTADO_ACTIVA) {
                if (self::vehiculoTieneAsignacionActiva($asignacion->vehiculo_id)) {
                    throw new \Exception('El vehículo ya tiene una asignación activa');
                }

                if (self::operadorTieneAsignacionActiva($asignacion->operador_id)) {
                    throw new \Exception('El operador ya tiene una asignación activa');
                }

                if (!self::obraTieneCapacidadParaNuevaAsignacion($asignacion->obra_id)) {
                    throw new \Exception('La obra no tiene capacidad para nuevas asignaciones');
                }
            }
        });

        // Log de creación
        static::created(function ($asignacion) {
            \App\Models\LogAccion::create([
                'usuario_id' => $asignacion->obra?->encargado_id,
                'accion' => 'crear_asignacion_obra',
                'tabla_afectada' => 'asignaciones_obra',
                'registro_id' => $asignacion->id,
                'detalles' => "Asignación creada: Vehículo {$asignacion->vehiculo->nombre_completo} -> Obra {$asignacion->obra->nombre_obra} -> Operador {$asignacion->operador->nombre_completo}",
            ]);
        });
    }
}
