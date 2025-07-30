<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePersonalRequest extends FormRequest
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
            'nombre_completo' => [
                'required',
                'string',
                'max:255',
                'min:3',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'
            ],
            'categoria_id' => [
                'required',
                'integer',
                'exists:categorias_personal,id'
            ],
            'estatus' => [
                'required',
                Rule::in(['activo', 'inactivo'])
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
            'direccion' => [
                'nullable',
                'string',
                'max:500'
            ],
            'ine' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[A-Z0-9]+$/'
            ],
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
            'curp_file' => [
                'nullable'
            ],
            'identificacion_file' => [
                'nullable'
            ],
            'rfc_file' => [
                'nullable'
            ],
            'nss_file' => [
                'nullable'
            ],
            'licencia_file' => [
                'nullable'
            ],
            'comprobante_file' => [
                'nullable'
            ],
            'cv_file' => [
                'nullable'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nombre_completo.required' => 'El nombre completo es obligatorio.',
            'nombre_completo.max' => 'El nombre completo no puede exceder 255 caracteres.',
            'nombre_completo.min' => 'El nombre completo debe tener al menos 3 caracteres.',
            'nombre_completo.regex' => 'El nombre completo solo puede contener letras y espacios.',
            'categoria_id.required' => 'La categoría es obligatoria.',
            'categoria_id.exists' => 'La categoría seleccionada no es válida.',
            'estatus.required' => 'El estatus es obligatorio.',
            'estatus.in' => 'El estatus debe ser activo o inactivo.',
            'curp_numero.size' => 'El CURP debe tener exactamente 18 caracteres.',
            'curp_file.mimes' => 'El archivo del CURP debe ser PDF, JPG, JPEG o PNG.',
            'curp_file.max' => 'El archivo del CURP no puede exceder 5MB.',
            'identificacion_file.mimes' => 'El archivo de identificación debe ser PDF, JPG, JPEG o PNG.',
            'identificacion_file.max' => 'El archivo de identificación no puede exceder 5MB.',
            'rfc_file.mimes' => 'El archivo del RFC debe ser PDF, JPG, JPEG o PNG.',
            'rfc_file.max' => 'El archivo del RFC no puede exceder 5MB.',
            'nss_file.mimes' => 'El archivo del NSS debe ser PDF, JPG, JPEG o PNG.',
            'nss_file.max' => 'El archivo del NSS no puede exceder 5MB.',
            'licencia_file.mimes' => 'El archivo de la licencia debe ser PDF, JPG, JPEG o PNG.',
            'licencia_file.max' => 'El archivo de la licencia no puede exceder 5MB.',
            'comprobante_file.mimes' => 'El archivo del comprobante debe ser PDF, JPG, JPEG o PNG.',
            'comprobante_file.max' => 'El archivo del comprobante no puede exceder 5MB.',
            'cv_file.mimes' => 'El archivo del CV debe ser PDF, DOC o DOCX.',
            'cv_file.max' => 'El archivo del CV no puede exceder 10MB.'
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'nombre_completo' => 'nombre completo',
            'categoria_id' => 'categoría',
            'estatus' => 'estatus',
            'curp_numero' => 'CURP',
            'curp_file' => 'archivo del CURP',
            'identificacion_file' => 'archivo de identificación',
            'rfc_file' => 'archivo del RFC',
            'nss_file' => 'archivo del NSS',
            'licencia_file' => 'archivo de la licencia',
            'comprobante_file' => 'archivo del comprobante',
            'cv_file' => 'archivo del CV'
        ];
    }
}