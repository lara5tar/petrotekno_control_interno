<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfiguracionDestinatariosRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Ajustar según permisos requeridos
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'emails_principales' => [
                'required',
                'array',
                'min:1',
                'max:10' // Máximo 10 emails principales
            ],
            'emails_principales.*' => [
                'email:rfc,dns',
                'max:255',
                'distinct' // No duplicados
            ],
            'emails_copia' => [
                'nullable',
                'array',
                'max:20' // Máximo 20 emails en copia
            ],
            'emails_copia.*' => [
                'email:rfc,dns',
                'max:255',
                'distinct' // No duplicados
            ],
            'notificar_inmediato' => [
                'sometimes',
                'boolean'
            ],
            'incluir_en_copia_diaria' => [
                'sometimes',
                'boolean'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'emails_principales.required' => 'Debe especificar al menos un email principal.',
            'emails_principales.min' => 'Debe especificar al menos un email principal.',
            'emails_principales.max' => 'No puede especificar más de 10 emails principales.',
            'emails_principales.*.email' => 'El formato del email principal no es válido.',
            'emails_principales.*.distinct' => 'No puede duplicar emails en la lista principal.',
            'emails_copia.max' => 'No puede especificar más de 20 emails en copia.',
            'emails_copia.*.email' => 'El formato del email en copia no es válido.',
            'emails_copia.*.distinct' => 'No puede duplicar emails en la lista de copia.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'emails_principales' => 'emails principales',
            'emails_principales.*' => 'email principal',
            'emails_copia' => 'emails en copia',
            'emails_copia.*' => 'email en copia',
            'notificar_inmediato' => 'notificación inmediata',
            'incluir_en_copia_diaria' => 'incluir en copia diaria',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validar que no hay duplicados entre emails principales y copia
            $principales = $this->input('emails_principales', []);
            $copia = $this->input('emails_copia', []);

            if ($principales && $copia) {
                $duplicados = array_intersect($principales, $copia);
                if (!empty($duplicados)) {
                    $validator->errors()->add(
                        'emails_copia',
                        'Los emails en copia no pueden duplicarse con los principales: ' . implode(', ', $duplicados)
                    );
                }
            }

            // Validar total de emails únicos (máximo 25)
            $todosLosEmails = array_unique(array_merge($principales, $copia));
            if (count($todosLosEmails) > 25) {
                $validator->errors()->add(
                    'emails_principales',
                    'El total de emails únicos no puede exceder 25.'
                );
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Limpiar y normalizar emails
        if ($this->has('emails_principales')) {
            $this->merge([
                'emails_principales' => array_filter(
                    array_map('trim', array_map('strtolower', $this->input('emails_principales', [])))
                )
            ]);
        }

        if ($this->has('emails_copia')) {
            $this->merge([
                'emails_copia' => array_filter(
                    array_map('trim', array_map('strtolower', $this->input('emails_copia', [])))
                )
            ]);
        }
    }
}
