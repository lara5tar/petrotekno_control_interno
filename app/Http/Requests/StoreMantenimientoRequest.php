<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMantenimientoRequest extends FormRequest
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
                'required',
                'integer',
                'exists:vehiculos,id',
            ],
            'tipo_servicio' => [
                'required',
                'string',
                'in:CORRECTIVO,PREVENTIVO',
            ],
            'sistema_vehiculo' => [
                'required',
                'string',
                'in:motor,transmision,hidraulico,general',
            ],
            'proveedor' => [
                'nullable',
                'string',
                'max:255',
            ],
            'descripcion' => [
                'required',
                'string',
                'max:1000',
            ],
            'fecha_inicio' => [
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
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        // Validar que el kilometraje sea consistente con el vehículo
        $validator->after(function ($validator) {
            if ($this->filled('vehiculo_id') && $this->filled('kilometraje_servicio')) {
                $vehiculo = \App\Models\Vehiculo::find($this->vehiculo_id);

                if ($vehiculo) {
                    // NUEVA LÓGICA: Permitir kilometraje mayor al actual (se actualizará automáticamente)
                    // Solo validar que no sea excesivamente mayor (diferencia máxima de 50,000 km)
                    $diferencia = $this->kilometraje_servicio - $vehiculo->kilometraje_actual;

                    if ($diferencia > 50000) {
                        $validator->errors()->add(
                            'kilometraje_servicio',
                            "El kilometraje de servicio ({$this->kilometraje_servicio}) parece excesivamente alto comparado con el kilometraje actual del vehículo ({$vehiculo->kilometraje_actual}). Diferencia: {$diferencia} km."
                        );
                    }

                    // Validar contra mantenimientos previos del mismo sistema
                    if ($this->filled('sistema_vehiculo') && $this->sistema_vehiculo !== 'general') {
                        $ultimoMantenimiento = \App\Models\Mantenimiento::where('vehiculo_id', $this->vehiculo_id)
                            ->where('sistema_vehiculo', $this->sistema_vehiculo)
                            ->orderBy('kilometraje_servicio', 'desc')
                            ->first();

                        if ($ultimoMantenimiento && $this->kilometraje_servicio < $ultimoMantenimiento->kilometraje_servicio) {
                            $validator->errors()->add(
                                'kilometraje_servicio',
                                "El kilometraje de servicio ({$this->kilometraje_servicio}) no puede ser menor al último mantenimiento de {$this->sistema_vehiculo} ({$ultimoMantenimiento->kilometraje_servicio} km)."
                            );
                        }
                    }
                }
            }
        });
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'vehiculo_id.required' => 'El vehículo es obligatorio.',
            'vehiculo_id.exists' => 'El vehículo seleccionado no existe.',
            'tipo_servicio.required' => 'El tipo de servicio es obligatorio.',
            'tipo_servicio.in' => 'El tipo de servicio debe ser CORRECTIVO o PREVENTIVO.',
            'sistema_vehiculo.required' => 'El sistema del vehículo es obligatorio.',
            'sistema_vehiculo.in' => 'El sistema del vehículo debe ser: motor, transmisión, hidráulico o general.',
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
            'tipo_servicio' => 'tipo de servicio',
            'sistema_vehiculo' => 'sistema del vehículo',
            'proveedor' => 'proveedor',
            'descripcion' => 'descripción',
            'fecha_inicio' => 'fecha de inicio',
            'fecha_fin' => 'fecha de fin',
            'kilometraje_servicio' => 'kilometraje de servicio',
            'costo' => 'costo',
        ];
    }
}
