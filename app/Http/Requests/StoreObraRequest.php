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
        return $this->user() && $this->user()->hasPermission('crear_obra');
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
                'unique:obras,nombre_obra',
                'regex:/^[a-zA-ZÀ-ÿ\s\d\-\.\,\(\)]+$/',
            ],
            'estatus' => [
                'required',
                'string',
                'in:'.implode(',', Obra::ESTADOS_VALIDOS),
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
            'nombre_obra.unique' => 'Ya existe una obra con este nombre.',
            'nombre_obra.regex' => 'El nombre de la obra contiene caracteres no permitidos.',

            'estatus.required' => 'El estatus de la obra es obligatorio.',
            'estatus.in' => 'El estatus seleccionado no es válido.',

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
            'estatus' => 'estatus',
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

        // Establecer avance por defecto según estatus
        if ($this->has('estatus') && ! $this->has('avance')) {
            $avanceDefecto = match ($this->estatus) {
                Obra::ESTATUS_PLANIFICADA => 0,
                Obra::ESTATUS_COMPLETADA => 100,
                default => null
            };

            if ($avanceDefecto !== null) {
                $this->merge(['avance' => $avanceDefecto]);
            }
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validación personalizada: Si está completada, avance debe ser 100
            if ($this->estatus === Obra::ESTATUS_COMPLETADA && $this->avance !== 100) {
                $validator->errors()->add('avance', 'Una obra completada debe tener 100% de avance.');
            }

            // Validación personalizada: Si está planificada, avance debe ser 0
            if ($this->estatus === Obra::ESTATUS_PLANIFICADA && $this->avance > 0) {
                $validator->errors()->add('avance', 'Una obra planificada no puede tener avance mayor a 0%.');
            }

            // Validación personalizada: Fecha fin requerida para obras completadas
            if ($this->estatus === Obra::ESTATUS_COMPLETADA && ! $this->fecha_fin) {
                $validator->errors()->add('fecha_fin', 'Una obra completada debe tener fecha de finalización.');
            }
        });
    }
}
