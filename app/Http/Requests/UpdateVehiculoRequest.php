<?php

namespace App\Http\Requests;

use App\Enums\EstadoVehiculo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateVehiculoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return Auth::check() && $user && $user->hasPermission('editar_vehiculos');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $vehiculoId = $this->route('vehiculo') ?? $this->route('id');

        return [
            'tipo_activo_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:tipo_activos,id',
            ],
            'marca' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                'min:2',
            ],
            'modelo' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                'min:2',
            ],
            'anio' => [
                'sometimes',
                'nullable',
                'integer',
                'min:1990',
                'max:' . (date('Y') + 1),
            ],
            'n_serie' => [
                'sometimes',
                'required',
                'string',
                'max:100',
            ],
            'placas' => [
                'sometimes',
                'nullable',
                'string',
                'max:20',
                'regex:/^[A-Z0-9\-]+$/',
            ],
            'estatus' => [
                'sometimes',
                'required',
                'string',
                Rule::in(array_column(EstadoVehiculo::cases(), 'value')),
            ],
            'kilometraje_actual' => [
                'sometimes',
                'required',
                'integer',
                'min:0',
                'max:9999999',
            ],
            'intervalo_km_motor' => [
                'nullable',
                'integer',
                'min:100',
                'max:100000',
            ],
            'intervalo_km_transmision' => [
                'nullable',
                'integer',
                'min:100',
                'max:500000',
            ],
            'intervalo_km_hidraulico' => [
                'nullable',
                'integer',
                'min:100',
                'max:200000',
            ],
            'observaciones' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'estado' => [
                'nullable',
                'string',
                'max:100',
            ],
            'municipio' => [
                'nullable',
                'string',
                'max:100',
            ],
            'numero_poliza' => [
                'nullable',
                'string',
                'max:100',
            ],
            // Validaciones para documentos adicionales
            'documentos_adicionales.*' => [
                'nullable',
                'file',
                'mimes:pdf,doc,docx,jpg,jpeg,png,webp',
                'max:10240', // 10MB máximo por archivo
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'tipo_activo_id.required' => 'El tipo de activo es obligatorio.',
            'tipo_activo_id.integer' => 'El tipo de activo debe ser un número entero.',
            'tipo_activo_id.exists' => 'El tipo de activo seleccionado no es válido.',
            'marca.required' => 'La marca del vehículo es obligatoria.',
            'marca.min' => 'La marca debe tener al menos 2 caracteres.',
            'marca.max' => 'La marca no puede exceder 50 caracteres.',
            'modelo.required' => 'El modelo del vehículo es obligatorio.',
            'modelo.min' => 'El modelo debe tener al menos 2 caracteres.',
            'modelo.max' => 'El modelo no puede exceder 100 caracteres.',
            'anio.min' => 'El año debe ser mayor o igual a 1990.',
            'anio.max' => 'El año no puede ser mayor al próximo año.',
            'n_serie.required' => 'El número de serie es obligatorio.',
            'n_serie.max' => 'El número de serie no puede exceder 100 caracteres.',
            'placas.regex' => 'Las placas solo pueden contener letras, números y guiones.',
            'placas.max' => 'Las placas no pueden exceder 20 caracteres.',
            'estatus.required' => 'El estatus del vehículo es obligatorio.',
            'estatus.in' => 'El estatus seleccionado no es válido.',
            'kilometraje_actual.required' => 'El kilometraje actual es obligatorio.',
            'kilometraje_actual.min' => 'El kilometraje no puede ser negativo.',
            'kilometraje_actual.max' => 'El kilometraje excede el límite permitido.',
            'intervalo_km_motor.min' => 'El intervalo de motor debe ser al menos 100 km.',
            'intervalo_km_motor.max' => 'El intervalo de motor no puede exceder 100,000 km.',
            'intervalo_km_transmision.min' => 'El intervalo de transmisión debe ser al menos 100 km.',
            'intervalo_km_transmision.max' => 'El intervalo de transmisión no puede exceder 500,000 km.',
            'intervalo_km_hidraulico.min' => 'El intervalo hidráulico debe ser al menos 100 km.',
            'intervalo_km_hidraulico.max' => 'El intervalo hidráulico no puede exceder 200,000 km.',
            'observaciones.max' => 'Las observaciones no pueden exceder 1,000 caracteres.',
            'estado.max' => 'El estado no puede exceder 100 caracteres.',
            'municipio.max' => 'El municipio no puede exceder 100 caracteres.',
            // Mensajes para documentos adicionales
            'documentos_adicionales.*.file' => 'Cada documento adicional debe ser un archivo válido.',
            'documentos_adicionales.*.mimes' => 'Los documentos adicionales deben ser de tipo: pdf, doc, docx, jpg, jpeg, png, webp.',
            'documentos_adicionales.*.max' => 'Cada documento adicional no puede exceder 10MB.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('placas')) {
            $this->merge([
                'placas' => strtoupper($this->placas),
            ]);
        }

        if ($this->has('marca')) {
            $this->merge([
                'marca' => ucwords(strtolower($this->marca)),
            ]);
        }

        if ($this->has('modelo')) {
            $this->merge([
                'modelo' => ucwords(strtolower($this->modelo)),
            ]);
        }
    }
}
