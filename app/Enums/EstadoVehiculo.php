<?php

namespace App\Enums;

enum EstadoVehiculo: string
{
    case DISPONIBLE = 'disponible';
    case ASIGNADO = 'asignado';
    case EN_MANTENIMIENTO = 'en_mantenimiento';
    case FUERA_DE_SERVICIO = 'fuera_de_servicio';
    case BAJA = 'baja';
    case BAJA_POR_VENTA = 'baja_por_venta';
    case BAJA_POR_PERDIDA = 'baja_por_perdida';

    /**
     * Obtener el nombre para mostrar del estado
     */
    public function nombre(): string
    {
        return match($this) {
            self::DISPONIBLE => 'Disponible',
            self::ASIGNADO => 'Asignado',
            self::EN_MANTENIMIENTO => 'En Mantenimiento',
            self::FUERA_DE_SERVICIO => 'Fuera de Servicio',
            self::BAJA => 'Baja',
            self::BAJA_POR_VENTA => 'Baja por Venta',
            self::BAJA_POR_PERDIDA => 'Baja por Pérdida',
        };
    }

    /**
     * Obtener la descripción del estado
     */
    public function descripcion(): string
    {
        return match($this) {
            self::DISPONIBLE => 'El vehículo está disponible para asignación',
            self::ASIGNADO => 'El vehículo está asignado a una obra o personal',
            self::EN_MANTENIMIENTO => 'El vehículo está en mantenimiento',
            self::FUERA_DE_SERVICIO => 'El vehículo está temporalmente fuera de servicio',
            self::BAJA => 'El vehículo ha sido dado de baja del inventario',
            self::BAJA_POR_VENTA => 'El vehículo ha sido vendido',
            self::BAJA_POR_PERDIDA => 'El vehículo se ha reportado como perdido o robado',
        };
    }

    /**
     * Obtener el color asociado al estado (para UI)
     */
    public function color(): string
    {
        return match($this) {
            self::DISPONIBLE => 'green',
            self::ASIGNADO => 'blue',
            self::EN_MANTENIMIENTO => 'yellow',
            self::FUERA_DE_SERVICIO => 'orange',
            self::BAJA => 'red',
            self::BAJA_POR_VENTA => 'purple',
            self::BAJA_POR_PERDIDA => 'gray',
        };
    }

    /**
     * Obtener todos los estados como un array para select inputs
     */
    public static function paraSelect(): array
    {
        $opciones = [];
        
        foreach (self::cases() as $estado) {
            $opciones[$estado->value] = $estado->nombre();
        }
        
        return $opciones;
    }
    
    /**
     * Determinar si el vehículo está disponible para asignación
     */
    public function estaDisponible(): bool
    {
        return $this === self::DISPONIBLE;
    }
    
    /**
     * Crear una instancia del enum a partir de su valor
     * 
     * @param string|null $value El valor del enum
     * @return static La instancia del enum correspondiente
     * @throws \ValueError Si el valor no corresponde a ningún caso del enum
     */
    public static function fromValue(?string $value): static
    {
        // Si el valor es nulo, devolver el valor predeterminado (DISPONIBLE)
        if ($value === null) {
            return self::DISPONIBLE;
        }
        
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }
        
        throw new \ValueError("\"$value\" no es un valor válido para el enum " . self::class);
    }
}