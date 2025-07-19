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
            'categoria_id' => [
                'required',
                'integer',
                Rule::exists('categorias_personal', 'id'),
            ],

            // Configuración de usuario
            'crear_usuario' => 'required|boolean',
            'email' => [
                'required_if:crear_usuario,true',
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->whereNull('deleted_at'),
            ],
            'rol_id' => [
                'required_if:crear_usuario,true',
                'nullable',
                'integer',
                Rule::exists('roles', 'id'),
            ],

            // Documentos opcionales
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
            'categoria_id.required' => 'La categoría es obligatoria',
            'categoria_id.exists' => 'La categoría seleccionada no existe',
            'crear_usuario.required' => 'Debe indicar si se creará un usuario',
            'email.required_if' => 'El email es obligatorio cuando se crea un usuario',
            'email.email' => 'El formato del email no es válido',
            'email.unique' => 'El email ya está registrado',
            'rol_id.required_if' => 'El rol es obligatorio cuando se crea un usuario',
            'rol_id.exists' => 'El rol seleccionado no existe',
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
            'categoria_id' => 'categoría',
            'crear_usuario' => 'crear usuario',
            'rol_id' => 'rol',
            'documentos.*.tipo_documento_id' => 'tipo de documento',
            'documentos.*.descripcion' => 'descripción del documento',
            'documentos.*.fecha_vencimiento' => 'fecha de vencimiento',
            'documentos.*.ruta_archivo' => 'archivo',
        ];
    }
}
