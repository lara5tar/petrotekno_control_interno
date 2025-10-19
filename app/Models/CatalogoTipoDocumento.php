<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatalogoTipoDocumento extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla
     */
    protected $table = 'catalogo_tipos_documento';

    /**
     * Atributos que se pueden asignar masivamente
     */
    protected $fillable = [
        'nombre_tipo_documento',
        'descripcion',
        'requiere_vencimiento',
    ];

    /**
     * Atributos que deben ser convertidos a tipos nativos
     */
    protected $casts = [
        'requiere_vencimiento' => 'boolean',
    ];

    /**
     * Valores por defecto para los atributos
     */
    protected $attributes = [
        'requiere_vencimiento' => false,
    ];

    /**
     * Relación uno a muchos con documentos
     */
    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'tipo_documento_id');
    }

    /**
     * Scope para tipos que requieren vencimiento
     */
    public function scopeQueRequierenVencimiento($query)
    {
        return $query->where('requiere_vencimiento', true);
    }

    /**
     * Scope para tipos que no requieren vencimiento
     */
    public function scopeQueNoRequierenVencimiento($query)
    {
        return $query->where('requiere_vencimiento', false);
    }

    /**
     * Accessor para compatibilidad con código existente
     */
    public function getNombreAttribute()
    {
        return $this->nombre_tipo_documento;
    }
}
