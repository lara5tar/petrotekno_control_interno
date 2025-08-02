<?php

namespace App\Services;

use App\Models\User;
use App\Models\Personal;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UsuarioService
{
    /**
     * Crear un usuario del sistema para un personal
     * Retorna un array con el usuario y la contraseña generada
     */
    public function crearUsuarioParaPersonal(Personal $personal, array $datosUsuario): array
    {
        // Validar que el personal no tenga ya un usuario
        if ($personal->usuario) {
            throw new \Exception('Este personal ya tiene un usuario asociado.');
        }

        // Validar que el email no esté en uso
        if (User::where('email', $datosUsuario['email'])->exists()) {
            throw new \Exception('El email ya está registrado en el sistema.');
        }

        // Validar que el rol exista
        $rol = Role::findOrFail($datosUsuario['rol_id']);

        // Generar contraseña según el tipo (ahora solo aleatoria)
        $password = $this->generarPasswordAleatoria();

        // Crear el usuario
        $usuario = User::create([
            'personal_id' => $personal->id,
            'email' => $datosUsuario['email'],
            'password' => Hash::make($password),
            'rol_id' => $datosUsuario['rol_id'],
        ]);

        // Envío de email automático deshabilitado por solicitud del usuario
        // $this->enviarPasswordPorEmail($usuario, $password, $personal);

        return [
            'usuario' => $usuario,
            'password' => $password
        ];
    }

    /**
     * Generar una contraseña aleatoria segura
     */
    private function generarPasswordAleatoria(): string
    {
        // Generar contraseña de 12 caracteres con mayúsculas, minúsculas, números y símbolos
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $symbols = '!@#$%&*';

        $password = '';

        // Asegurar al menos uno de cada tipo
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];

        // Completar con caracteres aleatorios
        $allChars = $uppercase . $lowercase . $numbers . $symbols;
        for ($i = 4; $i < 12; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        // Mezclar los caracteres
        return str_shuffle($password);
    }

    /**
     * Enviar contraseña por email al usuario
     */
    private function enviarPasswordPorEmail(User $usuario, string $password, Personal $personal): void
    {
        try {
            Mail::send('emails.nuevo-usuario', [
                'nombre' => $personal->nombre_completo,
                'email' => $usuario->email,
                'password' => $password,
                'rol' => $usuario->rol->nombre_rol,
                'sistema' => config('app.name'),
                'url_login' => route('login'),
            ], function ($message) use ($usuario, $personal) {
                $message->to($usuario->email, $personal->nombre_completo)
                    ->subject('Acceso al Sistema - ' . config('app.name'));
            });
        } catch (\Exception $e) {
            // Log del error pero no fallar la creación del usuario
            \Log::error('Error al enviar email de contraseña', [
                'usuario_id' => $usuario->id,
                'email' => $usuario->email,
                'error' => $e->getMessage()
            ]);

            // No lanzar excepción, solo loggear el error
            // La contraseña se mostrará en pantalla independientemente del email
        }
    }

    /**
     * Validar datos de usuario antes de la creación
     */
    public function validarDatosUsuario(array $datos): array
    {
        $errores = [];

        // Validar email
        if (empty($datos['email'])) {
            $errores[] = 'El email es obligatorio';
        } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El email no tiene un formato válido';
        } elseif (User::where('email', $datos['email'])->exists()) {
            $errores[] = 'El email ya está registrado en el sistema';
        }

        // Validar rol
        if (empty($datos['rol_id'])) {
            $errores[] = 'Debe seleccionar un rol';
        } elseif (!Role::where('id', $datos['rol_id'])->exists()) {
            $errores[] = 'El rol seleccionado no es válido';
        }

        // Validar tipo de contraseña (solo aleatoria permitida)
        if (empty($datos['tipo_password']) || $datos['tipo_password'] !== 'aleatoria') {
            $errores[] = 'Solo se permite contraseña aleatoria';
        }

        return $errores;
    }
}
