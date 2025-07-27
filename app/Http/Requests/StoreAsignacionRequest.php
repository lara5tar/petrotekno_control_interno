<?php

namespace App\Http\Requests;

use App\Models\Obra;
use App\Models\Vehiculo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreAsignacionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->hasPermission('crear_asignaciones');
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
                function ($attribute, $value, $fail) {
                    // Validar que el vehículo no tenga asignación activa
                    if (\App\Models\Asignacion::vehiculoTieneAsignacionActiva($value)) {
                        $fail('El vehículo seleccionado ya tiene una asignación activa.');
                    }

                    // Validar que el vehículo no esté en mantenimiento
                    $vehiculo = Vehiculo::find($value);
                    if ($vehiculo && $vehiculo->mantenimientos()->whereNull('fecha_fin')->exists()) {
                        $fail('No se puede asignar un vehículo que está en mantenimiento.');
                    }
                },
            ],
            'obra_id' => [
                'required',
                'integer',
                'exists:obras,id',
                function ($attribute, $value, $fail) {
                    // Validar que la obra no esté cancelada o completada
                    $obra = Obra::find($value);
                    if ($obra && in_array($obra->estatus, [Obra::ESTATUS_CANCELADA, Obra::ESTATUS_COMPLETADA])) {
                        $fail('No se puede asignar a una obra cancelada o completada.');
                    }
                },
            ],
            'personal_id' => [
                'required',
                'integer',
                'exists:personal,id',
                function ($attribute, $value, $fail) {
                    // Validar que el operador no tenga asignación activa
                    if (\App\Models\Asignacion::operadorTieneAsignacionActiva($value)) {
                        $fail('El operador seleccionado ya tiene una asignación activa.');
                    }
                },
            ],
            'fecha_asignacion' => [
                'required',
                'date',
                'before_or_equal:now',
            ],
            'kilometraje_inicial' => [
                'required',
                'integer',
                'min:0',
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
            'vehiculo_id.required' => 'Debe seleccionar un vehículo.',
            'vehiculo_id.exists' => 'El vehículo seleccionado no existe.',
            'obra_id.required' => 'Debe seleccionar una obra.',
            'obra_id.exists' => 'La obra seleccionada no existe.',
            'personal_id.required' => 'Debe seleccionar un operador.',
            'personal_id.exists' => 'El operador seleccionado no existe.',
            'fecha_asignacion.required' => 'La fecha de asignación es obligatoria.',
            'fecha_asignacion.date' => 'La fecha de asignación debe ser una fecha válida.',
            'fecha_asignacion.before_or_equal' => 'La fecha de asignación no puede ser posterior al momento actual.',
            'kilometraje_inicial.required' => 'El kilometraje inicial es obligatorio.',
            'kilometraje_inicial.integer' => 'El kilometraje inicial debe ser un número entero.',
            'kilometraje_inicial.min' => 'El kilometraje inicial no puede ser negativo.',
            'observaciones.string' => 'Las observaciones deben ser texto.',
            'observaciones.max' => 'Las observaciones no pueden exceder 1000 caracteres.',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'vehiculo_id' => 'vehículo',
            'obra_id' => 'obra',
            'personal_id' => 'operador',
            'fecha_asignacion' => 'fecha de asignación',
            'kilometraje_inicial' => 'kilometraje inicial',
            'observaciones' => 'observaciones',
        ];
    }
}
