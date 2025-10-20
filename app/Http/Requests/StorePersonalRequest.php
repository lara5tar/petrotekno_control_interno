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


            // Usuario del sistema (opcional)
            'crear_usuario' => 'nullable|boolean',
            'email_usuario' => [
                'required_if:crear_usuario,1',
                'nullable',
                'email',
                'max:255',
                'unique:users,email'
            ],
            'rol_usuario' => [
                'required_if:crear_usuario,1',
                'nullable',
                'integer',
                'exists:roles,id'
            ],
            'tipo_password' => [
                'required_if:crear_usuario,1',
                Rule::in(['aleatoria'])  // Solo contraseña aleatoria
            ],

            // Documentos - números (todos opcionales) - nombres exactos de la BD
            'ine' => [
                'nullable',
                'string',
                'max:20'
            ],
            'curp_numero' => [
                'nullable',
                'string',
                'max:18'
            ],
            'rfc' => [
                'nullable',
                'string',
                'max:13'
            ],
            'nss' => [
                'nullable',
                'string',
                'max:11'
            ],
            'no_licencia' => [
                'nullable',
                'string',
                'max:20'
            ],

            // Dirección
            'direccion' => [
                'nullable',
                'string'
            ],

            // Fechas laborales
            'fecha_inicio_laboral' => [
                'nullable',
                'date'
            ],
            'fecha_termino_laboral' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) {
                    if ($value && request('fecha_inicio_laboral')) {
                        $fechaInicio = \Carbon\Carbon::parse(request('fecha_inicio_laboral'));
                        $fechaTermino = \Carbon\Carbon::parse($value);
                        if ($fechaTermino->lt($fechaInicio)) {
                            $fail('La fecha de término laboral debe ser igual o posterior a la fecha de inicio.');
                        }
                    }
                }
            ],

            // Archivos de documentos
            'archivo_inicio_laboral' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:10240' // 10MB
            ],
            'archivo_termino_laboral' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:10240' // 10MB
            ],
            'archivo_ine' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:10240' // 10MB
            ],
            'archivo_curp' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:10240' // 10MB
            ],
            'archivo_rfc' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:10240' // 10MB
            ],
            'archivo_nss' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:10240' // 10MB
            ],
            'archivo_licencia' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:10240' // 10MB
            ],
            'archivo_comprobante_domicilio' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:10240' // 10MB
            ],
            'archivo_cv' => [
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



            // Usuario del sistema
            'email_usuario.required_if' => 'El email es obligatorio cuando se crea un usuario del sistema.',
            'email_usuario.email' => 'Debe ingresar un email válido.',
            'email_usuario.unique' => 'Este email ya está registrado en el sistema.',

            'rol_usuario.required_if' => 'Debe seleccionar un rol cuando se crea un usuario del sistema.',
            'rol_usuario.exists' => 'El rol seleccionado no es válido.',

            'tipo_password.required_if' => 'Debe seleccionar el tipo de contraseña.',
            'tipo_password.in' => 'El tipo de contraseña debe ser aleatoria.',

            // Documentos - números
            'ine.max' => 'El número de INE no puede exceder 20 caracteres.',

            'curp_numero.max' => 'El CURP no puede exceder 18 caracteres.',

            'rfc.max' => 'El RFC no puede exceder 13 caracteres.',

            'nss.max' => 'El NSS no puede exceder 11 caracteres.',

            'no_licencia.max' => 'El número de licencia no puede exceder 20 caracteres.',

            // Dirección
            'direccion.max' => 'La dirección no puede exceder 500 caracteres.',

            // Fechas laborales
            'fecha_inicio_laboral.date' => 'La fecha de inicio laboral debe ser una fecha válida.',
            'fecha_termino_laboral.date' => 'La fecha de término laboral debe ser una fecha válida.',
            'fecha_termino_laboral.after_or_equal' => 'La fecha de término laboral debe ser igual o posterior a la fecha de inicio.',

            // Archivos
            'archivo_inicio_laboral.file' => 'Debe seleccionar un archivo válido para el documento de inicio laboral.',
            'archivo_inicio_laboral.mimes' => 'El archivo de inicio laboral debe ser PDF, JPG, JPEG o PNG.',
            'archivo_inicio_laboral.max' => 'El archivo de inicio laboral no puede exceder 10MB.',

            'archivo_termino_laboral.file' => 'Debe seleccionar un archivo válido para el documento de término laboral.',
            'archivo_termino_laboral.mimes' => 'El archivo de término laboral debe ser PDF, JPG, JPEG o PNG.',
            'archivo_termino_laboral.max' => 'El archivo de término laboral no puede exceder 10MB.',

            'archivo_ine.file' => 'Debe seleccionar un archivo válido para la identificación.',
            'archivo_ine.mimes' => 'El archivo de identificación debe ser PDF, JPG, JPEG o PNG.',
            'archivo_ine.max' => 'El archivo de identificación no puede exceder 10MB.',

            'archivo_curp.file' => 'Debe seleccionar un archivo válido para el CURP.',
            'archivo_curp.mimes' => 'El archivo del CURP debe ser PDF, JPG, JPEG o PNG.',
            'archivo_curp.max' => 'El archivo del CURP no puede exceder 10MB.',

            'archivo_rfc.file' => 'Debe seleccionar un archivo válido para el RFC.',
            'archivo_rfc.mimes' => 'El archivo del RFC debe ser PDF, JPG, JPEG o PNG.',
            'archivo_rfc.max' => 'El archivo del RFC no puede exceder 10MB.',

            'archivo_nss.file' => 'Debe seleccionar un archivo válido para el NSS.',
            'archivo_nss.mimes' => 'El archivo del NSS debe ser PDF, JPG, JPEG o PNG.',
            'archivo_nss.max' => 'El archivo del NSS no puede exceder 10MB.',

            'archivo_licencia.file' => 'Debe seleccionar un archivo válido para la licencia.',
            'archivo_licencia.mimes' => 'El archivo de la licencia debe ser PDF, JPG, JPEG o PNG.',
            'archivo_licencia.max' => 'El archivo de la licencia no puede exceder 10MB.',

            'archivo_comprobante_domicilio.file' => 'Debe seleccionar un archivo válido para el comprobante.',
            'archivo_comprobante_domicilio.mimes' => 'El archivo del comprobante debe ser PDF, JPG, JPEG o PNG.',
            'archivo_comprobante_domicilio.max' => 'El archivo del comprobante no puede exceder 10MB.',

            'archivo_cv.file' => 'Debe seleccionar un archivo válido para el CV.',
            'archivo_cv.mimes' => 'El archivo del CV debe ser PDF, DOC o DOCX.',
            'archivo_cv.max' => 'El archivo del CV no puede exceder 10MB.',
            'archivo_comprobante_domicilio.mimes' => 'El archivo del comprobante debe ser PDF, JPG, JPEG o PNG.',
            'archivo_comprobante_domicilio.max' => 'El archivo del comprobante no puede exceder 10MB.',

            'archivo_cv.file' => 'Debe seleccionar un archivo válido para el CV.',
            'archivo_cv.mimes' => 'El archivo del CV debe ser PDF, DOC o DOCX.',
            'archivo_cv.max' => 'El archivo del CV no puede exceder 10MB.',
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

            'email_usuario' => 'email del usuario',
            'rol_usuario' => 'rol del usuario',
            'tipo_password' => 'tipo de contraseña',
            'ine' => 'número de INE',
            'curp_numero' => 'CURP',
            'rfc' => 'RFC',
            'nss' => 'NSS',
            'no_licencia' => 'número de licencia',
            'direccion' => 'dirección',
            'fecha_inicio_laboral' => 'fecha de inicio laboral',
            'fecha_termino_laboral' => 'fecha de término laboral',
            'archivo_inicio_laboral' => 'archivo de inicio laboral',
            'archivo_termino_laboral' => 'archivo de término laboral',
            'archivo_ine' => 'archivo de identificación',
            'archivo_curp' => 'archivo del CURP',
            'archivo_rfc' => 'archivo del RFC',
            'archivo_nss' => 'archivo del NSS',
            'archivo_licencia' => 'archivo de la licencia',
            'archivo_comprobante_domicilio' => 'archivo del comprobante',
            'archivo_cv' => 'archivo del CV',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        // Se eliminaron las validaciones de documentos para hacerlos completamente opcionales
    }
}
