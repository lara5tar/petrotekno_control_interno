<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCatalogoTipoServicioRequest extends FormRequest
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
            'nombre_tipo_servicio' => [
                'required',
                'string',
                'max:255',
                'unique:catalogo_tipos_servicio,nombre_tipo_servicio',
            ],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nombre_tipo_servicio.required' => 'El nombre del tipo de servicio es obligatorio.',
            'nombre_tipo_servicio.string' => 'El nombre del tipo de servicio debe ser texto.',
            'nombre_tipo_servicio.max' => 'El nombre del tipo de servicio no puede tener más de 255 caracteres.',
            'nombre_tipo_servicio.unique' => 'Ya existe un tipo de servicio con este nombre.',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'nombre_tipo_servicio' => 'nombre del tipo de servicio',
        ];
    }
}
