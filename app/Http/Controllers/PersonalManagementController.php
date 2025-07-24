<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePersonalRequest;
use App\Models\CatalogoTipoDocumento;
use App\Models\CategoriaPersonal;
use App\Models\Documento;
use App\Models\Personal;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PersonalManagementController extends Controller
{
    /**
     * Crear personal desde formulario web (sin archivos por ahora)
     */
    public function storeWeb(CreatePersonalRequest $request)
    {
        DB::beginTransaction();

        try {
            $validatedData = $request->validated();
            
            // Mapear campos del formulario web a los esperados por el controlador
            $personalData = [
                'nombre_completo' => $validatedData['nombre_completo'],
                'estatus' => $validatedData['estatus'],
                'categoria_id' => $validatedData['categoria_personal_id'], // El formulario usa categoria_personal_id
            ];

            // Crear el registro de personal
            $personal = $this->createPersonal($personalData);

            // Crear usuario si se solicita
            $usuario = null;
            if (isset($validatedData['crear_usuario']) && $validatedData['crear_usuario']) {
                // Mapear datos del usuario
                $userData = [
                    'email' => $validatedData['email_usuario'], // El formulario usa email_usuario
                    'rol_id' => 3, // Rol Operador por defecto para personal nuevo
                    'crear_usuario' => true
                ];
                $usuario = $this->createPersonalUser($personal, $userData);
            }

            DB::commit();

            // Redirigir con mensaje de éxito
            $mensaje = 'Personal creado exitosamente';
            if ($usuario) {
                $mensaje .= '. Usuario creado con email: ' . $usuario->email;
                // Mostrar contraseña temporal en el mensaje (solo para desarrollo)
                $mensaje .= '. Contraseña temporal: ' . $usuario->getAttribute('password_temp');
            }

            return redirect()->route('personal.show', $personal->id)
                ->with('success', $mensaje);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el personal: ' . $e->getMessage());
        }
    }

    /**
     * Crear personal con documentos y usuario opcional (API)
     */
    public function create(Request $request): JsonResponse
    {
        // TODO: Usar CreatePersonalRequest cuando PHPStan lo resuelva
        $validatedData = $request->all(); // Temporal para PHPStan

        DB::beginTransaction();

        try {
            // Crear el registro de personal
            $personal = $this->createPersonal($validatedData);

            // Procesar documentos si existen
            $documentos = [];
            if (isset($validatedData['documentos']) && ! empty($validatedData['documentos'])) {
                $documentos = $this->createPersonalDocuments($personal, $validatedData['documentos']);
            }

            // Crear usuario si se solicita
            $usuario = null;
            if ($validatedData['crear_usuario']) {
                $usuario = $this->createPersonalUser($personal, $validatedData);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Personal creado exitosamente',
                'data' => [
                    'personal' => $this->formatPersonalResponse($personal),
                    'documentos' => $documentos,
                    'usuario' => $usuario ? $this->formatUserResponse($usuario) : null,
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al crear el personal',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Crear registro de personal
     */
    private function createPersonal(array $data): Personal
    {
        return Personal::create([
            'nombre_completo' => $data['nombre_completo'],
            'estatus' => $data['estatus'],
            'categoria_id' => $data['categoria_id'],
        ]);
    }

    /**
     * Crear documentos asociados al personal
     */
    private function createPersonalDocuments(Personal $personal, array $documentosData): array
    {
        $documentos = [];

        foreach ($documentosData as $docData) {
            $documento = Documento::create([
                'personal_id' => $personal->id,
                'tipo_documento_id' => $docData['tipo_documento_id'],
                'descripcion' => $docData['descripcion'] ?? null,
                'fecha_vencimiento' => $docData['fecha_vencimiento'] ?? null,
                'ruta_archivo' => $docData['ruta_archivo'] ?? null,
                'contenido' => $docData['contenido'] ?? null,
            ]);

            $documentos[] = $this->formatDocumentResponse($documento);
        }

        return $documentos;
    }

    /**
     * Crear usuario asociado al personal
     */
    private function createPersonalUser(Personal $personal, array $data): User
    {
        $password = $this->generateRandomPassword();

        $usuario = User::create([
            'personal_id' => $personal->id,
            'email' => $data['email'],
            'password' => Hash::make($password),
            'rol_id' => $data['rol_id'],
        ]);

        // TODO: Enviar email con credenciales al usuario
        // Esto podría implementarse con un job en cola para evitar bloqueos

        // Agregar la contraseña temporal para retornar al frontend
        // Usamos setAttribute para evitar warnings de PHPStan sobre propiedades dinámicas
        $usuario->setAttribute('password_temp', $password);

        return $usuario;
    }

    /**
     * Generar contraseña aleatoria de 8 caracteres (letras y números)
     */
    private function generateRandomPassword(): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        return substr(str_shuffle($characters), 0, 8);
    }

    /**
     * Formatear respuesta del personal
     */
    private function formatPersonalResponse(Personal $personal): array
    {
        $personal->load('categoria');

        return [
            'id' => $personal->id,
            'nombre_completo' => $personal->nombre_completo,
            'estatus' => $personal->estatus,
            'categoria' => [
                'id' => $personal->categoria->id,
                'nombre' => $personal->categoria->nombre_categoria,
            ],
            'fecha_creacion' => $personal->created_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Formatear respuesta del usuario
     */
    private function formatUserResponse(User $usuario): array
    {
        $usuario->load('rol');

        return [
            'id' => $usuario->id,
            'email' => $usuario->email,
            'rol' => [
                'id' => $usuario->rol->id,
                'nombre' => $usuario->rol->nombre_rol,
            ],
            'password_temporal' => $usuario->getAttribute('password_temp'),
            'fecha_creacion' => $usuario->created_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Formatear respuesta del documento
     */
    private function formatDocumentResponse(Documento $documento): array
    {
        $documento->load('tipoDocumento');

        return [
            'id' => $documento->id,
            'tipo_documento' => [
                'id' => $documento->tipoDocumento->id,
                'nombre' => $documento->tipoDocumento->nombre_tipo_documento,
            ],
            'descripcion' => $documento->descripcion,
            'fecha_vencimiento' => $documento->fecha_vencimiento?->format('Y-m-d'),
            'ruta_archivo' => $documento->ruta_archivo,
            'estado' => $documento->estado,
            'fecha_creacion' => $documento->created_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Obtener datos para el formulario (categorías, tipos de documento, roles)
     */
    public function getFormData(): JsonResponse
    {
        try {
            $categorias = CategoriaPersonal::select('id', 'nombre_categoria')
                ->orderBy('nombre_categoria')
                ->get();

            $tiposDocumento = CatalogoTipoDocumento::select('id', 'nombre_tipo_documento')
                ->orderBy('nombre_tipo_documento')
                ->get();

            $roles = \App\Models\Role::select('id', 'nombre_rol', 'descripcion')
                ->orderBy('nombre_rol')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'categorias' => $categorias,
                    'tipos_documento' => $tiposDocumento,
                    'roles' => $roles,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener datos del formulario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Validar disponibilidad de email
     */
    public function checkEmailAvailability(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $isAvailable = ! User::where('email', $request->email)
            ->whereNull('deleted_at')
            ->exists();

        return response()->json([
            'available' => $isAvailable,
            'message' => $isAvailable ? 'Email disponible' : 'Email ya está en uso',
        ]);
    }
}
