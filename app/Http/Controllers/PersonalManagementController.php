<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePersonalRequest;
use App\Models\CatalogoTipoDocumento;
use App\Models\CategoriaPersonal;
use App\Models\Documento;
use App\Models\Personal;
use App\Models\User;
use App\Notifications\NuevoUsuarioCredenciales;
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

            // 1. Crear Personal
            $personal = $this->createPersonal([
                'nombre_completo' => $validatedData['nombre_completo'],
                'estatus' => $validatedData['estatus'],
                'categoria_id' => $validatedData['categoria_personal_id'],
            ]);

            // 2. Procesar y Guardar Documentos
            $documentosData = [];
            $tiposDocumento = [
                'identificacion_file' => 8,    // INE - Identificación Oficial
                'curp_file' => 9,              // CURP
                'rfc_file' => 10,              // RFC
                'nss_file' => 28,              // NSS - Número de Seguro Social
                'licencia_file' => 7,          // Licencia de Conducir
                'cv_file' => 31                // CV Profesional
            ]; // Mapeo de campos a IDs reales de la BD

            foreach ($tiposDocumento as $requestKey => $tipoId) {
                if ($request->hasFile($requestKey)) {
                    $path = $this->handleDocumentUpload($request->file($requestKey), $personal->id);
                    $documentosData[] = [
                        'tipo_documento_id' => $tipoId,
                        'ruta_archivo' => $path,
                        'descripcion' => "Documento de {$requestKey}",
                    ];
                }
            }

            if (!empty($documentosData)) {
                $this->createPersonalDocuments($personal, $documentosData);
            }

            // 3. Crear Usuario (si se solicita)
            $usuario = null;
            if (!empty($validatedData['crear_usuario'])) {
                $userData = [
                    'email' => $validatedData['email_usuario'],
                    'rol_id' => 3, // Rol por defecto
                    'nombre_completo' => $personal->nombre_completo,
                ];

                // Agregar contraseña personalizada si se especifica
                if (isset($validatedData['password_type']) && $validatedData['password_type'] === 'custom' && !empty($validatedData['password'])) {
                    $userData['password'] = $validatedData['password'];
                }

                $usuario = $this->createPersonalUser($personal, $userData);
            }

            DB::commit();

            $mensaje = 'Personal creado exitosamente';
            if ($usuario) {
                $mensaje .= '. Usuario creado con email: ' . $usuario->email;
                $mensaje .= '. Contraseña temporal: ' . $usuario->getAttribute('password_temp');
            }

            return redirect()->route('personal.show', $personal->id)->with('success', $mensaje);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error al crear el personal: ' . $e->getMessage());
        }
    }

    /**
     * Crear personal con documentos y usuario opcional (API)
     */
    public function create(CreatePersonalRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

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

    private function handleDocumentUpload(\Illuminate\Http\UploadedFile $file, int $personalId): string
    {
        $fileName = time() . '_' . $file->getClientOriginalName();
        return $file->storeAs("personal/{$personalId}/documentos", $fileName, 'private');
    }

    /**
     * Crear usuario asociado al personal
     */
    private function createPersonalUser(Personal $personal, array $data): User
    {
        // Determinar la contraseña a usar
        $password = isset($data['password']) && !empty($data['password']) 
            ? $data['password'] 
            : $this->generateRandomPassword();

        $usuario = User::create([
            'personal_id' => $personal->id,
            'email' => $data['email'],
            'password' => Hash::make($password),
            'rol_id' => $data['rol_id'],
        ]);

        // Enviar email con credenciales al usuario
        try {
            $usuario->notify(new NuevoUsuarioCredenciales($password, $data['nombre_completo']));
        } catch (\Exception $e) {
            // Log del error pero no fallar la creación
            \Log::warning('Error al enviar email de credenciales', [
                'usuario_id' => $usuario->id,
                'email' => $usuario->email,
                'error' => $e->getMessage(),
            ]);
        }

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
