<?php

namespace App\Http\Requests;

use App\Models\Asignacion;
use App\Models\Obra;
use App\Models\Vehiculo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateAsignacionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->hasPermission('editar_asignaciones');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $asignacionId = $this->route('asignacion') ?? $this->route('id');

        return [
            'vehiculo_id' => [
                'sometimes',
                'integer',
                'exists:vehiculos,id',
                function ($attribute, $value, $fail) use ($asignacionId) {
                    // Validar que el vehículo no tenga otra asignación activa
                    $existeOtraAsignacion = Asignacion::where('vehiculo_id', $value)
                        ->where('id', '!=', $asignacionId)
                        ->activas()
                        ->exists();

                    if ($existeOtraAsignacion) {
                        $fail('El vehículo seleccionado ya tiene otra asignación activa.');
                    }

                    // Validar que el vehículo no esté en mantenimiento
                    $vehiculo = Vehiculo::find($value);
                    if ($vehiculo && $vehiculo->mantenimientos()->whereNull('fecha_fin')->exists()) {
                        $fail('No se puede asignar un vehículo que está en mantenimiento.');
                    }
                },
            ],
            'obra_id' => [
                'sometimes',
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
                'sometimes',
                'integer',
                'exists:personal,id',
                function ($attribute, $value, $fail) use ($asignacionId) {
                    // Validar que el operador no tenga otra asignación activa
                    $existeOtraAsignacion = Asignacion::where('personal_id', $value)
                        ->where('id', '!=', $asignacionId)
                        ->activas()
                        ->exists();

                    if ($existeOtraAsignacion) {
                        $fail('El operador seleccionado ya tiene otra asignación activa.');
                    }
                },
            ],
            'fecha_asignacion' => [
                'sometimes',
                'date',
                'before_or_equal:now',
            ],
            'kilometraje_inicial' => [
                'sometimes',
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
            'vehiculo_id.exists' => 'El vehículo seleccionado no existe.',
            'obra_id.exists' => 'La obra seleccionada no existe.',
            'personal_id.exists' => 'El operador seleccionado no existe.',
            'fecha_asignacion.date' => 'La fecha de asignación debe ser una fecha válida.',
            'fecha_asignacion.before_or_equal' => 'La fecha de asignación no puede ser posterior al momento actual.',
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
