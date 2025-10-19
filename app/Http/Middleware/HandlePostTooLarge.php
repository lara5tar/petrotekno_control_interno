<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Symfony\Component\HttpFoundation\Response;

class HandlePostTooLarge
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (PostTooLargeException $e) {
            // Log para debugging
            \Log::error('PostTooLargeException capturada', [
                'url' => $request->url(),
                'method' => $request->method(),
                'content_length' => $request->header('Content-Length'),
                'post_max_size' => ini_get('post_max_size'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
            ]);

            // Obtener configuración actual de PHP
            $uploadMaxFilesize = ini_get('upload_max_filesize');
            $postMaxSize = ini_get('post_max_size');

            $message = "El tamaño total de los archivos excede el límite permitido. " .
                "Límite actual: {$postMaxSize}. " .
                "Por favor, reduce el número o tamaño de los archivos.";

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'error_type' => 'post_too_large',
                    'limits' => [
                        'upload_max_filesize' => $uploadMaxFilesize,
                        'post_max_size' => $postMaxSize,
                    ],
                    'suggestions' => [
                        'Reduce el número de archivos cargados simultáneamente',
                        'Comprime las imágenes antes de subirlas',
                        'Utiliza archivos PDF optimizados',
                        'Carga los documentos uno por uno en lugar de todos juntos'
                    ]
                ], 413);
            }

            // Para requests web regulares, redirigir con mensaje de error
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'upload_error' => $message
                ])
                ->with('upload_limits', [
                    'upload_max_filesize' => $uploadMaxFilesize,
                    'post_max_size' => $postMaxSize,
                ]);
        }
    }
}
