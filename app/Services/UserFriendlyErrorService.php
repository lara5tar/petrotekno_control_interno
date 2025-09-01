<?php

namespace App\Services;

use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserFriendlyErrorService
{
    /**
     * Convierte excepciones técnicas en mensajes amigables para el usuario
     */
    public static function getMessageForUser(Exception $e, string $operation = 'operación'): string
    {
        // Errores de base de datos
        if ($e instanceof QueryException) {
            return self::handleDatabaseError($e, $operation);
        }

        // Errores de validación
        if ($e instanceof ValidationException) {
            return self::getValidationMessage($e);
        }

        // Errores de modelo no encontrado
        if ($e instanceof ModelNotFoundException) {
            return 'El registro solicitado no existe o no se pudo encontrar.';
        }

        // Errores de autorización
        if ($e instanceof AuthorizationException) {
            return 'No tiene permisos para realizar esta acción.';
        }

        // Errores de página no encontrada
        if ($e instanceof NotFoundHttpException) {
            return 'La página o recurso solicitado no existe.';
        }

        // Errores de archivo
        if (self::isFileError($e)) {
            return self::handleFileError($e);
        }

        // Error genérico basado en la operación
        return self::getGenericMessage($operation);
    }

    /**
     * Maneja errores específicos de base de datos
     */
    private static function handleDatabaseError(QueryException $e, string $operation): string
    {
        $message = $e->getMessage();

        // Errores de duplicado
        if (str_contains($message, 'Duplicate entry')) {
            if (str_contains($message, 'email')) {
                return 'Este correo electrónico ya está registrado en el sistema.';
            }
            if (str_contains($message, 'curp')) {
                return 'Este CURP ya está registrado en el sistema.';
            }
            if (str_contains($message, 'rfc')) {
                return 'Este RFC ya está registrado en el sistema.';
            }
            if (str_contains($message, 'numero_poliza')) {
                return 'Este número de póliza ya está registrado en el sistema.';
            }
            if (str_contains($message, 'placas')) {
                return 'Estas placas ya están registradas en el sistema.';
            }
            if (str_contains($message, 'n_serie')) {
                return 'Este número de serie ya está registrado en el sistema.';
            }
            return 'Este registro ya existe en el sistema.';
        }

        // Errores de datos demasiado largos
        if (str_contains($message, 'Data too long')) {
            if (str_contains($message, 'marca')) {
                return 'La marca del vehículo es demasiado larga (máximo 50 caracteres).';
            }
            if (str_contains($message, 'modelo')) {
                return 'El modelo del vehículo es demasiado largo (máximo 100 caracteres).';
            }
            if (str_contains($message, 'placas')) {
                return 'Las placas son demasiado largas (máximo 20 caracteres).';
            }
            if (str_contains($message, 'n_serie')) {
                return 'El número de serie es demasiado largo (máximo 100 caracteres).';
            }
            if (str_contains($message, 'observaciones')) {
                return 'Las observaciones son demasiado largas (máximo 1000 caracteres).';
            }
            return 'Uno de los campos contiene demasiado texto. Verifique los límites de caracteres.';
        }

        // Errores de valor fuera de rango
        if (str_contains($message, 'Out of range')) {
            if (str_contains($message, 'anio')) {
                return 'El año del vehículo debe estar entre 1990 y ' . (date('Y') + 1) . '.';
            }
            if (str_contains($message, 'kilometraje')) {
                return 'El kilometraje debe ser un número válido y positivo.';
            }
            return 'Uno de los valores numéricos está fuera del rango permitido.';
        }

        // Errores de restricción de clave foránea
        if (str_contains($message, 'foreign key constraint') || str_contains($message, 'FOREIGN KEY')) {
            if (str_contains($message, 'tipo_activo_id')) {
                return 'El tipo de activo seleccionado no existe. Por favor, seleccione un tipo válido.';
            }
            if (str_contains($message, 'operador_id')) {
                return 'El operador seleccionado no existe. Por favor, seleccione un operador válido.';
            }
            if (str_contains($operation, 'eliminar')) {
                return 'No se puede eliminar este registro porque está siendo utilizado en otras partes del sistema.';
            }
            return 'La operación no se puede completar porque hace referencia a un registro que no existe.';
        }

        // Errores de campos requeridos (NOT NULL)
        if (str_contains($message, "cannot be null") || str_contains($message, "doesn't have a default value")) {
            if (str_contains($message, 'marca')) {
                return 'La marca del vehículo es obligatoria.';
            }
            if (str_contains($message, 'modelo')) {
                return 'El modelo del vehículo es obligatorio.';
            }
            if (str_contains($message, 'n_serie')) {
                return 'El número de serie del vehículo es obligatorio.';
            }
            if (str_contains($message, 'tipo_activo_id')) {
                return 'Debe seleccionar un tipo de activo.';
            }
            return 'Faltan campos obligatorios por completar.';
        }

        // Errores de columna no encontrada
        if (str_contains($message, 'Unknown column') || str_contains($message, "doesn't exist")) {
            return 'Hubo un problema con la estructura de datos. Contacte al administrador del sistema.';
        }

        // Errores de conexión
        if (str_contains($message, 'Connection refused') || str_contains($message, 'Connection timed out')) {
            return 'No se pudo conectar con la base de datos. Intente nuevamente en unos momentos.';
        }

        // Error genérico de base de datos
        return "Hubo un problema con la base de datos al realizar la {$operation}. Intente nuevamente.";
    }

    /**
     * Verifica si es un error relacionado con archivos
     */
    private static function isFileError(Exception $e): bool
    {
        $message = $e->getMessage();
        
        return str_contains($message, 'file') ||
               str_contains($message, 'upload') ||
               str_contains($message, 'storage') ||
               str_contains($message, 'permission') ||
               str_contains($message, 'directory');
    }

    /**
     * Maneja errores relacionados con archivos
     */
    private static function handleFileError(Exception $e): string
    {
        $message = $e->getMessage();

        if (str_contains($message, 'permission')) {
            return 'No se tienen los permisos necesarios para subir el archivo. Contacte al administrador.';
        }

        if (str_contains($message, 'size') || str_contains($message, 'too large')) {
            return 'El archivo es demasiado grande. Por favor, reduzca el tamaño e intente nuevamente.';
        }

        if (str_contains($message, 'format') || str_contains($message, 'type')) {
            return 'El formato del archivo no es válido. Verifique que sea un tipo de archivo permitido.';
        }

        if (str_contains($message, 'directory') || str_contains($message, 'storage')) {
            return 'Hubo un problema al guardar el archivo. Intente nuevamente.';
        }

        return 'Hubo un problema con el archivo. Verifique el archivo e intente nuevamente.';
    }

    /**
     * Obtiene un mensaje genérico basado en la operación
     */
    private static function getGenericMessage(string $operation): string
    {
        $messages = [
            'crear' => 'Hubo un problema al crear el registro. Intente nuevamente.',
            'actualizar' => 'Hubo un problema al actualizar el registro. Intente nuevamente.',
            'eliminar' => 'Hubo un problema al eliminar el registro. Intente nuevamente.',
            'guardar' => 'Hubo un problema al guardar los datos. Intente nuevamente.',
            'cargar' => 'Hubo un problema al cargar la información. Intente nuevamente.',
            'procesar' => 'Hubo un problema al procesar la solicitud. Intente nuevamente.',
        ];

        foreach ($messages as $key => $message) {
            if (str_contains($operation, $key)) {
                return $message;
            }
        }

        return "Hubo un problema al realizar la {$operation}. Si el problema persiste, contacte al administrador del sistema.";
    }

    /**
     * Obtiene un mensaje específico para operaciones comunes
     */
    public static function getOperationMessage(string $operation, Exception $e = null): string
    {
        $operations = [
            'crear_personal' => 'crear el personal',
            'actualizar_personal' => 'actualizar el personal',
            'eliminar_personal' => 'eliminar el personal',
            'crear_vehiculo' => 'crear el vehículo',
            'actualizar_vehiculo' => 'actualizar el vehículo',
            'eliminar_vehiculo' => 'eliminar el vehículo',
            'crear_obra' => 'crear la obra',
            'actualizar_obra' => 'actualizar la obra',
            'eliminar_obra' => 'eliminar la obra',
            'subir_documento' => 'subir el documento',
            'eliminar_documento' => 'eliminar el documento',
            'asignar_vehiculo' => 'asignar el vehículo',
            'crear_usuario' => 'crear el usuario',
            'crear_mantenimiento' => 'crear el mantenimiento',
            'actualizar_mantenimiento' => 'actualizar el mantenimiento',
        ];

        $operationText = $operations[$operation] ?? $operation;
        
        if ($e) {
            return self::getMessageForUser($e, $operationText);
        }

        return self::getGenericMessage($operationText);
    }

    /**
     * Maneja errores específicos de validación de Laravel
     */
    public static function getValidationMessage(ValidationException $e): string
    {
        $errors = $e->errors();
        $firstError = collect($errors)->flatten()->first();
        
        // Si hay un mensaje de validación específico, usarlo
        if ($firstError) {
            return $firstError;
        }
        
        return 'Los datos proporcionados no son válidos. Por favor, revise la información ingresada.';
    }

    /**
     * Registra el error técnico para debugging manteniendo el mensaje amigable para el usuario
     */
    public static function logTechnicalError(Exception $e, string $context = ''): void
    {
        \Log::error("Error técnico en {$context}: " . $e->getMessage(), [
            'exception' => $e,
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}
