<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentoRequest extends FormRequest
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
            'tipo_documento_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:catalogo_tipos_documento,id'
            ],
            'descripcion' => [
                'sometimes',
                'nullable',
                'string',
                'max:1000'
            ],
            'ruta_archivo' => [
                'sometimes',
                'nullable',
                'string',
                'max:500'
            ],
            'fecha_vencimiento' => [
                'sometimes',
                'nullable',
                'date',
                'after_or_equal:today'
            ],
            'vehiculo_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:vehiculos,id'
            ],
            'personal_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:personal,id'
            ],
            'obra_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:obras,id'
            ],
            'mantenimiento_id' => [
                'sometimes',
                'nullable',
                'integer'
                // TODO: Agregar exists cuando se implemente mantenimientos
            ],
            'archivo' => [
                'sometimes',
                'nullable',
                'file',
                'max:10240',
                'mimes:pdf,doc,docx,jpg,jpeg,png,txt,xls,xlsx'
            ],
            'multiple_associations' => [
                'prohibited'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'tipo_documento_id.exists' => 'El tipo de documento seleccionado no es válido.',
            'descripcion.max' => 'La descripción no puede exceder 1000 caracteres.',
            'ruta_archivo.max' => 'La ruta del archivo no puede exceder 500 caracteres.',
            'fecha_vencimiento.date' => 'La fecha de vencimiento debe ser una fecha válida.',
            'fecha_vencimiento.after_or_equal' => 'La fecha de vencimiento no puede ser anterior a hoy.',
            'vehiculo_id.exists' => 'El vehículo seleccionado no es válido.',
            'personal_id.exists' => 'El personal seleccionado no es válido.',
            'obra_id.exists' => 'La obra seleccionada no es válida.',
            'archivo.file' => 'Debe seleccionar un archivo válido.',
            'archivo.max' => 'El archivo no puede ser mayor a 10MB.',
            'archivo.mimes' => 'El archivo debe ser de tipo: PDF, DOC, DOCX, JPG, JPEG, PNG, TXT, XLS, XLSX.',
            'multiple_associations.prohibited' => 'Un documento no puede estar asociado a múltiples entidades al mismo tiempo.'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Asegurar que solo se asocie a una entidad a la vez
        $entidades = collect(['vehiculo_id', 'personal_id', 'obra_id', 'mantenimiento_id'])
            ->filter(fn($key) => $this->filled($key))
            ->count();

        if ($entidades > 1) {
            $this->merge([
                'multiple_associations' => true
            ]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        // Validar que si el tipo requiere vencimiento, se proporcione la fecha
        $validator->after(function ($validator) {
            if ($this->filled('tipo_documento_id')) {
                $tipoDocumento = \App\Models\CatalogoTipoDocumento::find($this->tipo_documento_id);
                
                if ($tipoDocumento && $tipoDocumento->requiere_vencimiento && !$this->filled('fecha_vencimiento')) {
                    $validator->errors()->add('fecha_vencimiento', 'La fecha de vencimiento es obligatoria para este tipo de documento.');
                }
            }
        });
    }
}
