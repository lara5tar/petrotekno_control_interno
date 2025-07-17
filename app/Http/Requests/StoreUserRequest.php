<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // La autorización se maneja en el controlador
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre_usuario' => [
                'required',
                'string',
                'max:255',
                'unique:users,nombre_usuario'
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email'
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:255'
            ],
            'rol_id' => [
                'required',
                'integer',
                'exists:roles,id'
            ],
            'personal_id' => [
                'nullable',
                'integer',
                'exists:personal,id'
            ]
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'nombre_usuario.max' => 'El nombre de usuario no puede exceder 255 caracteres.',
            'email.max' => 'El email no puede exceder 255 caracteres.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.max' => 'La contraseña no puede exceder 255 caracteres.',
            'nombre_usuario.unique' => 'Este nombre de usuario ya está en uso.',
            'email.unique' => 'Este email ya está registrado.',
            'rol_id.exists' => 'El rol seleccionado no existe.',
            'personal_id.exists' => 'El personal seleccionado no existe.'
        ];
    }
}
