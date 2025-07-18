<?php

namespace App\Http\Requests;

use App\Models\Obra;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateObraRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasPermission('editar_obra');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Obtener el ID de la obra ya sea por model binding o parámetro
        $obraId = null;
        $obra = $this->route('obra');

        if ($obra instanceof \App\Models\Obra) {
            $obraId = $obra->id;
        } else {
            // Fallback para usar el ID directamente
            $obraId = $obra ?? $this->route('id');
        }

        return [
            'nombre_obra' => [
                'sometimes',
                'required',
                'string',
                'min:5',
                'max:200',
                Rule::unique('obras', 'nombre_obra')->ignore($obraId),
                'regex:/^[a-zA-ZÀ-ÿ\s\d\-\.\,\(\)]+$/',
            ],
            'estatus' => [
                'sometimes',
                'required',
                'string',
                'in:'.implode(',', Obra::ESTADOS_VALIDOS),
            ],
            'avance' => [
                'sometimes',
                'nullable',
                'integer',
                'min:0',
                'max:100',
            ],
            'fecha_inicio' => [
                'sometimes',
                'required',
                'date',
            ],
            'fecha_fin' => [
                'sometimes',
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
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Obtener la obra usando model binding o buscarla por ID
            $obra = $this->route('obra');

            if (! ($obra instanceof \App\Models\Obra)) {
                // Si no es un objeto Obra, buscarla por ID
                $obraId = $obra ?? $this->route('id');
                $obra = \App\Models\Obra::find($obraId);
            }

            // Validación de transiciones de estatus
            if ($this->has('estatus') && $obra) {
                $nuevoEstatus = $this->estatus;
                $estatusActual = $obra->estatus;

                $transicionesPermitidas = [
                    Obra::ESTATUS_PLANIFICADA => [Obra::ESTATUS_EN_PROGRESO, Obra::ESTATUS_CANCELADA],
                    Obra::ESTATUS_EN_PROGRESO => [Obra::ESTATUS_SUSPENDIDA, Obra::ESTATUS_COMPLETADA, Obra::ESTATUS_CANCELADA],
                    Obra::ESTATUS_SUSPENDIDA => [Obra::ESTATUS_EN_PROGRESO, Obra::ESTATUS_CANCELADA],
                    Obra::ESTATUS_COMPLETADA => [], // No se puede cambiar desde completada
                    Obra::ESTATUS_CANCELADA => [], // No se puede cambiar desde cancelada
                ];

                if ($nuevoEstatus !== $estatusActual &&
                    ! in_array($nuevoEstatus, $transicionesPermitidas[$estatusActual] ?? [])) {
                    $validator->errors()->add('estatus',
                        "No se puede cambiar el estatus de '{$estatusActual}' a '{$nuevoEstatus}'."
                    );
                }
            }

            // Validación: Si está completada, avance debe ser 100
            if ($this->has('estatus') && $this->estatus === Obra::ESTATUS_COMPLETADA) {
                if ($this->has('avance') && $this->avance !== 100) {
                    $validator->errors()->add('avance', 'Una obra completada debe tener 100% de avance.');
                }
            }

            // Validación: Si está planificada, avance debe ser 0
            if ($this->has('estatus') && $this->estatus === Obra::ESTATUS_PLANIFICADA) {
                if ($this->has('avance') && $this->avance > 0) {
                    $validator->errors()->add('avance', 'Una obra planificada no puede tener avance mayor a 0%.');
                }
            }

            // Validación: Fecha fin requerida para obras completadas
            if ($this->has('estatus') && $this->estatus === Obra::ESTATUS_COMPLETADA) {
                if (! $this->has('fecha_fin') || ! $this->fecha_fin) {
                    $validator->errors()->add('fecha_fin', 'Una obra completada debe tener fecha de finalización.');
                }
            }

            // Validación: No permitir fecha fin en el pasado para obras no completadas
            if ($this->has('fecha_fin') && $this->fecha_fin) {
                $fechaFin = \Carbon\Carbon::parse($this->fecha_fin);
                $estatus = $this->estatus ?? ($obra ? $obra->estatus : null);

                if ($fechaFin->isPast() && $estatus !== Obra::ESTATUS_COMPLETADA) {
                    $validator->errors()->add('fecha_fin',
                        'No se puede establecer una fecha fin en el pasado para obras no completadas.'
                    );
                }
            }
        });
    }
}
