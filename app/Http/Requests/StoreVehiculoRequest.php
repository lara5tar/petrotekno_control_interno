<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreVehiculoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return Auth::check() && $user && $user->hasPermission('crear_vehiculos');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'marca' => [
                'required',
                'string',
                'max:50',
                'min:2',
            ],
            'modelo' => [
                'required',
                'string',
                'max:100',
                'min:2',
            ],
            'anio' => [
                'required',
                'integer',
                'min:1990',
                'max:' . (date('Y') + 1),
            ],
            'n_serie' => [
                'required',
                'string',
                'max:100',
                'unique:vehiculos,n_serie',
            ],
            'placas' => [
                'required',
                'string',
                'max:20',
                'unique:vehiculos,placas',
                'regex:/^[A-Z0-9\-]+$/',
            ],
            'estatus_id' => [
                'required',
                'integer',
                'exists:catalogo_estatus,id',
            ],
            'kilometraje_actual' => [
                'required',
                'integer',
                'min:0',
                'max:9999999',
            ],
            'intervalo_km_motor' => [
                'nullable',
                'integer',
                'min:1000',
                'max:50000',
            ],
            'intervalo_km_transmision' => [
                'nullable',
                'integer',
                'min:10000',
                'max:200000',
            ],
            'intervalo_km_hidraulico' => [
                'nullable',
                'integer',
                'min:5000',
                'max:100000',
            ],
            'observaciones' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'marca.required' => 'La marca del vehículo es obligatoria.',
            'marca.min' => 'La marca debe tener al menos 2 caracteres.',
            'marca.max' => 'La marca no puede exceder 50 caracteres.',
            'modelo.required' => 'El modelo del vehículo es obligatorio.',
            'modelo.min' => 'El modelo debe tener al menos 2 caracteres.',
            'modelo.max' => 'El modelo no puede exceder 100 caracteres.',
            'anio.required' => 'El año del vehículo es obligatorio.',
            'anio.min' => 'El año debe ser mayor o igual a 1990.',
            'anio.max' => 'El año no puede ser mayor al próximo año.',
            'n_serie.required' => 'El número de serie es obligatorio.',
            'n_serie.unique' => 'Este número de serie ya está registrado.',
            'n_serie.max' => 'El número de serie no puede exceder 100 caracteres.',
            'placas.required' => 'Las placas del vehículo son obligatorias.',
            'placas.unique' => 'Estas placas ya están registradas.',
            'placas.regex' => 'Las placas solo pueden contener letras, números y guiones.',
            'placas.max' => 'Las placas no pueden exceder 20 caracteres.',
            'estatus_id.required' => 'El estatus del vehículo es obligatorio.',
            'estatus_id.exists' => 'El estatus seleccionado no es válido.',
            'kilometraje_actual.required' => 'El kilometraje actual es obligatorio.',
            'kilometraje_actual.min' => 'El kilometraje no puede ser negativo.',
            'kilometraje_actual.max' => 'El kilometraje excede el límite permitido.',
            'intervalo_km_motor.min' => 'El intervalo de motor debe ser al menos 1,000 km.',
            'intervalo_km_motor.max' => 'El intervalo de motor no puede exceder 50,000 km.',
            'intervalo_km_transmision.min' => 'El intervalo de transmisión debe ser al menos 10,000 km.',
            'intervalo_km_transmision.max' => 'El intervalo de transmisión no puede exceder 200,000 km.',
            'intervalo_km_hidraulico.min' => 'El intervalo hidráulico debe ser al menos 5,000 km.',
            'intervalo_km_hidraulico.max' => 'El intervalo hidráulico no puede exceder 100,000 km.',
            'observaciones.max' => 'Las observaciones no pueden exceder 1,000 caracteres.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'placas' => strtoupper($this->placas ?? ''),
            'marca' => ucwords(strtolower($this->marca ?? '')),
            'modelo' => ucwords(strtolower($this->modelo ?? '')),
        ]);
    }
}
