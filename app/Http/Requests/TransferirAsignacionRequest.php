<?php

namespace App\Http\Requests;

use App\Models\Asignacion;
use App\Models\Personal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class TransferirAsignacionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->hasPermission('gestionar_asignaciones');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'nuevo_operador_id' => [
                'required',
                'integer',
                'exists:personal,id',
                function ($attribute, $value, $fail) {
                    // Validar que el nuevo operador esté activo
                    $personal = Personal::find($value);
                    if (! $personal || $personal->estatus !== 'activo') {
                        $fail('El operador seleccionado no está activo.');
                    }

                    // Validar que el nuevo operador no tenga asignaciones activas
                    if ($personal && $personal->asignaciones()->whereNull('fecha_liberacion')->exists()) {
                        $fail('El operador seleccionado ya tiene una asignación activa.');
                    }

                    // Validar que no sea el mismo operador actual
                    $asignacion = Asignacion::find($this->route('id'));
                    if ($asignacion && $asignacion->personal_id == $value) {
                        $fail('No se puede transferir a the mismo operador actual.');
                    }
                },
            ],
            'motivo_transferencia' => 'required|string|max:500',
            'kilometraje_transferencia' => [
                'required',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) {
                    $asignacion = Asignacion::find($this->route('id'));
                    if ($asignacion && $value < $asignacion->kilometraje_inicial) {
                        $fail('El kilometraje de transferencia no puede ser menor al kilometraje inicial de la asignación.');
                    }
                },
            ],
            'observaciones_transferencia' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'nuevo_operador_id.required' => 'Debe seleccionar un operador para la transferencia',
            'nuevo_operador_id.exists' => 'El operador seleccionado no existe',
            'motivo_transferencia.required' => 'El motivo de transferencia es obligatorio',
            'motivo_transferencia.max' => 'El motivo no debe exceder 500 caracteres',
            'kilometraje_transferencia.required' => 'El kilometraje de transferencia es obligatorio',
            'kilometraje_transferencia.min' => 'El kilometraje debe ser mayor a 0',
            'observaciones_transferencia.max' => 'Las observaciones no deben exceder 1000 caracteres',
        ];
    }

    /**
     * Get custom attributes for validation errors.
     */
    public function attributes(): array
    {
        return [
            'nuevo_operador_id' => 'nuevo operador',
            'motivo_transferencia' => 'motivo de transferencia',
            'kilometraje_transferencia' => 'kilometraje de transferencia',
            'observaciones_transferencia' => 'observaciones',
        ];
    }
}
