<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionAlerta extends Model
{
    protected $table = 'configuracion_alertas';

    protected $fillable = [
        'tipo_config',
        'clave',
        'valor',
        'descripcion',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Scopes
     */
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo_config', $tipo);
    }

    public function scopeClave($query, $clave)
    {
        return $query->where('clave', $clave);
    }

    /**
     * Accessor para decodificar JSON automáticamente
     */
    public function getValorAttribute($value)
    {
        // Si el valor parece ser JSON, decodificarlo
        if (is_string($value) && (str_starts_with($value, '[') || str_starts_with($value, '{'))) {
            $decoded = json_decode($value, true);
            return $decoded !== null ? $decoded : $value;
        }

        // Convertir strings boolean
        if ($value === 'true') return true;
        if ($value === 'false') return false;

        return $value;
    }
}
