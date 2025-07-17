<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();

        // Campos que necesitan sanitización estricta
        $fieldsToSanitize = [
            'nombre_usuario',
            'nombre_completo',
            'descripcion',
            'nombre_rol',
            'nombre_categoria',
        ];

        foreach ($fieldsToSanitize as $field) {
            if (isset($input[$field])) {
                // Solo aplicar sanitización anti-XSS básica sin HTMLPurifier
                $input[$field] = $this->removeXSSContent($input[$field]);
            }
        }

        // Reemplazar el input del request con la versión sanitizada
        $request->replace($input);

        return $next($request);
    }

    /**
     * Remove XSS content from string
     */
    private function removeXSSContent($value): string
    {
        // Remover scripts, iframes, objetos, etc.
        $value = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $value);
        $value = preg_replace('/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/mi', '', $value);
        $value = preg_replace('/<object\b[^<]*(?:(?!<\/object>)<[^<]*)*<\/object>/mi', '', $value);
        $value = preg_replace('/<embed\b[^<]*(?:(?!<\/embed>)<[^<]*)*<\/embed>/mi', '', $value);
        $value = preg_replace('/<applet\b[^<]*(?:(?!<\/applet>)<[^<]*)*<\/applet>/mi', '', $value);

        // Remover eventos JavaScript
        $value = preg_replace('/\bon\w+\s*=\s*["\'][^"\']*.["\']/i', '', $value);

        // Remover javascript: URLs
        $value = preg_replace('/javascript\s*:/i', '', $value);

        // Remover data: URLs que puedan contener scripts
        $value = preg_replace('/data\s*:\s*text\/html/i', '', $value);

        return trim($value);
    }
}
