<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePersonalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Simplificamos la autorización por ahora
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Datos básicos del personal
            'nombre_completo' => 'required|string|max:255',
            'estatus' => 'required|string|in:activo,inactivo',
            'categoria_personal_id' => [ // El formulario web usa este nombre
                'required',
                'integer',
                Rule::exists('categorias_personal', 'id'),
            ],

            // Configuración de usuario
            'crear_usuario' => 'nullable|boolean',
            'email_usuario' => [ // El formulario web usa este nombre
                'required_if:crear_usuario,1',
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->whereNull('deleted_at'),
            ],

            // Documentos opcionales (ignorados por ahora)
            'documentos' => 'nullable|array',
            'documentos.*.tipo_documento_id' => [
                'required_with:documentos',
                'integer',
                Rule::exists('catalogo_tipos_documento', 'id'),
            ],
            'documentos.*.descripcion' => 'nullable|string|max:1000',
            'documentos.*.fecha_vencimiento' => 'nullable|date|after:today',
            'documentos.*.ruta_archivo' => 'nullable|string|max:500',
            'documentos.*.contenido' => 'nullable|array',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'nombre_completo.required' => 'El nombre completo es obligatorio',
            'nombre_completo.max' => 'El nombre completo no debe exceder 255 caracteres',
            'estatus.required' => 'El estatus es obligatorio',
            'estatus.in' => 'El estatus debe ser activo o inactivo',
            'categoria_personal_id.required' => 'La categoría es obligatoria',
            'categoria_personal_id.exists' => 'La categoría seleccionada no existe',
            'email_usuario.required_if' => 'El email es obligatorio cuando se crea un usuario',
            'email_usuario.email' => 'El formato del email no es válido',
            'email_usuario.unique' => 'El email ya está registrado',
            'documentos.*.tipo_documento_id.required_with' => 'El tipo de documento es obligatorio',
            'documentos.*.tipo_documento_id.exists' => 'El tipo de documento no existe',
            'documentos.*.fecha_vencimiento.after' => 'La fecha de vencimiento debe ser posterior a hoy',
        ];
    }

    /**
     * Get custom attributes for validation errors.
     */
    public function attributes(): array
    {
        return [
            'nombre_completo' => 'nombre completo',
            'categoria_personal_id' => 'categoría',
            'email_usuario' => 'email del usuario',
            'documentos.*.tipo_documento_id' => 'tipo de documento',
            'documentos.*.descripcion' => 'descripción del documento',
            'documentos.*.fecha_vencimiento' => 'fecha de vencimiento',
            'documentos.*.ruta_archivo' => 'archivo',
        ];
    }
}
