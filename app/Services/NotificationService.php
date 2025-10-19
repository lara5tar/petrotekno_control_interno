<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class NotificationService
{
    /**
     * Tipos de notificación disponibles
     */
    const TYPE_SUCCESS = 'success';
    const TYPE_ERROR = 'error';
    const TYPE_WARNING = 'warning';
    const TYPE_INFO = 'info';

    /**
     * Crear notificación de éxito
     */
    public static function success(string $message, array $data = []): void
    {
        self::addNotification(self::TYPE_SUCCESS, $message, $data);
    }

    /**
     * Crear notificación de error
     */
    public static function error(string $message, array $data = []): void
    {
        self::addNotification(self::TYPE_ERROR, $message, $data);
    }

    /**
     * Crear notificación de advertencia
     */
    public static function warning(string $message, array $data = []): void
    {
        self::addNotification(self::TYPE_WARNING, $message, $data);
    }

    /**
     * Crear notificación de información
     */
    public static function info(string $message, array $data = []): void
    {
        self::addNotification(self::TYPE_INFO, $message, $data);
    }

    /**
     * Agregar notificación al sistema de sesión
     */
    private static function addNotification(string $type, string $message, array $data = []): void
    {
        $notification = [
            'type' => $type,
            'message' => $message,
            'data' => $data,
            'timestamp' => time(),
            'id' => uniqid()
        ];

        Session::flash('notification', $notification);
        
        // Mantener compatibilidad con sistema anterior
        if ($type === self::TYPE_SUCCESS) {
            Session::flash('success', $message);
        } elseif ($type === self::TYPE_ERROR) {
            Session::flash('error', $message);
        }
    }

    /**
     * Obtener todas las notificaciones pendientes
     */
    public static function getNotifications(): array
    {
        $notifications = [];
        
        // Obtener notificación principal
        if (Session::has('notification')) {
            $notifications[] = Session::get('notification');
        }
        
        // Obtener notificaciones adicionales (para múltiples notificaciones)
        if (Session::has('notifications')) {
            $notifications = array_merge($notifications, Session::get('notifications'));
        }

        return $notifications;
    }

    /**
     * Agregar múltiples notificaciones
     */
    public static function addMultiple(array $notifications): void
    {
        $currentNotifications = Session::get('notifications', []);
        
        foreach ($notifications as $notification) {
            $currentNotifications[] = [
                'type' => $notification['type'],
                'message' => $notification['message'],
                'data' => $notification['data'] ?? [],
                'timestamp' => time(),
                'id' => uniqid()
            ];
        }
        
        Session::flash('notifications', $currentNotifications);
    }

    /**
     * Crear notificación toast (para JavaScript)
     */
    public static function toast(string $type, string $message, array $options = []): array
    {
        return [
            'type' => $type,
            'message' => $message,
            'options' => array_merge([
                'duration' => 5000,
                'position' => 'top-right',
                'closable' => true,
                'icon' => true
            ], $options)
        ];
    }

    /**
     * Notificaciones específicas para operaciones comunes
     */
    
    public static function operationSuccess(string $operation, string $entity = 'registro'): void
    {
        $messages = [
            'crear' => "✅ {$entity} creado exitosamente",
            'actualizar' => "✅ {$entity} actualizado exitosamente",
            'eliminar' => "✅ {$entity} eliminado exitosamente",
            'guardar' => "✅ {$entity} guardado exitosamente",
            'enviar' => "✅ {$entity} enviado exitosamente",
            'subir' => "✅ {$entity} subido exitosamente",
            'procesar' => "✅ {$entity} procesado exitosamente",
        ];

        $message = $messages[$operation] ?? "✅ Operación realizada exitosamente";
        self::success($message);
    }

    public static function operationError(string $operation, string $entity = 'registro'): void
    {
        $messages = [
            'crear' => "❌ Error al crear {$entity}",
            'actualizar' => "❌ Error al actualizar {$entity}",
            'eliminar' => "❌ Error al eliminar {$entity}",
            'guardar' => "❌ Error al guardar {$entity}",
            'cargar' => "❌ Error al cargar {$entity}",
            'enviar' => "❌ Error al enviar {$entity}",
            'subir' => "❌ Error al subir {$entity}",
            'procesar' => "❌ Error al procesar {$entity}",
        ];

        $message = $messages[$operation] ?? "❌ Error en la operación";
        self::error($message);
    }

    public static function validationError(string $message = null): void
    {
        $defaultMessage = "⚠️ Por favor, corrija los errores en el formulario";
        self::warning($message ?? $defaultMessage);
    }

    public static function permissionDenied(string $action = 'realizar esta acción'): void
    {
        self::warning("🔒 No tiene permisos para {$action}");
    }

    public static function recordNotFound(string $entity = 'registro'): void
    {
        self::error("🔍 El {$entity} solicitado no fue encontrado");
    }

    /**
     * Métodos helper para tipos específicos del sistema
     */
    
    public static function personalCreated(): void
    {
        self::operationSuccess('crear', 'personal');
    }

    public static function personalUpdated(): void
    {
        self::operationSuccess('actualizar', 'personal');
    }

    public static function vehiculoCreated(): void
    {
        self::operationSuccess('crear', 'vehículo');
    }

    public static function vehiculoUpdated(): void
    {
        self::operationSuccess('actualizar', 'vehículo');
    }

    public static function obraCreated(): void
    {
        self::operationSuccess('crear', 'obra');
    }

    public static function obraUpdated(): void
    {
        self::operationSuccess('actualizar', 'obra');
    }

    public static function documentUploaded(string $documentType = 'documento'): void
    {
        self::success("📄 {$documentType} subido exitosamente");
    }

    public static function userPasswordGenerated(string $email, string $password): void
    {
        self::success("🔑 Usuario creado exitosamente. Contraseña generada: <strong>{$password}</strong><br>Email: {$email}");
    }

    /**
     * Crear notificación con acción
     */
    public static function withAction(string $type, string $message, string $actionText, string $actionUrl): void
    {
        self::addNotification($type, $message, [
            'action' => [
                'text' => $actionText,
                'url' => $actionUrl
            ]
        ]);
    }

    /**
     * Crear notificación de progreso
     */
    public static function progress(string $message, int $percentage = 0): void
    {
        self::addNotification(self::TYPE_INFO, $message, [
            'progress' => $percentage,
            'type' => 'progress'
        ]);
    }

    /**
     * Obtener configuración de estilos para cada tipo
     */
    public static function getTypeStyles(string $type): array
    {
        $styles = [
            self::TYPE_SUCCESS => [
                'bg' => 'bg-green-100',
                'border' => 'border-green-400',
                'text' => 'text-green-700',
                'icon' => 'text-green-400',
                'icon_path' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
            ],
            self::TYPE_ERROR => [
                'bg' => 'bg-red-100',
                'border' => 'border-red-400',
                'text' => 'text-red-700',
                'icon' => 'text-red-400',
                'icon_path' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'
            ],
            self::TYPE_WARNING => [
                'bg' => 'bg-yellow-100',
                'border' => 'border-yellow-400',
                'text' => 'text-yellow-700',
                'icon' => 'text-yellow-400',
                'icon_path' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z'
            ],
            self::TYPE_INFO => [
                'bg' => 'bg-blue-100',
                'border' => 'border-blue-400',
                'text' => 'text-blue-700',
                'icon' => 'text-blue-400',
                'icon_path' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
            ]
        ];

        return $styles[$type] ?? $styles[self::TYPE_INFO];
    }
}
