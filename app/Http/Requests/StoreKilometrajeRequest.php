<?php

namespace App\Http\Requests;

use App\Models\Kilometraje;
use Illuminate\Foundation\Http\FormRequest;

class StoreKilometrajeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('crear_kilometrajes');
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
            'kilometraje' => [
                'required',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) {
                    $vehiculoId = $this->input('vehiculo_id');

                    if (! $vehiculoId) {
                        return; // Ya se validará el vehiculo_id requerido
                    }

                    // Validar que el kilometraje sea mayor al último registrado
                    $ultimoKilometraje = Kilometraje::getUltimoKilometraje($vehiculoId);

                    if ($ultimoKilometraje && $value <= $ultimoKilometraje->kilometraje) {
                        $fail("El kilometraje debe ser mayor al último registrado ({$ultimoKilometraje->kilometraje} km).");
                    }

                    // Validar que el kilometraje no sea excesivamente mayor (más de 100,000 km de diferencia)
                    if ($ultimoKilometraje && ($value - $ultimoKilometraje->kilometraje) > 100000) {
                        $fail('El incremento de kilometraje parece excesivo. Por favor, verifica el valor ingresado.');
                    }
                },
            ],
            'fecha_captura' => [
                'required',
                'date',
                'before_or_equal:today',
                'after:2020-01-01', // Evitar fechas muy antiguas
            ],
            'observaciones' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'usuario_captura_id' => [
                'required',
                'integer',
                'exists:users,id',
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
            'vehiculo_id.required' => 'Debe seleccionar un vehículo.',
            'vehiculo_id.exists' => 'El vehículo seleccionado no existe.',
            'kilometraje.required' => 'El kilometraje es obligatorio.',
            'kilometraje.integer' => 'El kilometraje debe ser un número entero.',
            'kilometraje.min' => 'El kilometraje no puede ser negativo.',
            'fecha_captura.required' => 'La fecha de captura es obligatoria.',
            'fecha_captura.date' => 'La fecha de captura debe tener un formato válido.',
            'fecha_captura.before_or_equal' => 'La fecha de captura no puede ser futura.',
            'fecha_captura.after' => 'La fecha de captura debe ser posterior a enero 2020.',
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
            'vehiculo_id' => 'vehículo',
            'kilometraje' => 'kilometraje',
            'fecha_captura' => 'fecha de captura',
            'observaciones' => 'observaciones',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Si no se especifica usuario_captura_id, usar el usuario autenticado
        if (! $this->has('usuario_captura_id')) {
            $this->merge([
                'usuario_captura_id' => $this->user()->id,
            ]);
        }

        // Limpiar kilometraje de comas y puntos para formato internacional
        if ($this->has('kilometraje')) {
            $kilometraje = str_replace([',', '.'], '', $this->input('kilometraje'));
            $this->merge(['kilometraje' => (int) $kilometraje]);
        }
    }
}
