<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePersonalRequest;
use App\Models\CatalogoTipoDocumento;
use App\Models\CategoriaPersonal;
use App\Models\Documento;
use App\Models\Personal;
use App\Models\User;
use App\Notifications\NuevoUsuarioCredenciales;
use App\Services\UsuarioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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

            // 1. PRIMERO: Procesar y Guardar Archivos (antes de crear el personal)
            $documentosData = [];
            $urlsPersonal = []; // URLs que se incluirán directamente en la creación del personal
            $tiposDocumento = [
                'archivo_inicio_laboral' => 17,  // Documento de Inicio Laboral
                'archivo_termino_laboral' => 18, // Documento de Término Laboral
                'identificacion_file' => 9,    // INE - Identificación Oficial
                'curp_file' => 10,             // CURP
                'rfc_file' => 11,              // RFC
                'nss_file' => 12,              // NSS - Número de Seguro Social
                'licencia_file' => 8,          // Licencia de Conducir
                'comprobante_file' => 16,      // Comprobante de Domicilio
                'cv_file' => 1,                // CV Profesional
            ]; // Mapeo de campos a IDs reales de la BD

            // Crear un ID temporal para el personal (usaremos timestamp + random)
            $tempPersonalId = time() . '_' . rand(1000, 9999);

            foreach ($tiposDocumento as $requestKey => $tipoId) {
                if ($request->hasFile($requestKey)) {
                    \Log::info("Processing file: {$requestKey}", [
                        'file_name' => $request->file($requestKey)->getClientOriginalName(),
                        'file_size' => $request->file($requestKey)->getSize(),
                        'temp_personal_id' => $tempPersonalId
                    ]);

                    // Obtener el número de identificación correspondiente según el tipo de documento
                    $descripcion = match ($requestKey) {
                        'archivo_inicio_laboral' => $request->input('fecha_inicio_laboral') ? 'Inicio Laboral: ' . $request->input('fecha_inicio_laboral') : 'Documento de Inicio Laboral',
                        'archivo_termino_laboral' => $request->input('fecha_termino_laboral') ? 'Término Laboral: ' . $request->input('fecha_termino_laboral') : 'Documento de Término Laboral',
                        'identificacion_file' => $request->input('ine'),
                        'curp_file' => $request->input('curp_numero'),
                        'rfc_file' => $request->input('rfc'),
                        'nss_file' => $request->input('nss'),
                        'licencia_file' => $request->input('no_licencia'),
                        'comprobante_file' => $request->input('direccion'),
                        'cv_file' => 'Curriculum Vitae',
                        default => null
                    };

                    // Usar ID temporal para crear la estructura de carpetas
                    $path = $this->handleDocumentUpload($request->file($requestKey), $tempPersonalId, $requestKey, $descripcion);
                    \Log::info("File uploaded to: {$path}");

                    $documentosData[] = [
                        'tipo_documento_id' => $tipoId,
                        'ruta_archivo' => $path,
                        'descripcion' => $descripcion,
                        'tipo_archivo' => $requestKey, // Agregar tipo de archivo para usar en renombrado
                    ];

                    // Mapear URLs para incluir en la creación del personal (solo ruta relativa)
                    $urlField = match ($requestKey) {
                        'archivo_inicio_laboral' => 'url_inicio_laboral',
                        'archivo_termino_laboral' => 'url_termino_laboral',
                        'identificacion_file' => 'url_ine',
                        'curp_file' => 'url_curp',
                        'rfc_file' => 'url_rfc',
                        'nss_file' => 'url_nss',
                        'licencia_file' => 'url_licencia',
                        'comprobante_file' => 'url_comprobante_domicilio',
                        'cv_file' => 'url_cv',
                        default => null
                    };

                    if ($urlField) {
                        $urlsPersonal[$urlField] = $path;
                    }

                    \Log::info("Document data prepared", [
                        'tipo_documento_id' => $tipoId,
                        'ruta_archivo' => $path,
                        'descripcion' => $descripcion,
                        'url_field' => $urlField
                    ]);
                }
            }

            // 2. SEGUNDO: Crear Personal con las URLs ya incluidas
            $personalData = [
                'nombre_completo' => $validatedData['nombre_completo'],
                'estatus' => 'activo',
                'categoria_id' => $validatedData['categoria_personal_id'],
                'ine' => $validatedData['ine'] ?? null,
                'curp_numero' => $validatedData['curp_numero'] ?? null,
                'rfc' => $validatedData['rfc'] ?? null,
                'nss' => $validatedData['nss'] ?? null,
                'no_licencia' => $validatedData['no_licencia'] ?? null,
                'direccion' => $validatedData['direccion'] ?? null,
                'cuenta_bancaria' => $validatedData['cuenta_bancaria'] ?? null,
                'fecha_inicio_laboral' => $validatedData['fecha_inicio_laboral'] ?? null,
                'fecha_termino_laboral' => $validatedData['fecha_termino_laboral'] ?? null,
            ];

            // Agregar las URLs de los archivos al personal
            $personalData = array_merge($personalData, $urlsPersonal);

            $personal = $this->createPersonal($personalData);

            // 3. TERCERO: Actualizar las rutas de archivos con el ID real del personal
            if (!empty($documentosData)) {
                // Renombrar archivos con el ID real del personal
                $documentosDataUpdated = [];
                foreach ($documentosData as $docData) {
                    $oldPath = $docData['ruta_archivo'];
                    
                    // Simplemente reemplazar el ID temporal con el ID real en el nombre del archivo
                    $newPath = str_replace("personal/{$tempPersonalId}/", "personal/{$personal->id}/", $oldPath);
                    $newPath = str_replace($tempPersonalId, $personal->id, $newPath);

                    // Mover archivo físico con nuevo nombre
                    Storage::disk('public')->move($oldPath, $newPath);

                    // Actualizar URL en personal (solo ruta relativa)
                    foreach ($urlsPersonal as $urlField => $urlOldPath) {
                        if ($urlOldPath === $docData['ruta_archivo']) {
                            $personal->update([$urlField => $newPath]);
                            break;
                        }
                    }

                    $documentosDataUpdated[] = [
                        'tipo_documento_id' => $docData['tipo_documento_id'],
                        'ruta_archivo' => $newPath,
                        'descripcion' => $docData['descripcion'],
                    ];
                }

                \Log::info("Creating documents with real personal ID", ['count' => count($documentosDataUpdated), 'personal_id' => $personal->id]);
                $this->createPersonalDocuments($personal, $documentosDataUpdated);
                \Log::info("Documents created successfully");
            } else {
                \Log::info("No documents to create");
            }

            // 3. Crear Usuario (si se solicita)
            $usuario = null;
            $mensajeUsuario = '';

            // Añadir logs para debugging
            \Log::info('Datos raw del request (PersonalManagementController):', [
                'all_data' => $request->all(),
                'has_crear_usuario' => $request->has('crear_usuario'),
                'crear_usuario_value' => $request->get('crear_usuario'),
            ]);
            \Log::info('Datos validados (PersonalManagementController):', ['validated' => $validatedData]);

            if (!empty($validatedData['crear_usuario']) && $validatedData['crear_usuario']) {
                \Log::info('Creando usuario para personal', [
                    'personal_id' => $personal->id,
                    'email_usuario' => $validatedData['email_usuario'] ?? 'No proporcionado',
                    'rol_usuario' => $validatedData['rol_usuario'] ?? 'No proporcionado',
                    'tipo_password' => $validatedData['tipo_password'] ?? 'No proporcionado'
                ]);

                $usuarioService = new UsuarioService();

                try {
                    $datosUsuario = [
                        'email' => $validatedData['email_usuario'],
                        'rol_id' => $validatedData['rol_usuario'],
                        'tipo_password' => $validatedData['tipo_password'], // Siempre será 'aleatoria'
                    ];

                    $resultado = $usuarioService->crearUsuarioParaPersonal($personal, $datosUsuario);
                    $usuario = $resultado['usuario'];
                    $passwordGenerada = $resultado['password'];

                    \Log::info('Usuario creado exitosamente', [
                        'usuario_id' => $usuario->id,
                        'password_generated' => $passwordGenerada
                    ]);

                    // Incluir la contraseña en el mensaje, independientemente del email
                    $mensajeUsuario = '. Usuario creado exitosamente. <div class="mt-3 p-4 bg-blue-50 border-l-4 border-blue-400 rounded-md"><div class="flex items-center"><div class="flex-shrink-0"><svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg></div><div class="ml-3"><h3 class="text-sm font-medium text-blue-800">Contraseña generada:</h3><p class="mt-1 text-sm text-blue-700 font-mono"><strong>' . $passwordGenerada . '</strong></p><p class="mt-1 text-xs text-blue-600">Guarde esta contraseña, también se ha enviado por email.</p></div></div></div>';
                } catch (\Exception $e) {
                    // Si falla la creación del usuario, continuar pero informar
                    \Log::error('Error al crear usuario', ['error' => $e->getMessage()]);
                    $mensajeUsuario = '. Advertencia: ' . $e->getMessage();
                }
            }

            DB::commit();

            $mensaje = 'Personal creado exitosamente' . $mensajeUsuario;

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
            'estatus' => 'activo',
            'categoria_id' => $data['categoria_id'],
            'ine' => $data['ine'] ?? null,
            'url_ine' => $data['url_ine'] ?? null,
            'curp_numero' => $data['curp_numero'] ?? null,
            'url_curp' => $data['url_curp'] ?? null,
            'rfc' => $data['rfc'] ?? null,
            'url_rfc' => $data['url_rfc'] ?? null,
            'nss' => $data['nss'] ?? null,
            'url_nss' => $data['url_nss'] ?? null,
            'no_licencia' => $data['no_licencia'] ?? null,
            'url_licencia' => $data['url_licencia'] ?? null,
            'direccion' => $data['direccion'] ?? null,
            'url_comprobante_domicilio' => $data['url_comprobante_domicilio'] ?? null,
            'cuenta_bancaria' => $data['cuenta_bancaria'] ?? null,
            'fecha_inicio_laboral' => $data['fecha_inicio_laboral'] ?? null,
            'url_inicio_laboral' => $data['url_inicio_laboral'] ?? null,
            'fecha_termino_laboral' => $data['fecha_termino_laboral'] ?? null,
            'url_termino_laboral' => $data['url_termino_laboral'] ?? null,
        ]);
    }

    /**
     * Crear documentos asociados al personal
     */
    private function createPersonalDocuments(Personal $personal, array $documentosData): array
    {
        $documentos = [];

        foreach ($documentosData as $docData) {
            \Log::info("Creating individual document", [
                'personal_id' => $personal->id,
                'tipo_documento_id' => $docData['tipo_documento_id'],
                'descripcion' => $docData['descripcion'] ?? null,
                'ruta_archivo' => $docData['ruta_archivo'] ?? null
            ]);

            $documento = Documento::create([
                'personal_id' => $personal->id,
                'tipo_documento_id' => $docData['tipo_documento_id'],
                'descripcion' => $docData['descripcion'] ?? null,
                'fecha_vencimiento' => $docData['fecha_vencimiento'] ?? null,
                'ruta_archivo' => $docData['ruta_archivo'] ?? null,
                'contenido' => $docData['contenido'] ?? null,
            ]);

            \Log::info("Document created with ID: {$documento->id}");

            $documentos[] = $this->formatDocumentResponse($documento);
        }

        return $documentos;
    }

    private function handleDocumentUpload(\Illuminate\Http\UploadedFile $file, int|string $personalId, string $fileType = null, string $descripcion = null): string
    {
        // Generar nombre de archivo con formato: ID_TIPODOCUMENTO_DESCRIPCION.extension
        $extension = $file->getClientOriginalExtension();
        
        // Mapear tipos de archivo a nombres legibles
        $tipoDocumentoNombres = [
            'identificacion_file' => 'INE',
            'curp_file' => 'CURP',
            'rfc_file' => 'RFC',
            'nss_file' => 'NSS',
            'licencia_file' => 'LICENCIA',
            'comprobante_file' => 'COMPROBANTE',
            'cv_file' => 'CV'
        ];
        
        $tipoNombre = $tipoDocumentoNombres[$fileType] ?? 'DOCUMENTO';
        
        // Limpiar la descripción: solo caracteres alfanuméricos, máximo 15 caracteres
        $descripcionLimpia = '';
        if ($descripcion) {
            $descripcionLimpia = preg_replace('/[^A-Za-z0-9]/', '', $descripcion);
            $descripcionLimpia = substr($descripcionLimpia, 0, 15);
        }
        
        // Construir nombre: ID_TIPO_DESCRIPCION.extension
        $nombreArchivo = $personalId . '_' . $tipoNombre;
        if (!empty($descripcionLimpia)) {
            $nombreArchivo .= '_' . $descripcionLimpia;
        }
        $nombreArchivo .= '.' . $extension;
        
        \Log::info("Generated filename: {$nombreArchivo} for fileType: {$fileType}, personalId: {$personalId}");
        
        return $file->storeAs("personal/{$personalId}/documentos", $nombreArchivo, 'public');
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

        // Envío de email automático deshabilitado por solicitud del usuario
        // try {
        //     $usuario->notify(new NuevoUsuarioCredenciales($password, $data['nombre_completo']));
        // } catch (\Exception $e) {
        //     // Log del error pero no fallar la creación
        //     \Log::warning('Error al enviar email de credenciales', [
        //         'usuario_id' => $usuario->id,
        //         'email' => $usuario->email,
        //         'error' => $e->getMessage(),
        //     ]);
        // }

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
