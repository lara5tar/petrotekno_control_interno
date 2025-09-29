<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kilometraje extends Model
{
    use HasFactory;

    protected $table = 'kilometrajes';

    protected $fillable = [
        'vehiculo_id',
        'obra_id',
        'kilometraje',
        'fecha_captura',
        'usuario_captura_id',
        'observaciones',
        'cantidad_combustible',
        'created_at_registro',
    ];

    protected $casts = [
        'fecha_captura' => 'datetime',
        'created_at_registro' => 'datetime',
        'kilometraje' => 'integer',
        'cantidad_combustible' => 'decimal:2',
    ];

    protected $with = ['vehiculo', 'obra', 'usuarioCaptura.personal'];

    // Relaciones
    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function obra(): BelongsTo
    {
        return $this->belongsTo(Obra::class);
    }

    public function usuarioCaptura(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_captura_id');
    }

    // Scopes para filtros comunes
    public function scopeByVehiculo(Builder $query, int $vehiculoId): Builder
    {
        return $query->where('vehiculo_id', $vehiculoId);
    }

    public function scopeByFechas(Builder $query, string $fechaInicio, ?string $fechaFin = null): Builder
    {
        $query->whereDate('fecha_captura', '>=', $fechaInicio);

        if ($fechaFin) {
            $query->whereDate('fecha_captura', '<=', $fechaFin);
        }

        return $query;
    }

    public function scopeRecientes(Builder $query, int $dias = 30): Builder
    {
        return $query->where('fecha_captura', '>=', Carbon::now()->subDays($dias));
    }

    public function scopeOrderedByFecha(Builder $query, string $direction = 'desc'): Builder
    {
        return $query->orderBy('fecha_captura', $direction)
            ->orderBy('created_at', $direction);
    }

    // Accessors
    public function getKilometrajeFormateadoAttribute(): string
    {
        return number_format($this->kilometraje, 0, '.', ',') . ' km';
    }

    public function getFechaCapturaFormattedAttribute(): string
    {
        return $this->fecha_captura->format('d/m/Y');
    }

    public function getDiasDesdeCaptura(): int
    {
        return (int) $this->fecha_captura->diffInDays(Carbon::now());
    }

    // Métodos para reglas de negocio
    public static function getUltimoKilometraje(int $vehiculoId): ?self
    {
        return static::where('vehiculo_id', $vehiculoId)
            ->orderBy('kilometraje', 'desc')
            ->first();
    }

    public function esKilometrajeValido(int $nuevoKilometraje, int $vehiculoId): bool
    {
        $ultimo = static::getUltimoKilometraje($vehiculoId);

        if (! $ultimo) {
            return true; // Primera captura
        }

        return $nuevoKilometraje > $ultimo->kilometraje;
    }

    public function calcularProximosMantenimientos(): array
    {
        $vehiculo = $this->vehiculo;
        $alertas = [];

        if ($vehiculo->intervalo_km_motor) {
            $proximo = ceil($this->kilometraje / $vehiculo->intervalo_km_motor) * $vehiculo->intervalo_km_motor;
            $alertas[] = [
                'tipo' => 'Motor',
                'proximo_km' => $proximo,
                'km_restantes' => $proximo - $this->kilometraje,
                'urgente' => ($proximo - $this->kilometraje) <= 1000,
            ];
        }

        if ($vehiculo->intervalo_km_transmision) {
            $proximo = ceil($this->kilometraje / $vehiculo->intervalo_km_transmision) * $vehiculo->intervalo_km_transmision;
            $alertas[] = [
                'tipo' => 'Transmisión',
                'proximo_km' => $proximo,
                'km_restantes' => $proximo - $this->kilometraje,
                'urgente' => ($proximo - $this->kilometraje) <= 1000,
            ];
        }

        if ($vehiculo->intervalo_km_hidraulico) {
            $proximo = ceil($this->kilometraje / $vehiculo->intervalo_km_hidraulico) * $vehiculo->intervalo_km_hidraulico;
            $alertas[] = [
                'tipo' => 'Hidráulico',
                'proximo_km' => $proximo,
                'km_restantes' => $proximo - $this->kilometraje,
                'urgente' => ($proximo - $this->kilometraje) <= 1000,
            ];
        }

        return $alertas;
    }
}
