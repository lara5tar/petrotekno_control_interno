<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

/**
 * Modelo para gestionar el historial de asignaciones de responsables a obras
 *
 * @property int $id
 * @property int $obra_id
 * @property int $responsable_id
 * @property \Carbon\Carbon $fecha_asignacion
 * @property \Carbon\Carbon|null $fecha_liberacion
 * @property int|null $usuario_asigno_id
 * @property int|null $usuario_libero_id
 * @property string|null $motivo_asignacion
 * @property string|null $motivo_liberacion
 * @property string|null $observaciones
 * @property string $estado
 * @property \Carbon\Carbon|null $fecha_eliminacion
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class AsignacionObraResponsable extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nombre de la tabla en la base de datos
     */
    protected $table = 'asignaciones_obra_responsable';

    /**
     * Nombre de la columna para soft deletes
     */
    const DELETED_AT = 'fecha_eliminacion';

    /**
     * Estados válidos para las asignaciones
     */
    const ESTADO_ACTIVA = 'activa';
    const ESTADO_LIBERADA = 'liberada';
    const ESTADO_TRANSFERIDA = 'transferida';

    /**
     * Array de estados válidos
     */
    const ESTADOS_VALIDOS = [
        self::ESTADO_ACTIVA,
        self::ESTADO_LIBERADA,
        self::ESTADO_TRANSFERIDA,
    ];

    /**
     * Campos que se pueden asignar masivamente
     */
    protected $fillable = [
        'obra_id',
        'responsable_id',
        'fecha_asignacion',
        'fecha_liberacion',
        'usuario_asigno_id',
        'usuario_libero_id',
        'motivo_asignacion',
        'motivo_liberacion',
        'observaciones',
        'estado',
    ];

    /**
     * Campos que deben ser convertidos a tipos nativos
     */
    protected $casts = [
        'fecha_asignacion' => 'datetime',
        'fecha_liberacion' => 'datetime',
        'fecha_eliminacion' => 'datetime',
    ];

    /**
     * Atributos adicionales que deben agregarse al array/JSON
     */
    protected $appends = [
        'dias_asignado',
        'esta_activa',
        'duracion_asignacion',
    ];

    /**
     * Relación con la obra
     */
    public function obra(): BelongsTo
    {
        return $this->belongsTo(Obra::class);
    }

    /**
     * Relación con el responsable (personal)
     */
    public function responsable(): BelongsTo
    {
        return $this->belongsTo(Personal::class, 'responsable_id');
    }

    /**
     * Relación con el usuario que asignó
     */
    public function usuarioAsigno(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_asigno_id');
    }

    /**
     * Relación con el usuario que liberó
     */
    public function usuarioLibero(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_libero_id');
    }

    /**
     * Scope para asignaciones activas
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', self::ESTADO_ACTIVA);
    }

    /**
     * Scope para asignaciones liberadas
     */
    public function scopeLiberadas($query)
    {
        return $query->where('estado', self::ESTADO_LIBERADA);
    }

    /**
     * Scope para asignaciones transferidas
     */
    public function scopeTransferidas($query)
    {
        return $query->where('estado', self::ESTADO_TRANSFERIDA);
    }

    /**
     * Scope para filtrar por obra
     */
    public function scopePorObra($query, $obraId)
    {
        return $query->where('obra_id', $obraId);
    }

    /**
     * Scope para filtrar por responsable
     */
    public function scopePorResponsable($query, $responsableId)
    {
        return $query->where('responsable_id', $responsableId);
    }

    /**
     * Accessor para determinar si la asignación está activa
     */
    public function getEstaActivaAttribute(): bool
    {
        return $this->estado === self::ESTADO_ACTIVA && is_null($this->fecha_liberacion);
    }

    /**
     * Accessor para obtener días asignado
     */
    public function getDiasAsignadoAttribute(): int
    {
        $fechaFin = $this->fecha_liberacion ?? Carbon::now();
        return $this->fecha_asignacion->diffInDays($fechaFin);
    }

    /**
     * Accessor para obtener duración de la asignación en formato legible
     */
    public function getDuracionAsignacionAttribute(): string
    {
        if ($this->esta_activa) {
            $dias = $this->dias_asignado;
            if ($dias === 0) {
                return 'Asignado hoy';
            } elseif ($dias === 1) {
                return '1 día';
            } else {
                return "{$dias} días";
            }
        } else {
            $dias = $this->fecha_asignacion->diffInDays($this->fecha_liberacion);
            if ($dias === 0) {
                return 'Menos de 1 día';
            } elseif ($dias === 1) {
                return '1 día';
            } else {
                return "{$dias} días";
            }
        }
    }

    /**
     * Mutator para validar el estado
     */
    public function setEstadoAttribute($value)
    {
        if (!in_array($value, self::ESTADOS_VALIDOS)) {
            throw new \InvalidArgumentException("Estado '{$value}' no es válido.");
        }
        $this->attributes['estado'] = $value;
    }

    /**
     * Método para liberar la asignación
     */
    public function liberar($motivoLiberacion = null, $usuarioLiberoId = null, $observaciones = null): bool
    {
        if (!$this->esta_activa) {
            throw new \Exception('La asignación ya está liberada o transferida');
        }

        $this->estado = self::ESTADO_LIBERADA;
        $this->fecha_liberacion = Carbon::now();
        $this->motivo_liberacion = $motivoLiberacion;
        $this->usuario_libero_id = $usuarioLiberoId;
        
        if ($observaciones) {
            $this->observaciones = $this->observaciones 
                ? $this->observaciones . "\n\n" . $observaciones 
                : $observaciones;
        }

        return $this->save();
    }

    /**
     * Método para transferir la asignación
     */
    public function transferir($nuevoResponsableId, $motivoTransferencia = null, $usuarioLiberoId = null): self
    {
        if (!$this->esta_activa) {
            throw new \Exception('Solo se pueden transferir asignaciones activas');
        }

        // Marcar la asignación actual como transferida
        $this->estado = self::ESTADO_TRANSFERIDA;
        $this->fecha_liberacion = Carbon::now();
        $this->motivo_liberacion = $motivoTransferencia ?? 'Transferencia de responsabilidad';
        $this->usuario_libero_id = $usuarioLiberoId;
        $this->save();

        // Crear nueva asignación para el nuevo responsable
        return self::create([
            'obra_id' => $this->obra_id,
            'responsable_id' => $nuevoResponsableId,
            'fecha_asignacion' => Carbon::now(),
            'usuario_asigno_id' => $usuarioLiberoId,
            'motivo_asignacion' => 'Transferencia desde responsable anterior',
            'estado' => self::ESTADO_ACTIVA,
        ]);
    }

    /**
     * Método estático para asignar un nuevo responsable a una obra
     */
    public static function asignarResponsable(
        int $obraId, 
        int $responsableId, 
        int $usuarioAsignoId = null, 
        string $motivoAsignacion = null
    ): self {
        // Verificar si hay una asignación activa para esta obra
        $asignacionActiva = self::where('obra_id', $obraId)
            ->activas()
            ->first();

        if ($asignacionActiva) {
            throw new \Exception('La obra ya tiene un responsable activo asignado');
        }

        return self::create([
            'obra_id' => $obraId,
            'responsable_id' => $responsableId,
            'fecha_asignacion' => Carbon::now(),
            'usuario_asigno_id' => $usuarioAsignoId,
            'motivo_asignacion' => $motivoAsignacion ?? 'Asignación inicial',
            'estado' => self::ESTADO_ACTIVA,
        ]);
    }

    /**
     * Método estático para obtener el responsable actual de una obra
     */
    public static function responsableActual(int $obraId): ?self
    {
        return self::where('obra_id', $obraId)
            ->activas()
            ->with(['responsable', 'usuarioAsigno'])
            ->first();
    }

    /**
     * Método estático para obtener el historial completo de una obra
     */
    public static function historialObra(int $obraId)
    {
        return self::where('obra_id', $obraId)
            ->with(['responsable', 'usuarioAsigno', 'usuarioLibero'])
            ->orderBy('fecha_asignacion', 'desc')
            ->get();
    }

    /**
     * Método estático para obtener todas las obras asignadas a un responsable
     */
    public static function obrasDelResponsable(int $responsableId, bool $soloActivas = false)
    {
        $query = self::where('responsable_id', $responsableId)
            ->with(['obra', 'usuarioAsigno']);

        if ($soloActivas) {
            $query->activas();
        }

        return $query->orderBy('fecha_asignacion', 'desc')->get();
    }
}
