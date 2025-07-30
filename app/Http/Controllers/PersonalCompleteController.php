<?php

namespace App\Http\Controllers;

use App\Models\CategoriaPersonal;
use App\Models\CatalogoTipoDocumento;
use App\Models\Documento;
use App\Models\LogAccion;
use App\Models\Personal;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PersonalCompleteController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:crear_personal');
    }

    /**
     * Mostrar formulario para crear personal completo con documentos y usuario
     */
    public function create(Request $request): JsonResponse|View|RedirectResponse
    {
        // Verificar permisos
        if (!$request->user()->hasPermission('crear_personal')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "No tienes el permiso 'crear_personal' necesario para acceder a este recurso",
                ], 403);
            }

            return redirect()->back()->with('error', 'No tienes permisos para acceder a esta sección');
        }

        try {
            // Obtener datos necesarios para el formulario
            $categorias = CategoriaPersonal::select('id', 'nombre_categoria')
                ->orderBy('nombre_categoria')
                ->get();

            $tiposDocumento = CatalogoTipoDocumento::select('id', 'nombre_tipo_documento', 'requiere_vencimiento')
                ->whereIn('id', [7, 8, 9, 10, 11, 15, 16]) // IDs específicos para personal
                ->orderBy('nombre_tipo_documento')
                ->get();

            $roles = Role::select('id', 'nombre_rol')
                ->where('nombre_rol', '!=', 'super_admin') // Excluir super admin
                ->orderBy('nombre_rol')
                ->get();

            // Respuesta híbrida
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'categorias' => $categorias,
                        'tipos_documento' => $tiposDocumento,
                        'roles' => $roles,
                    ],
                ]);
            }

            return view('personal.create-complete', compact('categorias', 'tiposDocumento', 'roles'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al cargar el formulario',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }
    }

    /**
     * Crear personal completo con documentos y usuario
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        // Verificar permisos
        if (!$request->user()->hasPermission('crear_personal')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "No tienes el permiso 'crear_personal' necesario para acceder a este recurso",
                ], 403);
            }

            return redirect()->back()->with('error', 'No tienes permisos para acceder a esta sección');
        }

        // Validación de datos
        try {
            $validated = $request->validate([
                // Datos del personal
                'nombre_completo' => 'required|string|max:255',
                'estatus' => ['required', Rule::in(Personal::ESTATUS_VALIDOS)],
                'categoria_personal_id' => 'required|exists:categorias_personal,id',
                
                // Documentos (archivos)
                'documento_identificacion' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png',
                'documento_curp' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png',
                'documento_rfc' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png',
                'documento_nss' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png',
                'documento_licencia' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png',
                'documento_cv' => 'nullable|file|max:10240|mimes:pdf,doc,docx',
                'documento_domicilio' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png',
                
                // Descripciones de documentos
                'descripcion_identificacion' => 'nullable|string|max:255',
                'descripcion_curp' => 'nullable|string|max:255',
                'descripcion_rfc' => 'nullable|string|max:255',
                'descripcion_nss' => 'nullable|string|max:255',
                'descripcion_licencia' => 'nullable|string|max:255',
                'descripcion_cv' => 'nullable|string|max:255',
                'descripcion_domicilio' => 'nullable|string|max:255',
                
                // Fechas de vencimiento de documentos
                'fecha_vencimiento_identificacion' => 'nullable|date|after_or_equal:today',
                'fecha_vencimiento_licencia' => 'nullable|date|after_or_equal:today',
                
                // Datos del usuario (opcional)
                'crear_usuario' => 'nullable|boolean',
                'email' => 'nullable|required_if:crear_usuario,true|email|unique:users,email',
                'password' => 'nullable|required_if:crear_usuario,true|string|min:8|confirmed',
                'rol_id' => 'nullable|required_if:crear_usuario,true|exists:roles,id',
            ]);
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $e->errors(),
                ], 422);
            }

            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        // Iniciar transacción de base de datos
        DB::beginTransaction();
        
        try {
            $archivosSubidos = [];
            
            // PASO 1: Subir archivos adjuntos
            $documentosConfig = [
                'documento_identificacion' => ['tipo_id' => 8, 'nombre' => 'Identificación Oficial', 'vencimiento' => 'fecha_vencimiento_identificacion', 'descripcion_campo' => 'descripcion_identificacion'],
                'documento_curp' => ['tipo_id' => 9, 'nombre' => 'CURP', 'vencimiento' => null, 'descripcion_campo' => 'descripcion_curp'],
                'documento_rfc' => ['tipo_id' => 10, 'nombre' => 'RFC', 'vencimiento' => null, 'descripcion_campo' => 'descripcion_rfc'],
                'documento_nss' => ['tipo_id' => 11, 'nombre' => 'NSS', 'vencimiento' => null, 'descripcion_campo' => 'descripcion_nss'],
                'documento_licencia' => ['tipo_id' => 7, 'nombre' => 'Licencia de Conducir', 'vencimiento' => 'fecha_vencimiento_licencia', 'descripcion_campo' => 'descripcion_licencia'],
                'documento_cv' => ['tipo_id' => 15, 'nombre' => 'CV Profesional', 'vencimiento' => null, 'descripcion_campo' => 'descripcion_cv'],
                'documento_domicilio' => ['tipo_id' => 16, 'nombre' => 'Comprobante de Domicilio', 'vencimiento' => null, 'descripcion_campo' => 'descripcion_domicilio'],
            ];

            foreach ($documentosConfig as $campo => $config) {
                if ($request->hasFile($campo)) {
                    $archivo = $request->file($campo);
                    $nombreArchivo = time() . '_' . Str::slug($config['nombre'], '_') . '_' . Str::random(8) . '.' . $archivo->getClientOriginalExtension();
                    $rutaArchivo = $archivo->storeAs('documentos/personal', $nombreArchivo, 'public');
                    
                    $archivosSubidos[$campo] = [
                        'ruta' => $rutaArchivo,
                        'config' => $config,
                    ];
                }
            }

            // PASO 2: Crear el personal
            $personal = Personal::create([
                'nombre_completo' => $validated['nombre_completo'],
                'estatus' => $validated['estatus'],
                'categoria_id' => $validated['categoria_personal_id'],
                'ine' => $validated['ine'] ?? null,
                'url_ine' => $validated['url_ine'] ?? null,
                'curp_numero' => $validated['curp_numero'] ?? null,
                'url_curp' => $validated['url_curp'] ?? null,
                'rfc' => $validated['rfc'] ?? null,
                'url_rfc' => $validated['url_rfc'] ?? null,
                'nss' => $validated['nss'] ?? null,
                'url_nss' => $validated['url_nss'] ?? null,
                'no_licencia' => $validated['no_licencia'] ?? null,
                'url_licencia' => $validated['url_licencia'] ?? null,
                'direccion' => $validated['direccion'] ?? null,
                'url_comprobante_domicilio' => $validated['url_comprobante_domicilio'] ?? null,
            ]);

            // PASO 3: Crear documentos asociados al personal y actualizar URLs
            $urlsToUpdate = [];
            foreach ($archivosSubidos as $campo => $archivoData) {
                $config = $archivoData['config'];
                $fechaVencimiento = null;
                
                // Verificar si tiene fecha de vencimiento
                if ($config['vencimiento'] && isset($validated[$config['vencimiento']])) {
                    $fechaVencimiento = $validated[$config['vencimiento']];
                }

                // Obtener descripción personalizada o usar la por defecto
                $descripcionPersonalizada = null;
                if ($config['descripcion_campo'] && isset($validated[$config['descripcion_campo']])) {
                    $descripcionPersonalizada = $validated[$config['descripcion_campo']];
                }
                
                $descripcionFinal = $descripcionPersonalizada 
                    ? $descripcionPersonalizada 
                    : $config['nombre'] . ' - ' . $personal->nombre_completo;

                Documento::create([
                    'tipo_documento_id' => $config['tipo_id'],
                    'descripcion' => $descripcionFinal,
                    'ruta_archivo' => $archivoData['ruta'],
                    'fecha_vencimiento' => $fechaVencimiento,
                    'personal_id' => $personal->id,
                ]);
                
                // Mapear al campo correspondiente en la tabla personal (solo ruta relativa)
                $urlField = match($campo) {
                    'documento_ine' => 'url_ine',
                    'documento_curp' => 'url_curp',
                    'documento_rfc' => 'url_rfc',
                    'documento_nss' => 'url_nss',
                    'documento_licencia' => 'url_licencia',
                    'documento_domicilio' => 'url_comprobante_domicilio',
                    default => null
                };
                
                if ($urlField) {
                    $urlsToUpdate[$urlField] = $archivoData['ruta'];
                }
            }
            
            // Actualizar URLs en la tabla personal
            if (!empty($urlsToUpdate)) {
                $personal->update($urlsToUpdate);
            }

            // PASO 4: Crear usuario si se seleccionó la opción
            $usuario = null;
            if (!empty($validated['crear_usuario']) && $validated['crear_usuario']) {
                $usuario = User::create([
                    'personal_id' => $personal->id,
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'rol_id' => $validated['rol_id'],
                ]);
            }

            // Cargar relaciones para la respuesta
            $personal->load(['categoria', 'documentos.tipoDocumento', 'usuario.rol']);

            // Log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'crear_personal_completo',
                'tabla_afectada' => 'personal',
                'registro_id' => $personal->id,
                'detalles' => json_encode([
                    'personal' => $personal->nombre_completo,
                    'categoria' => $personal->categoria->nombre_categoria,
                    'documentos_creados' => count($archivosSubidos),
                    'usuario_creado' => $usuario ? true : false,
                    'email_usuario' => $usuario ? $usuario->email : null,
                ]),
            ]);

            // Confirmar transacción
            DB::commit();

            // Respuesta híbrida
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Personal creado exitosamente con todos sus documentos' . ($usuario ? ' y usuario' : ''),
                    'data' => [
                        'personal' => $personal,
                        'documentos_creados' => count($archivosSubidos),
                        'usuario_creado' => $usuario ? true : false,
                    ],
                ], 201);
            }

            return redirect()
                ->route('personal.show', $personal)
                ->with('success', 'Personal creado exitosamente con todos sus documentos' . ($usuario ? ' y usuario' : ''));
                
        } catch (\Exception $e) {
            // Revertir transacción
            DB::rollBack();
            
            // Limpiar archivos subidos en caso de error
            foreach ($archivosSubidos as $archivoData) {
                if (Storage::disk('public')->exists($archivoData['ruta'])) {
                    Storage::disk('public')->delete($archivoData['ruta']);
                }
            }

            // Respuesta de error híbrida
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear el personal completo',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear el personal: ' . $e->getMessage()]);
        }
    }
}