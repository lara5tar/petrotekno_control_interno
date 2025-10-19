<?php

namespace App\Models;

use App\Traits\UppercaseAttributes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
    use HasFactory, SoftDeletes, UppercaseAttributes;

    /**
     * Campos que se convertirán automáticamente a MAYÚSCULAS
     */
    protected $uppercaseFields = [
        'nombre_obra',
        'ubicacion',
        'observaciones',
    ];

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
        'ubicacion',
        'estatus',
        'avance',
        'fecha_inicio',
        'fecha_fin',
        'observaciones',
        'encargado_id', // Añadido para permitir asignación masiva
        // Campos para archivos
        'archivo_contrato',
        'archivo_fianza',
        'archivo_acta_entrega_recepcion',
        'fecha_subida_contrato',
        'fecha_subida_fianza',
        'fecha_subida_acta',
    ];

    /**
     * Campos que deben ser convertidos a tipos nativos
     */
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'avance' => 'integer',
        'fecha_eliminacion' => 'datetime',
        // Campos de fechas de archivos
        'fecha_subida_contrato' => 'datetime',
        'fecha_subida_fianza' => 'datetime',
        'fecha_subida_acta' => 'datetime',
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
        'total_asignaciones_activas',
        'total_vehiculos_asignados',
        'total_operadores_asignados',
        'puede_recibir_nuevas_asignaciones',
    ];

    /**
     * Relación con asignaciones de obra (nuevas múltiples asignaciones)
     */
    public function asignacionesObra(): HasMany
    {
        return $this->hasMany(AsignacionObra::class);
    }

    /**
     * Relación con asignaciones activas de obra
     */
    public function asignacionesActivas(): HasMany
    {
        return $this->asignacionesObra()->activas()
            ->orderBy('fecha_asignacion', 'asc')
            ->orderBy('id', 'asc'); // Orden secundario para casos con misma fecha/hora
    }

    /**
     * Relación con asignaciones liberadas de obra
     */
    public function asignacionesLiberadas(): HasMany
    {
        return $this->asignacionesObra()->liberadas()
            ->orderBy('fecha_asignacion', 'desc')
            ->orderBy('id', 'desc'); // Orden secundario para casos con misma fecha/hora
    }

    /**
     * Relación con vehículos actualmente asignados (a través de asignaciones activas)
     */
    public function vehiculosAsignados()
    {
        return $this->hasManyThrough(
            Vehiculo::class,
            AsignacionObra::class,
            'obra_id',
            'id',
            'id',
            'vehiculo_id'
        )->where('asignaciones_obra.estado', AsignacionObra::ESTADO_ACTIVA);
    }

    /**
     * Relación many-to-many con vehículos
     */
    public function vehiculos()
    {
        return $this->belongsToMany(Vehiculo::class, 'asignaciones_obra', 'obra_id', 'vehiculo_id')
                   ->whereNull('asignaciones_obra.deleted_at')
                   ->whereNull('fecha_liberacion');
    }

    /**
     * Relación con operadores actualmente asignados (a través de asignaciones activas)
     */
    public function operadoresAsignados()
    {
        return $this->hasManyThrough(
            Personal::class,
            AsignacionObra::class,
            'obra_id',
            'id',
            'id',
            'operador_id'
        )->where('asignaciones_obra.estado', AsignacionObra::ESTADO_ACTIVA);
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
     * Relación con el personal encargado
     */
    public function encargado(): BelongsTo
    {
        return $this->belongsTo(Personal::class, 'encargado_id');
    }

    /**
     * Relación con el personal que creó la obra (alias para encargado)
     */
    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(Personal::class, 'encargado_id');
    }

    /**
     * Relación con el historial de operadores asignados a esta obra
     */
    public function historialOperadores(): HasMany
    {
        return $this->hasMany(HistorialOperadorVehiculo::class, 'obra_id')
            ->with(['operadorNuevo', 'vehiculo', 'usuarioAsigno'])
            ->orderBy('fecha_asignacion', 'desc');
    }

    /**
     * Relación con el historial de responsables asignados a esta obra
     */
    public function historialResponsables(): HasMany
    {
        return $this->hasMany(AsignacionObraResponsable::class, 'obra_id')
            ->with(['responsable', 'usuarioAsigno', 'usuarioLibero'])
            ->orderBy('fecha_asignacion', 'desc');
    }

    /**
     * Relación con el responsable actual de la obra
     */
    public function responsableActual(): HasOne
    {
        return $this->hasOne(AsignacionObraResponsable::class, 'obra_id')
            ->where('estado', AsignacionObraResponsable::ESTADO_ACTIVA)
            ->with(['responsable']);
    }

    /**
     * Relación con todas las asignaciones activas de responsables
     */
    public function asignacionesResponsableActivas(): HasMany
    {
        return $this->hasMany(AsignacionObraResponsable::class, 'obra_id')
            ->where('estado', AsignacionObraResponsable::ESTADO_ACTIVA)
            ->with(['responsable', 'usuarioAsigno']);
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

        return (int) floor($fechaInicio->diffInDays($fechaActual));
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

        return (int) floor($fechaActual->diffInDays($fechaFin));
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

        return (int) floor($fechaInicio->diffInDays($fechaFin) + 1);
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
     * Método para cambiar el estatus sin restricciones
     */
    public function cambiarEstatus($nuevoEstatus, $motivo = null)
    {
        $estatusAnterior = $this->estatus;

        // Permitir cambios de estado sin restricciones

        $this->estatus = $nuevoEstatus;

        // Si se completa, establecer avance al 100%
        if ($nuevoEstatus === self::ESTATUS_COMPLETADA) {
            $this->avance = 100;
        }

        return $this->save();
    }

    /**
     * Método para subir archivo de contrato
     */
    public function subirContrato($archivo)
    {
        if ($archivo && $archivo->isValid()) {
            $ruta = $archivo->store('obras/contratos', 'public');
            $this->archivo_contrato = $ruta;
            $this->fecha_subida_contrato = now();
            
            // Guardar en tabla obras
            $saved = $this->save();
            
            if ($saved) {
                // También crear registro en tabla documentos
                $this->documentos()->create([
                    'tipo_documento_id' => 21, // Contrato de Obra
                    'descripcion' => 'Contrato de la obra: ' . $this->nombre_obra,
                    'ruta_archivo' => $ruta,
                    'contenido' => [
                        'nombre_original' => $archivo->getClientOriginalName(),
                        'tamaño' => $archivo->getSize(),
                        'tipo_mime' => $archivo->getMimeType(),
                        'fecha_subida' => now()->format('Y-m-d H:i:s')
                    ]
                ]);
            }
            
            return $saved;
        }
        return false;
    }

    /**
     * Método para subir archivo de fianza
     */
    public function subirFianza($archivo)
    {
        if ($archivo && $archivo->isValid()) {
            $ruta = $archivo->store('obras/fianzas', 'public');
            $this->archivo_fianza = $ruta;
            $this->fecha_subida_fianza = now();
            
            // Guardar en tabla obras
            $saved = $this->save();
            
            if ($saved) {
                // También crear registro en tabla documentos
                $this->documentos()->create([
                    'tipo_documento_id' => 26, // Póliza de Fianza
                    'descripcion' => 'Póliza de fianza de la obra: ' . $this->nombre_obra,
                    'ruta_archivo' => $ruta,
                    'contenido' => [
                        'nombre_original' => $archivo->getClientOriginalName(),
                        'tamaño' => $archivo->getSize(),
                        'tipo_mime' => $archivo->getMimeType(),
                        'fecha_subida' => now()->format('Y-m-d H:i:s')
                    ]
                ]);
            }
            
            return $saved;
        }
        return false;
    }

    /**
     * Método para subir archivo de acta entrega-recepción
     */
    public function subirActaEntregaRecepcion($archivo)
    {
        if ($archivo && $archivo->isValid()) {
            $ruta = $archivo->store('obras/actas', 'public');
            $this->archivo_acta_entrega_recepcion = $ruta;
            $this->fecha_subida_acta = now();
            
            // Guardar en tabla obras
            $saved = $this->save();
            
            if ($saved) {
                // También crear registro en tabla documentos
                $this->documentos()->create([
                    'tipo_documento_id' => 28, // Acta de Entrega-Recepción
                    'descripcion' => 'Acta de entrega-recepción de la obra: ' . $this->nombre_obra,
                    'ruta_archivo' => $ruta,
                    'contenido' => [
                        'nombre_original' => $archivo->getClientOriginalName(),
                        'tamaño' => $archivo->getSize(),
                        'tipo_mime' => $archivo->getMimeType(),
                        'fecha_subida' => now()->format('Y-m-d H:i:s')
                    ]
                ]);
            }
            
            return $saved;
        }
        return false;
    }

    /**
     * Verificar si tiene contrato subido
     */
    public function tieneContrato()
    {
        return !empty($this->archivo_contrato);
    }

    /**
     * Verificar si tiene fianza subida
     */
    public function tieneFianza()
    {
        return !empty($this->archivo_fianza);
    }

    /**
     * Verificar si tiene acta entrega-recepción subida
     */
    public function tieneActaEntregaRecepcion()
    {
        return !empty($this->archivo_acta_entrega_recepcion);
    }

    /**
     * Obtener URL completa del contrato
     */
    public function getUrlContrato()
    {
        return $this->archivo_contrato ? asset('storage/' . $this->archivo_contrato) : null;
    }

    /**
     * Obtener URL completa de la fianza
     */
    public function getUrlFianza()
    {
        return $this->archivo_fianza ? asset('storage/' . $this->archivo_fianza) : null;
    }

    /**
     * Obtener URL completa del acta entrega-recepción
     */
    public function getUrlActaEntregaRecepcion()
    {
        return $this->archivo_acta_entrega_recepcion ? asset('storage/' . $this->archivo_acta_entrega_recepcion) : null;
    }

    /**
     * Obtener porcentaje de documentos completados
     */
    public function getPorcentajeDocumentosCompletados()
    {
        $total = 3; // contrato, fianza, acta
        $completados = 0;
        
        if ($this->tieneContrato()) $completados++;
        if ($this->tieneFianza()) $completados++;
        if ($this->tieneActaEntregaRecepcion()) $completados++;
        
        return round(($completados / $total) * 100, 1);
    }

    /**
     * Accessors para múltiples asignaciones
     */
    public function getTotalAsignacionesActivasAttribute()
    {
        return $this->asignacionesActivas()->count();
    }

    public function getTotalVehiculosAsignadosAttribute()
    {
        return $this->asignacionesActivas()->distinct('vehiculo_id')->count();
    }

    public function getTotalOperadoresAsignadosAttribute()
    {
        return $this->asignacionesActivas()->distinct('operador_id')->count();
    }

    public function getPuedeRecibirNuevasAsignacionesAttribute()
    {
        // Si tiene límite de vehículos y ya lo alcanzó
        if ($this->max_vehiculos && $this->total_vehiculos_asignados >= $this->max_vehiculos) {
            return false;
        }

        // La obra debe estar activa (no cancelada o completada)
        return in_array($this->estatus, [
            self::ESTATUS_PLANIFICADA,
            self::ESTATUS_EN_PROGRESO,
            self::ESTATUS_SUSPENDIDA
        ]);
    }

    /**
     * Métodos para gestionar múltiples asignaciones
     */
    public function asignarVehiculoYOperador($vehiculoId, $operadorId, $datos = [])
    {
        // Verificar que la obra puede recibir nuevas asignaciones
        if (!$this->puede_recibir_nuevas_asignaciones) {
            throw new \Exception('La obra no puede recibir nuevas asignaciones');
        }

        // REGLA ESTRICTA: Verificar que el vehículo NO tenga ninguna asignación activa
        AsignacionObra::validarAsignacionUnicaVehiculo($vehiculoId);

        // NOTA: Los operadores SÍ pueden tener múltiples asignaciones
        // Un operador puede manejar varios vehículos en diferentes obras

        // Crear la nueva asignación (sin encargado_id, se obtiene a través de la obra)
        return $this->asignacionesObra()->create([
            'vehiculo_id' => $vehiculoId,
            'operador_id' => $operadorId,
            'fecha_asignacion' => now(),
            'kilometraje_inicial' => $datos['kilometraje_inicial'] ?? null,
            'combustible_inicial' => $datos['combustible_inicial'] ?? null,
            'observaciones' => $datos['observaciones'] ?? null,
            'estado' => AsignacionObra::ESTADO_ACTIVA,
        ]);
    }

    public function liberarAsignacion($asignacionId, $kilometrajeFinal, $datos = [])
    {
        $asignacion = $this->asignacionesObra()->findOrFail($asignacionId);
        
        if (!$asignacion->esta_activa) {
            throw new \Exception('La asignación ya está liberada');
        }

        return $asignacion->liberar(
            $kilometrajeFinal,
            $datos['combustible_final'] ?? null,
            $datos['observaciones'] ?? null
        );
    }

    public function transferirAsignacion($asignacionId, $nuevoOperadorId, $kilometrajeTransferencia, $observaciones = null)
    {
        $asignacion = $this->asignacionesObra()->findOrFail($asignacionId);
        
        if (!$asignacion->esta_activa) {
            throw new \Exception('Solo se pueden transferir asignaciones activas');
        }

        // Verificar que el nuevo operador esté disponible
        if (AsignacionObra::operadorTieneAsignacionActiva($nuevoOperadorId, $asignacionId)) {
            throw new \Exception('El nuevo operador ya tiene una asignación activa');
        }

        return $asignacion->transferir($nuevoOperadorId, $kilometrajeTransferencia, $observaciones);
    }

    public function getResumenAsignaciones()
    {
        $asignacionesActivas = $this->asignacionesActivas()->with(['vehiculo', 'operador'])->get();
        $asignacionesLiberadas = $this->asignacionesLiberadas()->with(['vehiculo', 'operador'])->get();

        return [
            'activas' => [
                'total' => $asignacionesActivas->count(),
                'vehiculos' => $asignacionesActivas->pluck('vehiculo.nombre_completo')->unique()->values(),
                'operadores' => $asignacionesActivas->pluck('operador.nombre_completo')->unique()->values(),
                'detalles' => $asignacionesActivas
            ],
            'liberadas' => [
                'total' => $asignacionesLiberadas->count(),
                'kilometraje_total' => $asignacionesLiberadas->sum('kilometraje_recorrido'),
                'combustible_total' => $asignacionesLiberadas->sum('combustible_consumido'),
                'detalles' => $asignacionesLiberadas
            ],
            'capacidad' => [
                'max_vehiculos' => $this->max_vehiculos,
                'max_operadores' => $this->max_operadores,
                'puede_recibir_nuevas' => $this->puede_recibir_nuevas_asignaciones
            ]
        ];
    }

    /**
     * Scope para obras con asignaciones activas
     */
    public function scopeConAsignacionesActivas($query)
    {
        return $query->whereHas('asignacionesActivas');
    }

    /**
     * Scope para obras sin asignaciones activas
     */
    public function scopeSinAsignacionesActivas($query)
    {
        return $query->whereDoesntHave('asignacionesActivas');
    }

    /**
     * Scope para obras que pueden recibir nuevas asignaciones
     */
    public function scopeDisponiblesParaAsignacion($query)
    {
        return $query->whereIn('estatus', [
            self::ESTATUS_PLANIFICADA,
            self::ESTATUS_EN_PROGRESO,
            self::ESTATUS_SUSPENDIDA
        ]);
    }

    /**
     * =====================================================
     * MÉTODOS PARA GESTIÓN DE HISTORIAL DE RESPONSABLES
     * =====================================================
     */

    /**
     * Asignar un nuevo responsable a la obra
     */
    public function asignarResponsable(int $responsableId, int $usuarioAsignoId = null, string $motivo = null): AsignacionObraResponsable
    {
        // Liberar el responsable actual si existe
        $responsableActualAsignacion = $this->responsableActual;
        if ($responsableActualAsignacion) {
            $responsableActualAsignacion->liberar(
                'Cambio de responsable', 
                $usuarioAsignoId,
                'Liberado automáticamente por cambio de responsable'
            );
        }

        // Crear nueva asignación
        return AsignacionObraResponsable::create([
            'obra_id' => $this->id,
            'responsable_id' => $responsableId,
            'fecha_asignacion' => now(),
            'usuario_asigno_id' => $usuarioAsignoId,
            'motivo_asignacion' => $motivo ?? 'Asignación de responsable',
            'estado' => AsignacionObraResponsable::ESTADO_ACTIVA,
        ]);
    }

    /**
     * Liberar el responsable actual de la obra
     */
    public function liberarResponsable(string $motivo = null, int $usuarioLiberoId = null): bool
    {
        $responsableActual = $this->responsableActual;
        
        if (!$responsableActual) {
            throw new \Exception('La obra no tiene un responsable activo asignado');
        }

        return $responsableActual->liberar($motivo, $usuarioLiberoId);
    }

    /**
     * Transferir la responsabilidad de la obra a otro personal
     */
    public function transferirResponsable(int $nuevoResponsableId, string $motivo = null, int $usuarioId = null): AsignacionObraResponsable
    {
        $responsableActual = $this->responsableActual;
        
        if (!$responsableActual) {
            throw new \Exception('La obra no tiene un responsable activo para transferir');
        }

        return $responsableActual->transferir($nuevoResponsableId, $motivo, $usuarioId);
    }

    /**
     * Obtener el responsable actual de la obra
     */
    public function getResponsableActual(): ?Personal
    {
        $asignacion = $this->responsableActual;
        return $asignacion ? $asignacion->responsable : null;
    }

    /**
     * Verificar si la obra tiene un responsable activo
     */
    public function tieneResponsableActivo(): bool
    {
        return $this->responsableActual !== null;
    }

    /**
     * Obtener resumen del historial de responsables
     */
    public function getResumenResponsables(): array
    {
        $historial = $this->historialResponsables;
        $responsableActual = $this->getResponsableActual();

        return [
            'responsable_actual' => [
                'personal' => $responsableActual,
                'asignacion' => $this->responsableActual,
                'dias_asignado' => $this->responsableActual?->dias_asignado ?? 0
            ],
            'historial' => [
                'total_asignaciones' => $historial->count(),
                'total_responsables_diferentes' => $historial->pluck('responsable_id')->unique()->count(),
                'asignaciones_activas' => $historial->where('estado', AsignacionObraResponsable::ESTADO_ACTIVA)->count(),
                'asignaciones_liberadas' => $historial->where('estado', AsignacionObraResponsable::ESTADO_LIBERADA)->count(),
                'asignaciones_transferidas' => $historial->where('estado', AsignacionObraResponsable::ESTADO_TRANSFERIDA)->count(),
                'detalles' => $historial
            ]
        ];
    }

    /**
     * Scope para obras con responsable actual
     */
    public function scopeConResponsableActivo($query)
    {
        return $query->whereHas('responsableActual');
    }

    /**
     * Scope para obras sin responsable actual
     */
    public function scopeSinResponsableActivo($query)
    {
        return $query->whereDoesntHave('responsableActual');
    }

    /**
     * Scope para obras de un responsable específico
     */
    public function scopeDelResponsable($query, int $responsableId)
    {
        return $query->whereHas('responsableActual', function ($q) use ($responsableId) {
            $q->where('responsable_id', $responsableId);
        });
    }
}
