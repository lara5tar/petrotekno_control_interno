<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCatalogoTipoDocumentoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('sanctum')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre_tipo_documento' => [
                'required',
                'string',
                'max:255',
                'unique:catalogo_tipos_documento,nombre_tipo_documento',
            ],
            'descripcion' => [
                'nullable',
                'string',
                'max:500',
            ],
            'requiere_vencimiento' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nombre_tipo_documento.required' => 'El nombre del tipo de documento es obligatorio.',
            'nombre_tipo_documento.max' => 'El nombre no puede exceder 255 caracteres.',
            'nombre_tipo_documento.unique' => 'Ya existe un tipo de documento con este nombre.',
            'descripcion.max' => 'La descripción no puede exceder 500 caracteres.',
            'requiere_vencimiento.boolean' => 'El campo requiere vencimiento debe ser verdadero o falso.',
        ];
    }
}
