<?php

// use App\Http\Controllers\AuthController; // Comentado - AuthController no existe

use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\KilometrajeController;
use App\Http\Controllers\MantenimientoController;
use App\Http\Controllers\ObraController;
// use App\Http\Controllers\PermissionController; // Comentado - PermissionController no existe
use App\Http\Controllers\PersonalController;
// use App\Http\Controllers\RoleController; // Comentado - RoleController no existe
// use App\Http\Controllers\UserController; // Comentado - UserController no existe
use App\Http\Controllers\VehiculoController;
use App\Models\CatalogoEstatus;
use App\Models\LogAccion;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rutas de verificación para testing con Playwright (sin autenticación para testing)
Route::prefix('verificacion')->group(function () {
    Route::post('/verificar-obra', [\App\Http\Controllers\Api\VerificacionController::class, 'verificarObra']);
});

// Rutas públicas de autenticación - COMENTADAS (AuthController no existe)
// Route::prefix('auth')->group(function () {
//     Route::post('/login', [AuthController::class, 'login']);
//     Route::post('/register', [AuthController::class, 'register']); // Para futuro uso
// });

// Rutas protegidas por autenticación
Route::middleware('auth:sanctum')->group(function () {

    // Rutas de autenticación del usuario logueado - COMENTADAS (AuthController no existe)
    // Route::prefix('auth')->group(function () {
    //     Route::post('/logout', [AuthController::class, 'logout']);
    //     Route::get('/me', [AuthController::class, 'me']);
    //     Route::put('/change-password', [AuthController::class, 'changePassword']);
    // });

    // Rutas de usuarios - requieren permisos específicos - COMENTADAS (UserController no existe)
    /*
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])
            ->middleware('permission:ver_usuarios');

        Route::post('/', [UserController::class, 'store'])
            ->middleware('permission:crear_usuarios');

        Route::get('/{id}', [UserController::class, 'show'])
            ->middleware('permission:ver_usuarios');

        Route::put('/{id}', [UserController::class, 'update'])
            ->middleware('permission:editar_usuarios');

        Route::delete('/{id}', [UserController::class, 'destroy'])
            ->middleware('permission:eliminar_usuarios');

        Route::post('/{id}/restore', [UserController::class, 'restore'])
            ->middleware('permission:editar_usuarios');
    });
    */

    // Rutas de roles - solo administradores - COMENTADAS (RoleController no existe)
    /*
    Route::prefix('roles')->middleware('role:Admin')->group(function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::post('/', [RoleController::class, 'store']);
        Route::get('/{id}', [RoleController::class, 'show']);
        Route::put('/{id}', [RoleController::class, 'update']);
        Route::delete('/{id}', [RoleController::class, 'destroy']);
        Route::post('/{roleId}/permissions/{permissionId}', [RoleController::class, 'attachPermission']);
        Route::delete('/{roleId}/permissions/{permissionId}', [RoleController::class, 'detachPermission']);
    });
    */

    // Rutas de permisos - solo administradores - COMENTADAS (PermissionController no existe)
    /*
    Route::prefix('permissions')->middleware('role:Admin')->group(function () {
        Route::get('/', [PermissionController::class, 'index']);
        Route::post('/', [PermissionController::class, 'store']);
        Route::get('/{id}', [PermissionController::class, 'show']);
        Route::put('/{id}', [PermissionController::class, 'update']);
        Route::delete('/{id}', [PermissionController::class, 'destroy']);
    });
    */

    // Rutas de personal
    Route::prefix('personal')->group(function () {
        Route::get('/', [PersonalController::class, 'index'])
            ->middleware('permission:ver_personal');

        Route::get('/create', [PersonalController::class, 'create'])
            ->middleware('permission:crear_personal');

        Route::post('/', [PersonalController::class, 'store'])
            ->middleware('permission:crear_personal');

        Route::get('/{id}', [PersonalController::class, 'show'])
            ->middleware('permission:ver_personal');

        Route::get('/{id}/edit', [PersonalController::class, 'edit'])
            ->middleware('permission:editar_personal');

        Route::put('/{id}', [PersonalController::class, 'update'])
            ->middleware('permission:editar_personal');

        Route::delete('/{id}', [PersonalController::class, 'destroy'])
            ->middleware('permission:eliminar_personal');
    });

    // Rutas de vehículos
    Route::prefix('vehiculos')->group(function () {
        Route::get('/', [VehiculoController::class, 'index'])
            ->middleware('permission:ver_vehiculos');

        Route::get('/create', [VehiculoController::class, 'create'])
            ->middleware('permission:crear_vehiculos');

        Route::post('/', [VehiculoController::class, 'store'])
            ->middleware('permission:crear_vehiculos');

        Route::get('/estatus', [VehiculoController::class, 'estatusOptions']);

        Route::get('/{id}', [VehiculoController::class, 'show'])
            ->middleware('permission:ver_vehiculos');

        Route::get('/{id}/edit', [VehiculoController::class, 'edit'])
            ->middleware('permission:editar_vehiculos');

        Route::put('/{id}', [VehiculoController::class, 'update'])
            ->middleware('permission:editar_vehiculos');

        Route::delete('/{id}', [VehiculoController::class, 'destroy'])
            ->middleware('permission:eliminar_vehiculos');

        Route::post('/{id}/restore', [VehiculoController::class, 'restore'])
            ->middleware('permission:restaurar_vehiculos');
    });

    // Rutas de obras
    Route::prefix('obras')->group(function () {
        Route::get('/', [ObraController::class, 'index'])
            ->middleware('permission:ver_obras');

        Route::get('/create', [ObraController::class, 'create'])
            ->middleware('permission:crear_obras');

        Route::post('/', [ObraController::class, 'store'])
            ->middleware('permission:crear_obras');

        Route::get('/estatus-options', [ObraController::class, 'status'])
            ->middleware('permission:ver_obras');

        Route::get('/{id}', [ObraController::class, 'show'])
            ->middleware('permission:ver_obras');

        Route::get('/{id}/edit', [ObraController::class, 'edit'])
            ->middleware('permission:actualizar_obras');

        Route::put('/{id}', [ObraController::class, 'update'])
            ->middleware('permission:actualizar_obras');

        Route::delete('/{id}', [ObraController::class, 'destroy'])
            ->middleware('permission:eliminar_obras');

        Route::post('/{id}/restore', [ObraController::class, 'restore'])
            ->middleware('permission:restaurar_obras');
    });

    // Rutas de documentos
    Route::prefix('documentos')->group(function () {
        Route::get('/', [DocumentoController::class, 'index'])
            ->middleware('permission:ver_documentos');

        Route::get('/create', [DocumentoController::class, 'create'])
            ->middleware('permission:crear_documentos');

        Route::post('/', [DocumentoController::class, 'store'])
            ->middleware('permission:crear_documentos');

        Route::get('/proximos-a-vencer', [DocumentoController::class, 'proximosAVencer'])
            ->middleware('permission:ver_documentos');

        Route::get('/vencidos', [DocumentoController::class, 'vencidos'])
            ->middleware('permission:ver_documentos');

        Route::get('/{documento}', [DocumentoController::class, 'show'])
            ->middleware('permission:ver_documentos');

        Route::get('/{documento}/edit', [DocumentoController::class, 'edit'])
            ->middleware('permission:editar_documentos');

        Route::put('/{documento}', [DocumentoController::class, 'update'])
            ->middleware('permission:editar_documentos');

        Route::delete('/{documento}', [DocumentoController::class, 'destroy'])
            ->middleware('permission:eliminar_documentos');
    });

    // Rutas específicas para documentos de vehículos
    Route::prefix('vehiculos/{vehiculo}/documentos')->group(function () {
        Route::post('/', [DocumentoController::class, 'storeForVehiculo'])
            ->middleware('permission:crear_documentos');
        
        Route::get('/', [DocumentoController::class, 'getByVehiculo'])
            ->middleware('permission:ver_documentos');
    });

    // Rutas de catálogo de tipos de documento (Comentado - Controlador no existe)
    /*
    Route::prefix('catalogo-tipos-documento')->group(function () {
        Route::get('/', [CatalogoTipoDocumentoController::class, 'index'])
            ->middleware('permission:ver_catalogos');

        Route::post('/', [CatalogoTipoDocumentoController::class, 'store'])
            ->middleware('permission:crear_catalogos');

        Route::get('/{id}', [CatalogoTipoDocumentoController::class, 'show'])
            ->middleware('permission:ver_catalogos');

        Route::put('/{id}', [CatalogoTipoDocumentoController::class, 'update'])
            ->middleware('permission:editar_catalogos');

        Route::delete('/{id}', [CatalogoTipoDocumentoController::class, 'destroy'])
            ->middleware('permission:eliminar_catalogos');
    });
    */

    // Rutas de mantenimientos
    Route::prefix('mantenimientos')->group(function () {
        Route::get('/', [MantenimientoController::class, 'index'])
            ->middleware('permission:ver_mantenimientos');

        Route::get('/create', [MantenimientoController::class, 'create'])
            ->middleware('permission:crear_mantenimientos');

        Route::post('/', [MantenimientoController::class, 'store'])
            ->middleware('permission:crear_mantenimientos');

        Route::get('/proximos-por-kilometraje', [MantenimientoController::class, 'proximosPorKilometraje'])
            ->middleware('permission:ver_mantenimientos');

        Route::get('/estadisticas', [MantenimientoController::class, 'estadisticas'])
            ->middleware('permission:ver_mantenimientos');

        Route::get('/{id}', [MantenimientoController::class, 'show'])
            ->middleware('permission:ver_mantenimientos');

        Route::get('/{id}/edit', [MantenimientoController::class, 'edit'])
            ->middleware('permission:actualizar_mantenimientos');

        Route::put('/{id}', [MantenimientoController::class, 'update'])
            ->middleware('permission:actualizar_mantenimientos');

        Route::delete('/{id}', [MantenimientoController::class, 'destroy'])
            ->middleware('permission:eliminar_mantenimientos');

        Route::post('/{id}/restore', [MantenimientoController::class, 'restore'])
            ->middleware('permission:restaurar_mantenimientos');
    });

    // Rutas de kilometrajes
    Route::prefix('kilometrajes')->group(function () {
        Route::get('/', [KilometrajeController::class, 'index'])
            ->middleware('permission:ver_kilometrajes');

        Route::post('/', [KilometrajeController::class, 'store'])
            ->middleware('permission:crear_kilometrajes');

        Route::get('/create', [KilometrajeController::class, 'create'])
            ->middleware('permission:crear_kilometrajes');

        Route::get('/vehiculo/{vehiculoId}/historial', [KilometrajeController::class, 'historialPorVehiculo'])
            ->middleware('permission:ver_kilometrajes');

        Route::get('/{kilometraje}', [KilometrajeController::class, 'show'])
            ->middleware('permission:ver_kilometrajes');

        Route::get('/{kilometraje}/edit', [KilometrajeController::class, 'edit'])
            ->middleware('permission:editar_kilometrajes');

        Route::put('/{kilometraje}', [KilometrajeController::class, 'update'])
            ->middleware('permission:editar_kilometrajes');

        Route::delete('/{kilometraje}', [KilometrajeController::class, 'destroy'])
            ->middleware('permission:eliminar_kilometrajes');
    });

    // Rutas de configuración de alertas de mantenimiento
    Route::prefix('configuracion-alertas')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\ConfiguracionAlertasController::class, 'index'])
            ->middleware('permission:ver_configuraciones');

        Route::put('/general', [App\Http\Controllers\Api\ConfiguracionAlertasController::class, 'updateGeneral'])
            ->middleware('permission:editar_configuraciones');

        Route::put('/horarios', [App\Http\Controllers\Api\ConfiguracionAlertasController::class, 'updateHorarios'])
            ->middleware('permission:editar_configuraciones');

        Route::put('/destinatarios', [App\Http\Controllers\Api\ConfiguracionAlertasController::class, 'updateDestinatarios'])
            ->middleware('permission:editar_configuraciones');

        Route::get('/resumen-alertas', [App\Http\Controllers\Api\ConfiguracionAlertasController::class, 'resumenAlertas'])
            ->middleware('permission:ver_alertas_mantenimiento');

        Route::post('/probar-envio', [App\Http\Controllers\Api\ConfiguracionAlertasController::class, 'probarEnvio'])
            ->middleware('permission:gestionar_alertas_mantenimiento');
    });

    // Rutas de consulta general (sin restricciones especiales)
    Route::prefix('data')->group(function () {
        Route::get('/categorias-personal', function () {
            return response()->json([
                'success' => true,
                'data' => \App\Models\CategoriaPersonal::all(),
            ]);
        });

        Route::get('/estatus-vehiculos', function () {
            return response()->json([
                'success' => true,
                'data' => CatalogoEstatus::activos()->get(),
            ]);
        });

        Route::get('/roles', function () {
            return response()->json([
                'success' => true,
                'data' => Role::all(),
            ]);
        });

        Route::get('/permissions', function () {
            return response()->json([
                'success' => true,
                'data' => Permission::all(),
            ]);
        })->middleware('role:Admin');
    });

    // Ruta para obtener logs del sistema - solo administradores
    Route::get('/logs', function (Request $request) {
        $logs = LogAccion::with('usuario')
            ->orderBy('fecha_hora', 'desc')
            ->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    })->middleware('permission:ver_logs');
});
