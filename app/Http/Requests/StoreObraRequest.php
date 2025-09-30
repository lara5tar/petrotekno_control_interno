<?php

namespace App\Http\Requests;

use App\Models\Obra;
use Illuminate\Foundation\Http\FormRequest;

class StoreObraRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasPermission('crear_obras');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre_obra' => [
                'required',
                'string',
                'min:5',
                'max:200',
                'regex:/^[a-zA-ZÀ-ÿ\s\d\-\.\,\(\)]+$/',
            ],

            'ubicacion' => [
                'nullable',
                'string',
                'max:500',
            ],

            'avance' => [
                'nullable',
                'integer',
                'min:0',
                'max:100',
            ],
            'fecha_inicio' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
            'fecha_fin' => [
                'nullable',
                'date',
                'after:fecha_inicio',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nombre_obra.required' => 'El nombre de la obra es obligatorio.',
            'nombre_obra.min' => 'El nombre de la obra debe tener al menos 5 caracteres.',
            'nombre_obra.max' => 'El nombre de la obra no puede exceder 200 caracteres.',
            'nombre_obra.regex' => 'El nombre de la obra contiene caracteres no permitidos.',

            'ubicacion.string' => 'La ubicación debe ser un texto válido.',
            'ubicacion.max' => 'La ubicación no puede exceder 500 caracteres.',

            'avance.integer' => 'El avance debe ser un número entero.',
            'avance.min' => 'El avance no puede ser menor a 0%.',
            'avance.max' => 'El avance no puede ser mayor a 100%.',

            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida.',
            'fecha_inicio.after_or_equal' => 'La fecha de inicio debe ser hoy o posterior.',

            'fecha_fin.date' => 'La fecha de fin debe ser una fecha válida.',
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'nombre_obra' => 'nombre de la obra',
            'ubicacion' => 'ubicación',
            'avance' => 'avance',
            'fecha_inicio' => 'fecha de inicio',
            'fecha_fin' => 'fecha de fin',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Limpiar y normalizar el nombre de la obra
        if ($this->has('nombre_obra')) {
            $this->merge([
                'nombre_obra' => trim($this->nombre_obra),
            ]);
        }

        // Avance por defecto para nuevas obras
        if (! $this->has('avance')) {
            $this->merge(['avance' => 0]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validaciones personalizadas removidas ya que el estatus se establece automáticamente
        });
    }
}
