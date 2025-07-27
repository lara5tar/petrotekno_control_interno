<?php

namespace App\Http\Requests;

use App\Models\Kilometraje;
use Illuminate\Foundation\Http\FormRequest;

class UpdateKilometrajeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('editar_kilometrajes');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $kilometraje = $this->route('kilometraje');

        return [
            'kilometraje' => [
                'sometimes',
                'required',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) use ($kilometraje) {
                    if (! $kilometraje) {
                        return;
                    }

                    // Para ediciones, permitir cierta flexibilidad pero mantener coherencia
                    $vehiculoId = $kilometraje->vehiculo_id;

                    // Buscar el kilometraje anterior y posterior
                    $anterior = Kilometraje::where('vehiculo_id', $vehiculoId)
                        ->where('kilometraje', '<', $kilometraje->kilometraje)
                        ->orderBy('kilometraje', 'desc')
                        ->first();

                    $posterior = Kilometraje::where('vehiculo_id', $vehiculoId)
                        ->where('kilometraje', '>', $kilometraje->kilometraje)
                        ->orderBy('kilometraje', 'asc')
                        ->first();

                    // Validar que esté entre el anterior y posterior
                    if ($anterior && $value <= $anterior->kilometraje) {
                        $fail("El kilometraje debe ser mayor al registro anterior ({$anterior->kilometraje} km).");
                    }

                    if ($posterior && $value >= $posterior->kilometraje) {
                        $fail("El kilometraje debe ser menor al registro posterior ({$posterior->kilometraje} km).");
                    }
                },
            ],
            'fecha_captura' => [
                'sometimes',
                'required',
                'date',
                'before_or_equal:today',
                'after:2020-01-01',
            ],
            'obra_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:obras,id',
            ],
            'observaciones' => [
                'sometimes',
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kilometraje.required' => 'El kilometraje es obligatorio.',
            'kilometraje.integer' => 'El kilometraje debe ser un número entero.',
            'kilometraje.min' => 'El kilometraje no puede ser negativo.',
            'fecha_captura.required' => 'La fecha de captura es obligatoria.',
            'fecha_captura.date' => 'La fecha de captura debe tener un formato válido.',
            'fecha_captura.before_or_equal' => 'La fecha de captura no puede ser futura.',
            'fecha_captura.after' => 'La fecha de captura debe ser posterior a enero 2020.',
            'obra_id.exists' => 'La obra seleccionada no existe.',
            'observaciones.max' => 'Las observaciones no pueden exceder 1000 caracteres.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'kilometraje' => 'kilometraje',
            'fecha_captura' => 'fecha de captura',
            'obra_id' => 'obra',
            'observaciones' => 'observaciones',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Limpiar kilometraje de comas y puntos para formato internacional
        if ($this->has('kilometraje')) {
            $kilometraje = str_replace([',', '.'], '', $this->input('kilometraje'));
            $this->merge(['kilometraje' => (int) $kilometraje]);
        }
    }
}
