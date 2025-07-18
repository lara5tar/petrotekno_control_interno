<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MantenimientoRequest extends FormRequest
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
        $rules = [
            'vehiculo_id' => 'required|integer|exists:vehiculos,id',
            'tipo_servicio_id' => 'required|integer|exists:catalogo_tipos_servicio,id',
            'proveedor' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string|max:65535',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'kilometraje_servicio' => 'required|integer|min:0|max:999999',
            'costo' => 'nullable|numeric|min:0|max:999999.99',
        ];

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'vehiculo_id.required' => 'El vehículo es requerido',
            'vehiculo_id.exists' => 'El vehículo seleccionado no existe',
            'tipo_servicio_id.required' => 'El tipo de servicio es requerido',
            'tipo_servicio_id.exists' => 'El tipo de servicio seleccionado no existe',
            'proveedor.max' => 'El proveedor no puede exceder 255 caracteres',
            'descripcion.max' => 'La descripción no puede exceder 65535 caracteres',
            'fecha_inicio.required' => 'La fecha de inicio es requerida',
            'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida',
            'fecha_fin.date' => 'La fecha de fin debe ser una fecha válida',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio',
            'kilometraje_servicio.required' => 'El kilometraje de servicio es requerido',
            'kilometraje_servicio.integer' => 'El kilometraje de servicio debe ser un número entero',
            'kilometraje_servicio.min' => 'El kilometraje de servicio no puede ser negativo',
            'kilometraje_servicio.max' => 'El kilometraje de servicio no puede exceder 999,999',
            'costo.numeric' => 'El costo debe ser un número válido',
            'costo.min' => 'El costo no puede ser negativo',
            'costo.max' => 'El costo no puede exceder 999,999.99',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validación personalizada: kilometraje no puede ser excesivamente superior al actual del vehículo
            if ($this->filled('vehiculo_id') && $this->filled('kilometraje_servicio')) {
                $vehiculo = \App\Models\Vehiculo::find($this->vehiculo_id);
                if ($vehiculo && $this->kilometraje_servicio > $vehiculo->kilometraje_actual + 100000) {
                    $validator->errors()->add(
                        'kilometraje_servicio',
                        'El kilometraje de servicio no puede ser excesivamente superior al kilometraje actual del vehículo'
                    );
                }
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Sanitizar campos de texto para prevenir XSS
        if ($this->has('proveedor')) {
            $this->merge([
                'proveedor' => $this->sanitizeInput($this->proveedor),
            ]);
        }

        if ($this->has('descripcion')) {
            $this->merge([
                'descripcion' => $this->sanitizeInput($this->descripcion),
            ]);
        }
    }

    /**
     * Sanitize input to prevent XSS attacks
     */
    private function sanitizeInput($input)
    {
        // Remove HTML tags
        $input = strip_tags($input);
        
        // Remove javascript: and other dangerous protocols
        $input = preg_replace('/javascript:|data:|vbscript:/i', '', $input);
        
        // Remove event handlers
        $input = preg_replace('/on\w+\s*=/i', '', $input);
        
        return trim($input);
    }
}
