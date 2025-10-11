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
        'operador_id',  // Añadido para poder asignar operadores
        'fecha_asignacion',
        'fecha_liberacion',
        'kilometraje_inicial',
        'kilometraje_final',
        'observaciones',
        'estado',
    ];

    protected $casts = [
        'fecha_asignacion' => 'datetime',
        'fecha_liberacion' => 'datetime',
        'kilometraje_inicial' => 'integer',
        'kilometraje_final' => 'integer',
    ];

    protected $appends = [
        'esta_activa',
        'duracion_en_dias',
        'kilometraje_recorrido',
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
     * Relaciones - SOLO obra y vehiculo
     */
    public function obra(): BelongsTo
    {
        return $this->belongsTo(Obra::class);
    }

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }
    
    /**
     * Relación con el operador (Personal)
     */
    public function operador(): BelongsTo
    {
        return $this->belongsTo(Personal::class, 'operador_id');
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

    public function scopePorFecha($query, $fechaInicio, $fechaFin = null)
    {
        $query->where('fecha_asignacion', '>=', $fechaInicio);
        if ($fechaFin) {
            $query->where('fecha_asignacion', '<=', $fechaFin);
        }
        return $query;
    }

    public function scopeHistorialPorVehiculoObra($query, $vehiculoId, $obraId)
    {
        return $query->where('vehiculo_id', $vehiculoId)
                     ->where('obra_id', $obraId)
                     ->orderBy('fecha_asignacion', 'desc');
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
                (int) floor(Carbon::parse($this->fecha_asignacion)->diffInDays(Carbon::now())) : null;
        }

        return $this->fecha_asignacion ? 
            (int) floor(Carbon::parse($this->fecha_asignacion)->diffInDays(Carbon::parse($this->fecha_liberacion))) : null;
    }

    public function getKilometrajeRecorridoAttribute(): ?int
    {
        if (!$this->kilometraje_final || !$this->kilometraje_inicial) {
            return null;
        }
        return $this->kilometraje_final - $this->kilometraje_inicial;
    }

    /**
     * Métodos de validación de negocio - SIMPLIFICADOS para solo vehículos
     */
    public static function vehiculoTieneAsignacionActiva($vehiculoId, $exceptoId = null): bool
    {
        $query = self::where('vehiculo_id', $vehiculoId)->activas();
        
        if ($exceptoId) {
            $query->where('id', '!=', $exceptoId);
        }
        
        return $query->exists();
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

        // Verificar que la obra esté en un estado que permita asignaciones
        return in_array($obra->estatus, [
            \App\Models\Obra::ESTATUS_PLANIFICADA,
            \App\Models\Obra::ESTATUS_EN_PROGRESO,
            \App\Models\Obra::ESTATUS_SUSPENDIDA
        ]);
    }

    /**
     * Método para liberar asignación - SIMPLIFICADO sin combustible
     */
    public function liberar($kilometrajeFinal, $observaciones = null): bool
    {
        $this->update([
            'fecha_liberacion' => Carbon::now(),
            'kilometraje_final' => $kilometrajeFinal,
            'estado' => self::ESTADO_LIBERADA,
            'observaciones' => $observaciones ? 
                ($this->observaciones ? $this->observaciones . "\n\n" . $observaciones : $observaciones) :
                $this->observaciones,
        ]);

        // Actualizar kilometraje y estado del vehículo
        if ($this->vehiculo) {
            $updateData = [];
            
            // Actualizar kilometraje si es mayor
            if ($kilometrajeFinal > $this->vehiculo->kilometraje_actual) {
                $updateData['kilometraje_actual'] = $kilometrajeFinal;
            }
            
            // Cambiar estado del vehículo a DISPONIBLE
            $updateData['estatus'] = \App\Enums\EstadoVehiculo::DISPONIBLE->value;
            
            $this->vehiculo->update($updateData);
        }

        return true;
    }

    /**
     * Crear una nueva asignación duplicando esta asignación pero con un nuevo operador
     * Usado cuando se cambia el operador de un vehículo para mantener historial
     */
    public function duplicarConNuevoOperador(int $nuevoOperadorId, string $observaciones = null): self
    {
        // Finalizar la asignación actual con fecha de liberación = ahora
        $this->update([
            'fecha_liberacion' => Carbon::now(),
            'estado' => self::ESTADO_LIBERADA,
            'observaciones' => ($this->observaciones ? $this->observaciones . "\n\n" : '') . 
                             "Asignación finalizada por cambio de operador. " . 
                             ($observaciones ? "Observaciones: " . $observaciones : '')
        ]);

        // Crear nueva asignación con los mismos datos pero nuevo operador
        return self::create([
            'obra_id' => $this->obra_id,
            'vehiculo_id' => $this->vehiculo_id,
            'operador_id' => $nuevoOperadorId,
            'fecha_asignacion' => Carbon::now(),
            'kilometraje_inicial' => $this->vehiculo->kilometraje_actual, // Usar kilometraje actual del vehículo
            'observaciones' => 'Asignación creada por cambio de operador. ' . 
                             ($observaciones ? "Observaciones: " . $observaciones : ''),
            'estado' => self::ESTADO_ACTIVA,
        ]);
    }

    /**
     * Obtener el historial completo de asignaciones para un vehículo en una obra específica
     */
    public static function obtenerHistorialVehiculoObra(int $vehiculoId, int $obraId): \Illuminate\Database\Eloquent\Collection
    {
        return self::with(['operador'])
                   ->historialPorVehiculoObra($vehiculoId, $obraId)
                   ->get();
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

                if (!self::obraTieneCapacidadParaNuevaAsignacion($asignacion->obra_id)) {
                    throw new \Exception('La obra no tiene capacidad para nuevas asignaciones');
                }
            }
        });

        // Log de creación y cambio de estado del vehículo
        static::created(function ($asignacion) {
            // Cambiar estado del vehículo a 'asignado' cuando se crea una asignación activa
            if ($asignacion->estado === self::ESTADO_ACTIVA && $asignacion->vehiculo) {
                $asignacion->vehiculo->update([
                    'estatus' => \App\Enums\EstadoVehiculo::ASIGNADO
                ]);
            }

            // Solo crear log si hay un usuario autenticado
            if (auth()->check()) {
                \App\Models\LogAccion::create([
                    'usuario_id' => auth()->id(),
                    'accion' => 'crear_asignacion_obra',
                    'tabla_afectada' => 'asignaciones_obra',
                    'registro_id' => $asignacion->id,
                    'detalles' => "Asignación creada: Vehículo {$asignacion->vehiculo->nombre_completo} -> Obra {$asignacion->obra->nombre_obra}",
                ]);
            }
        });

        // Cambiar estado del vehículo cuando se actualiza una asignación
        static::updated(function ($asignacion) {
            if ($asignacion->vehiculo) {
                // Si la asignación se libera, cambiar estado a disponible
                if ($asignacion->estado === self::ESTADO_LIBERADA && $asignacion->fecha_liberacion) {
                    // Verificar que no tenga otras asignaciones activas
                    $tieneOtrasAsignaciones = self::where('vehiculo_id', $asignacion->vehiculo_id)
                        ->where('id', '!=', $asignacion->id)
                        ->where('estado', self::ESTADO_ACTIVA)
                        ->whereNull('fecha_liberacion')
                        ->exists();

                    if (!$tieneOtrasAsignaciones) {
                        $asignacion->vehiculo->update([
                            'estatus' => \App\Enums\EstadoVehiculo::DISPONIBLE
                        ]);
                    }
                }
                // Si la asignación se activa, cambiar estado a asignado
                elseif ($asignacion->estado === self::ESTADO_ACTIVA) {
                    $asignacion->vehiculo->update([
                        'estatus' => \App\Enums\EstadoVehiculo::ASIGNADO
                    ]);
                }
            }
        });

        // Cambiar estado del vehículo cuando se elimina una asignación
        static::deleted(function ($asignacion) {
            if ($asignacion->vehiculo) {
                // Verificar que no tenga otras asignaciones activas
                $tieneOtrasAsignaciones = self::where('vehiculo_id', $asignacion->vehiculo_id)
                    ->where('id', '!=', $asignacion->id)
                    ->where('estado', self::ESTADO_ACTIVA)
                    ->whereNull('fecha_liberacion')
                    ->exists();

                if (!$tieneOtrasAsignaciones) {
                    $asignacion->vehiculo->update([
                        'estatus' => \App\Enums\EstadoVehiculo::DISPONIBLE
                    ]);
                }
            }
        });
    }
}
