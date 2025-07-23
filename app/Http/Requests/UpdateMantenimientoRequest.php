<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMantenimientoRequest extends FormRequest
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
            'vehiculo_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:vehiculos,id',
            ],
            'tipo_servicio_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:catalogo_tipos_servicio,id',
            ],
            'proveedor' => [
                'nullable',
                'string',
                'max:255',
            ],
            'descripcion' => [
                'sometimes',
                'required',
                'string',
            ],
            'fecha_inicio' => [
                'sometimes',
                'required',
                'date',
                'before_or_equal:today',
            ],
            'fecha_fin' => [
                'nullable',
                'date',
                'after_or_equal:fecha_inicio',
            ],
            'kilometraje_servicio' => [
                'sometimes',
                'required',
                'integer',
                'min:0',
            ],
            'costo' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'vehiculo_id.required' => 'El vehículo es obligatorio.',
            'vehiculo_id.exists' => 'El vehículo seleccionado no existe.',
            'tipo_servicio_id.required' => 'El tipo de servicio es obligatorio.',
            'tipo_servicio_id.exists' => 'El tipo de servicio seleccionado no existe.',
            'descripcion.required' => 'La descripción del mantenimiento es obligatoria.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_inicio.before_or_equal' => 'La fecha de inicio no puede ser futura.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
            'kilometraje_servicio.required' => 'El kilometraje de servicio es obligatorio.',
            'kilometraje_servicio.min' => 'El kilometraje de servicio no puede ser negativo.',
            'costo.min' => 'El costo no puede ser negativo.',
            'costo.max' => 'El costo no puede exceder 999,999.99.',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'vehiculo_id' => 'vehículo',
            'tipo_servicio_id' => 'tipo de servicio',
            'proveedor' => 'proveedor',
            'descripcion' => 'descripción',
            'fecha_inicio' => 'fecha de inicio',
            'fecha_fin' => 'fecha de fin',
            'kilometraje_servicio' => 'kilometraje de servicio',
            'costo' => 'costo',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        // Validar que el kilometraje sea consistente con el vehículo
        $validator->after(function ($validator) {
            $vehiculoId = $this->filled('vehiculo_id') ? $this->vehiculo_id : $this->route('mantenimiento')->vehiculo_id;

            if ($vehiculoId && $this->filled('kilometraje_servicio')) {
                $vehiculo = \App\Models\Vehiculo::find($vehiculoId);

                if ($vehiculo && $this->kilometraje_servicio > $vehiculo->kilometraje_actual) {
                    $validator->errors()->add(
                        'kilometraje_servicio',
                        "El kilometraje de servicio ({$this->kilometraje_servicio}) no puede ser mayor al kilometraje actual del vehículo ({$vehiculo->kilometraje_actual})."
                    );
                }
            }
        });
    }
}
