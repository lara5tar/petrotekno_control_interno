<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePersonalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Campos básicos
            'nombre_completo' => [
                'required',
                'string',
                'max:255',
                'min:3',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'
            ],
            'categoria_personal_id' => [
                'required',
                'integer',
                'exists:categorias_personal,id'
            ],
            'estatus' => [
                'required',
                Rule::in(['activo', 'inactivo'])
            ],
            
            // Usuario del sistema (opcional)
            'crear_usuario' => 'nullable|boolean',
            'email_usuario' => [
                'required_if:crear_usuario,1',
                'nullable',
                'email',
                'max:255',
                'unique:users,email'
            ],
            
            // Documentos - números
            'no_identificacion' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[A-Z0-9]+$/'
            ],
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
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // Campos básicos
            'nombre_completo.required' => 'El nombre completo es obligatorio.',
            'nombre_completo.min' => 'El nombre completo debe tener al menos 3 caracteres.',
            'nombre_completo.max' => 'El nombre completo no puede exceder 255 caracteres.',
            'nombre_completo.regex' => 'El nombre completo solo puede contener letras y espacios.',
            
            'categoria_personal_id.required' => 'Debe seleccionar una categoría.',
            'categoria_personal_id.exists' => 'La categoría seleccionada no es válida.',
            
            'estatus.required' => 'Debe seleccionar un estatus.',
            'estatus.in' => 'El estatus debe ser activo o inactivo.',
            
            // Usuario del sistema
            'email_usuario.required_if' => 'El email es obligatorio cuando se crea un usuario del sistema.',
            'email_usuario.email' => 'Debe ingresar un email válido.',
            'email_usuario.unique' => 'Este email ya está registrado en el sistema.',
            
            // Documentos - números
            'no_identificacion.regex' => 'El número de identificación solo puede contener letras mayúsculas y números.',
            'no_identificacion.max' => 'El número de identificación no puede exceder 20 caracteres.',
            
            'curp_numero.size' => 'El CURP debe tener exactamente 18 caracteres.',
            
            'rfc.min' => 'El RFC debe tener al menos 10 caracteres.',
            'rfc.max' => 'El RFC no puede exceder 13 caracteres.',
            'rfc.regex' => 'El formato del RFC no es válido.',
            
            'nss.size' => 'El NSS debe tener exactamente 11 dígitos.',
            'nss.regex' => 'El NSS solo puede contener números.',
            
            'no_licencia.max' => 'El número de licencia no puede exceder 20 caracteres.',
            
            // Dirección
            'direccion.max' => 'La dirección no puede exceder 500 caracteres.',
            
            // Archivos
            'identificacion_file.file' => 'Debe seleccionar un archivo válido para la identificación.',
            'identificacion_file.mimes' => 'El archivo de identificación debe ser PDF, JPG, JPEG o PNG.',
            'identificacion_file.max' => 'El archivo de identificación no puede exceder 5MB.',
            
            'curp_file.file' => 'Debe seleccionar un archivo válido para el CURP.',
            'curp_file.mimes' => 'El archivo del CURP debe ser PDF, JPG, JPEG o PNG.',
            'curp_file.max' => 'El archivo del CURP no puede exceder 5MB.',
            
            'rfc_file.file' => 'Debe seleccionar un archivo válido para el RFC.',
            'rfc_file.mimes' => 'El archivo del RFC debe ser PDF, JPG, JPEG o PNG.',
            'rfc_file.max' => 'El archivo del RFC no puede exceder 5MB.',
            
            'nss_file.file' => 'Debe seleccionar un archivo válido para el NSS.',
            'nss_file.mimes' => 'El archivo del NSS debe ser PDF, JPG, JPEG o PNG.',
            'nss_file.max' => 'El archivo del NSS no puede exceder 5MB.',
            
            'licencia_file.file' => 'Debe seleccionar un archivo válido para la licencia.',
            'licencia_file.mimes' => 'El archivo de la licencia debe ser PDF, JPG, JPEG o PNG.',
            'licencia_file.max' => 'El archivo de la licencia no puede exceder 5MB.',
            
            'comprobante_file.file' => 'Debe seleccionar un archivo válido para el comprobante.',
            'comprobante_file.mimes' => 'El archivo del comprobante debe ser PDF, JPG, JPEG o PNG.',
            'comprobante_file.max' => 'El archivo del comprobante no puede exceder 5MB.',
            
            'cv_file.file' => 'Debe seleccionar un archivo válido para el CV.',
            'cv_file.mimes' => 'El archivo del CV debe ser PDF, DOC o DOCX.',
            'cv_file.max' => 'El archivo del CV no puede exceder 10MB.',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'nombre_completo' => 'nombre completo',
            'categoria_personal_id' => 'categoría',
            'estatus' => 'estatus',
            'email_usuario' => 'email del usuario',
            'no_identificacion' => 'número de identificación',
            'curp_numero' => 'CURP',
            'rfc' => 'RFC',
            'nss' => 'NSS',
            'no_licencia' => 'número de licencia',
            'direccion' => 'dirección',
            'identificacion_file' => 'archivo de identificación',
            'curp_file' => 'archivo del CURP',
            'rfc_file' => 'archivo del RFC',
            'nss_file' => 'archivo del NSS',
            'licencia_file' => 'archivo de la licencia',
            'comprobante_file' => 'archivo del comprobante',
            'cv_file' => 'archivo del CV',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validación personalizada: Si se proporciona un número de documento, 
            // se recomienda también subir el archivo
            $documentos = [
                'no_identificacion' => 'identificacion_file',
                'curp_numero' => 'curp_file',
                'rfc' => 'rfc_file',
                'nss' => 'nss_file',
                'no_licencia' => 'licencia_file'
            ];

            foreach ($documentos as $numero => $archivo) {
                if ($this->filled($numero) && !$this->hasFile($archivo)) {
                    $validator->warnings()->add($archivo, "Se recomienda subir el archivo del documento cuando se proporciona el número.");
                }
            }
        });
    }
}