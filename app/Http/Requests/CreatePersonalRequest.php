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
            'nombre_completo' => [
                'required',
                'string',
                'max:255',
                'min:3',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'
            ],
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
            'password_type' => 'required_if:crear_usuario,1|in:random,custom',
            'password' => 'required_if:password_type,custom|nullable|string|min:8|max:255',

            // Documentos - números
            'curp_numero' => [
                'nullable',
                'string',
                'size:18'
            ],
            'rfc' => [
                'nullable',
                'string',
                'max:13',
                'min:10',
                'regex:/^[A-ZÑ&]{3,4}[0-9]{6}[A-Z0-9]{3}$/'
            ],
            'nss' => [
                'nullable',
                'string',
                'size:11',
                'regex:/^[0-9]{11}$/'
            ],
            'no_licencia' => [
                'nullable',
                'string',
                'max:20'
            ],
            
            // Dirección
            'direccion' => [
                'nullable',
                'string',
                'max:500'
            ],
            
            // INE
            'ine' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[A-Z0-9]+$/'
            ],
            
            // URLs de documentos
            'url_ine' => [
                'nullable',
                'string',
                'max:500',
                'url'
            ],
            'url_curp' => [
                'nullable',
                'string',
                'max:500',
                'url'
            ],
            'url_rfc' => [
                'nullable',
                'string',
                'max:500',
                'url'
            ],
            'url_nss' => [
                'nullable',
                'string',
                'max:500',
                'url'
            ],
            'url_licencia' => [
                'nullable',
                'string',
                'max:500',
                'url'
            ],
            'url_comprobante_domicilio' => [
                'nullable',
                'string',
                'max:500',
                'url'
            ],
            
            // Archivos de documentos
            'identificacion_file' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120' // 5MB
            ],
            'curp_file' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120' // 5MB
            ],
            'rfc_file' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120' // 5MB
            ],
            'nss_file' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120' // 5MB
            ],
            'licencia_file' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120' // 5MB
            ],
            'comprobante_file' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120' // 5MB
            ],
            'cv_file' => [
                'nullable',
                'file',
                'mimes:pdf,doc,docx',
                'max:10240' // 10MB
            ],

            // Documentos opcionales para API
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
            // Campos básicos
            'nombre_completo.required' => 'El nombre completo es obligatorio',
            'nombre_completo.min' => 'El nombre completo debe tener al menos 3 caracteres',
            'nombre_completo.max' => 'El nombre completo no debe exceder 255 caracteres',
            'nombre_completo.regex' => 'El nombre completo solo puede contener letras y espacios',
            'estatus.required' => 'El estatus es obligatorio',
            'estatus.in' => 'El estatus debe ser activo o inactivo',
            'categoria_personal_id.required' => 'La categoría es obligatoria',
            'categoria_personal_id.exists' => 'La categoría seleccionada no existe',
            
            // Usuario del sistema
            'email_usuario.required_if' => 'El email es obligatorio cuando se crea un usuario',
            'email_usuario.email' => 'El formato del email no es válido',
            'email_usuario.unique' => 'El email ya está registrado',
            'password_type.required_if' => 'Debe seleccionar el tipo de contraseña cuando se crea un usuario',
            'password_type.in' => 'El tipo de contraseña debe ser aleatoria o personalizada',
            'password.required_if' => 'La contraseña es obligatoria cuando se selecciona contraseña personalizada',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.max' => 'La contraseña no debe exceder 255 caracteres',
            
            // Documentos - números
            'curp_numero.size' => 'El CURP debe tener exactamente 18 caracteres',
            'rfc.min' => 'El RFC debe tener al menos 10 caracteres',
            'rfc.max' => 'El RFC no puede exceder 13 caracteres',
            'rfc.regex' => 'El formato del RFC no es válido',
            'nss.size' => 'El NSS debe tener exactamente 11 dígitos',
            'nss.regex' => 'El NSS solo puede contener números',
            'no_licencia.max' => 'El número de licencia no puede exceder 20 caracteres',
            
            // Dirección
            'direccion.max' => 'La dirección no puede exceder 500 caracteres',
            
            // Archivos
            'identificacion_file.file' => 'Debe seleccionar un archivo válido para la identificación',
            'identificacion_file.mimes' => 'El archivo de identificación debe ser PDF, JPG, JPEG o PNG',
            'identificacion_file.max' => 'El archivo de identificación no puede exceder 5MB',
            'curp_file.file' => 'Debe seleccionar un archivo válido para el CURP',
            'curp_file.mimes' => 'El archivo del CURP debe ser PDF, JPG, JPEG o PNG',
            'curp_file.max' => 'El archivo del CURP no puede exceder 5MB',
            'rfc_file.file' => 'Debe seleccionar un archivo válido para el RFC',
            'rfc_file.mimes' => 'El archivo del RFC debe ser PDF, JPG, JPEG o PNG',
            'rfc_file.max' => 'El archivo del RFC no puede exceder 5MB',
            'nss_file.file' => 'Debe seleccionar un archivo válido para el NSS',
            'nss_file.mimes' => 'El archivo del NSS debe ser PDF, JPG, JPEG o PNG',
            'nss_file.max' => 'El archivo del NSS no puede exceder 5MB',
            'licencia_file.file' => 'Debe seleccionar un archivo válido para la licencia',
            'licencia_file.mimes' => 'El archivo de la licencia debe ser PDF, JPG, JPEG o PNG',
            'licencia_file.max' => 'El archivo de la licencia no puede exceder 5MB',
            'comprobante_file.file' => 'Debe seleccionar un archivo válido para el comprobante',
            'comprobante_file.mimes' => 'El archivo del comprobante debe ser PDF, JPG, JPEG o PNG',
            'comprobante_file.max' => 'El archivo del comprobante no puede exceder 5MB',
            'cv_file.file' => 'Debe seleccionar un archivo válido para el CV',
            'cv_file.mimes' => 'El archivo del CV debe ser PDF, DOC o DOCX',
            'cv_file.max' => 'El archivo del CV no puede exceder 10MB',
            
            // Documentos API
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
            'password_type' => 'tipo de contraseña',
            'password' => 'contraseña',
            'documentos.*.tipo_documento_id' => 'tipo de documento',
            'documentos.*.descripcion' => 'descripción del documento',
            'documentos.*.fecha_vencimiento' => 'fecha de vencimiento',
            'documentos.*.ruta_archivo' => 'archivo',
        ];
    }
}
