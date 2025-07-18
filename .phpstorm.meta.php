<?php

namespace PHPSTORM_META {

    expectedArguments(\App\Models\User::hasPermission(), 0, argumentsSet('permissions'));
    expectedArguments(\App\Models\User::hasRole(), 0, argumentsSet('roles'));
    expectedArguments(\App\Models\Role::hasPermission(), 0, argumentsSet('permissions'));

    argumentsSet('permissions',
        'ver_vehiculos',
        'crear_vehiculos',
        'editar_vehiculos',
        'eliminar_vehiculos',
        'restaurar_vehiculos',
        'ver_usuarios',
        'crear_usuarios',
        'editar_usuarios',
        'eliminar_usuarios',
        'ver_personal',
        'crear_personal',
        'editar_personal',
        'eliminar_personal',
        'ver_logs'
    );

    argumentsSet('roles',
        'Admin',
        'Supervisor',
        'Operador'
    );

}
