<?php

namespace App\Traits;

/**
 * Trait UppercaseAttributes
 * 
 * Convierte automáticamente los campos de texto importantes a MAYÚSCULAS
 * sin afectar campos de control como estados, roles, catálogos, etc.
 * 
 * Uso:
 * 1. Agregar el trait a cualquier modelo
 * 2. Definir la propiedad $uppercaseFields con los campos a convertir
 * 
 * Ejemplo:
 * protected $uppercaseFields = ['nombre', 'apellido', 'descripcion'];
 */
trait UppercaseAttributes
{
    /**
     * Boot del trait - se ejecuta automáticamente cuando se carga el modelo
     */
    protected static function bootUppercaseAttributes(): void
    {
        // Evento que se dispara antes de crear un registro
        static::creating(function ($model) {
            $model->convertToUppercase();
        });

        // Evento que se dispara antes de actualizar un registro
        static::updating(function ($model) {
            $model->convertToUppercase();
        });
    }

    /**
     * Convierte los campos especificados a mayúsculas
     */
    protected function convertToUppercase(): void
    {
        if (!property_exists($this, 'uppercaseFields')) {
            return;
        }

        foreach ($this->uppercaseFields as $field) {
            if (isset($this->attributes[$field]) && is_string($this->attributes[$field])) {
                // Convertir a mayúsculas manteniendo los caracteres especiales
                $this->attributes[$field] = mb_strtoupper($this->attributes[$field], 'UTF-8');
            }
        }
    }

    /**
     * Campos excluidos automáticamente (nunca se convertirán a mayúsculas)
     * Puedes sobrescribir esto en tu modelo si es necesario
     */
    protected function getExcludedFromUppercase(): array
    {
        return [
            // Campos de autenticación y control
            'password',
            'email',
            'remember_token',
            'api_token',
            
            // Campos de estado y control
            'estatus',
            'estado',
            'status',
            'rol',
            'role',
            'tipo',
            'type',
            'categoria',
            'category',
            
            // Campos de sistema
            'sistema_vehiculo',
            'tipo_servicio',
            'tipo_documento',
            'tipo_activo',
            
            // Fechas y timestamps
            'created_at',
            'updated_at',
            'deleted_at',
            'fecha_eliminacion',
            
            // URLs y archivos
            'url',
            'path',
            'ruta',
            'archivo',
            'file',
        ];
    }

    /**
     * Verifica si un campo debe ser excluido de la conversión
     */
    protected function shouldExcludeField(string $field): bool
    {
        $excluded = $this->getExcludedFromUppercase();
        
        // Excluir si el campo está en la lista
        if (in_array($field, $excluded)) {
            return true;
        }
        
        // Excluir si el campo contiene alguna de las palabras clave
        foreach ($excluded as $keyword) {
            if (str_contains($field, $keyword)) {
                return true;
            }
        }
        
        return false;
    }
}
