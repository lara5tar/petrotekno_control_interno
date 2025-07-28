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
            // Validaciones para documentos específicos
            'tarjeta_circulacion_file' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120', // 5MB máximo
            ],
            'no_tarjeta_circulacion' => [
                'nullable',
                'string',
                'max:100',
            ],
            'fecha_vencimiento_tarjeta' => [
                'nullable',
                'date',
                'after:today',
            ],
            'tenencia_vehicular_file' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120', // 5MB máximo
            ],
            'no_tenencia_vehicular' => [
                'nullable',
                'string',
                'max:100',
            ],
            'fecha_vencimiento_tenencia' => [
                'nullable',
                'date',
                'after:today',
            ],
            'verificacion_vehicular_file' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120', // 5MB máximo
            ],
            'no_verificacion_vehicular' => [
                'nullable',
                'string',
                'max:100',
            ],
            'fecha_vencimiento_verificacion' => [
                'nullable',
                'date',
                'after:today',
            ],
            'manual_vehiculo_file' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120', // 5MB máximo
            ],
            'poliza_seguro_file' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120', // 5MB máximo
            ],
            'no_poliza_seguro' => [
                'nullable',
                'string',
                'max:100',
            ],
            'fecha_vencimiento_seguro' => [
                'nullable',
                'date',
                'after:today',
            ],
            'aseguradora' => [
                'nullable',
                'string',
                'max:100',
            ],
            'factura_compra_file' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120', // 5MB máximo
            ],
            'no_factura_compra' => [
                'nullable',
                'string',
                'max:100',
            ],
            'fotografia_file' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png',
                'max:5120', // 5MB máximo
            ],
            // Validaciones para documentos adicionales estructurados
            'documentos_adicionales_tipos' => [
                'nullable',
                'array',
            ],
            'documentos_adicionales_tipos.*' => [
                'required_with:documentos_adicionales_archivos.*',
                'integer',
                'exists:catalogo_tipo_documento,id',
            ],
            'documentos_adicionales_descripciones' => [
                'nullable',
                'array',
            ],
            'documentos_adicionales_descripciones.*' => [
                'nullable',
                'string',
                'max:500',
            ],
            'documentos_adicionales_fechas_vencimiento' => [
                'nullable',
                'array',
            ],
            'documentos_adicionales_fechas_vencimiento.*' => [
                'nullable',
                'date',
                'after:today',
            ],
            'documentos_adicionales_archivos' => [
                'nullable',
                'array',
            ],
            'documentos_adicionales_archivos.*' => [
                'required_with:documentos_adicionales_tipos.*',
                'file',
                'mimes:pdf,doc,docx,jpg,jpeg,png,webp',
                'max:10240', // 10MB máximo por archivo
            ],
            // Validaciones para documentos adicionales (legacy)
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
            'marca.required' => 'La marca del vehículo es obligatoria',
            'marca.string' => 'La marca debe ser un texto válido',
            'marca.min' => 'La marca debe tener al menos 2 caracteres',
            'marca.max' => 'La marca no puede tener más de 50 caracteres',
            
            'modelo.required' => 'El modelo del vehículo es obligatorio',
            'modelo.string' => 'El modelo debe ser un texto válido',
            'modelo.min' => 'El modelo debe tener al menos 2 caracteres',
            'modelo.max' => 'El modelo no puede tener más de 100 caracteres',
            
            'anio.required' => 'El año del vehículo es obligatorio',
            'anio.integer' => 'El año debe ser un número entero',
            'anio.min' => 'El año debe ser mayor o igual a 1990',
            'anio.max' => 'El año no puede ser mayor al próximo año',
            
            'n_serie.required' => 'El número de serie es obligatorio',
            'n_serie.string' => 'El número de serie debe ser un texto válido',
            'n_serie.max' => 'El número de serie no puede exceder 100 caracteres',
            'n_serie.unique' => 'Este número de serie ya está registrado en el sistema',
            
            'placas.required' => 'Las placas del vehículo son obligatorias',
            'placas.string' => 'Las placas deben ser un texto válido',
            'placas.max' => 'Las placas no pueden exceder 20 caracteres',
            'placas.unique' => 'Estas placas ya están registradas en el sistema',
            'placas.regex' => 'Las placas solo pueden contener letras, números y guiones',
            
            'estatus_id.required' => 'El estatus del vehículo es obligatorio',
            'estatus_id.integer' => 'El estatus debe ser un número entero',
            'estatus_id.exists' => 'El estatus seleccionado no es válido',
            
            'kilometraje_actual.required' => 'El kilometraje actual es obligatorio',
            'kilometraje_actual.integer' => 'El kilometraje debe ser un número entero',
            'kilometraje_actual.min' => 'El kilometraje no puede ser negativo',
            'kilometraje_actual.max' => 'El kilometraje excede el límite permitido',
            
            'intervalo_km_motor.integer' => 'El intervalo de motor debe ser un número entero',
            'intervalo_km_motor.min' => 'El intervalo de motor debe ser al menos 1,000 km',
            'intervalo_km_motor.max' => 'El intervalo de motor no puede exceder 50,000 km',
            
            'intervalo_km_transmision.integer' => 'El intervalo de transmisión debe ser un número entero',
            'intervalo_km_transmision.min' => 'El intervalo de transmisión debe ser al menos 10,000 km',
            'intervalo_km_transmision.max' => 'El intervalo de transmisión no puede exceder 200,000 km',
            
            'intervalo_km_hidraulico.integer' => 'El intervalo hidráulico debe ser un número entero',
            'intervalo_km_hidraulico.min' => 'El intervalo hidráulico debe ser al menos 5,000 km',
            'intervalo_km_hidraulico.max' => 'El intervalo hidráulico no puede exceder 100,000 km',
            
            'observaciones.string' => 'Las observaciones deben ser un texto válido',
            'observaciones.max' => 'Las observaciones no pueden exceder 1,000 caracteres',
            
            // Mensajes para documentos específicos
            'tarjeta_circulacion_file.file' => 'La tarjeta de circulación debe ser un archivo válido',
            'tarjeta_circulacion_file.mimes' => 'La tarjeta de circulación debe ser de tipo: pdf, jpg, jpeg, png',
            'tarjeta_circulacion_file.max' => 'La tarjeta de circulación no puede exceder 5MB',
            'no_tarjeta_circulacion.string' => 'El número de tarjeta de circulación debe ser un texto válido',
            'no_tarjeta_circulacion.max' => 'El número de tarjeta de circulación no puede exceder 100 caracteres',
            'fecha_vencimiento_tarjeta.date' => 'La fecha de vencimiento de la tarjeta debe ser una fecha válida',
            'fecha_vencimiento_tarjeta.after' => 'La fecha de vencimiento de la tarjeta debe ser posterior a hoy',
            
            'tenencia_vehicular_file.file' => 'La tenencia vehicular debe ser un archivo válido',
            'tenencia_vehicular_file.mimes' => 'La tenencia vehicular debe ser de tipo: pdf, jpg, jpeg, png',
            'tenencia_vehicular_file.max' => 'La tenencia vehicular no puede exceder 5MB',
            'no_tenencia_vehicular.string' => 'El número de tenencia vehicular debe ser un texto válido',
            'no_tenencia_vehicular.max' => 'El número de tenencia vehicular no puede exceder 100 caracteres',
            'fecha_vencimiento_tenencia.date' => 'La fecha de vencimiento de la tenencia debe ser una fecha válida',
            'fecha_vencimiento_tenencia.after' => 'La fecha de vencimiento de la tenencia debe ser posterior a hoy',
            
            'verificacion_vehicular_file.file' => 'La verificación vehicular debe ser un archivo válido',
            'verificacion_vehicular_file.mimes' => 'La verificación vehicular debe ser de tipo: pdf, jpg, jpeg, png',
            'verificacion_vehicular_file.max' => 'La verificación vehicular no puede exceder 5MB',
            'no_verificacion_vehicular.string' => 'El número de verificación vehicular debe ser un texto válido',
            'no_verificacion_vehicular.max' => 'El número de verificación vehicular no puede exceder 100 caracteres',
            'fecha_vencimiento_verificacion.date' => 'La fecha de vencimiento de la verificación debe ser una fecha válida',
            'fecha_vencimiento_verificacion.after' => 'La fecha de vencimiento de la verificación debe ser posterior a hoy',
            
            'manual_vehiculo_file.file' => 'El manual del vehículo debe ser un archivo válido',
            'manual_vehiculo_file.mimes' => 'El manual del vehículo debe ser de tipo: pdf, jpg, jpeg, png',
            'manual_vehiculo_file.max' => 'El manual del vehículo no puede exceder 5MB',
            
            'poliza_seguro_file.file' => 'La póliza de seguro debe ser un archivo válido',
            'poliza_seguro_file.mimes' => 'La póliza de seguro debe ser de tipo: pdf, jpg, jpeg, png',
            'poliza_seguro_file.max' => 'La póliza de seguro no puede exceder 5MB',
            'no_poliza_seguro.string' => 'El número de póliza debe ser un texto válido',
            'no_poliza_seguro.max' => 'El número de póliza no puede exceder 100 caracteres',
            'fecha_vencimiento_seguro.date' => 'La fecha de vencimiento del seguro debe ser una fecha válida',
            'fecha_vencimiento_seguro.after' => 'La fecha de vencimiento del seguro debe ser posterior a hoy',
            'aseguradora.string' => 'El nombre de la aseguradora debe ser un texto válido',
            'aseguradora.max' => 'El nombre de la aseguradora no puede exceder 100 caracteres',
            
            'factura_compra_file.file' => 'La factura de compra debe ser un archivo válido',
            'factura_compra_file.mimes' => 'La factura de compra debe ser de tipo: pdf, jpg, jpeg, png',
            'factura_compra_file.max' => 'La factura de compra no puede exceder 5MB',
            'no_factura_compra.string' => 'El número de factura de compra debe ser un texto válido',
            'no_factura_compra.max' => 'El número de factura de compra no puede exceder 100 caracteres',
            
            'fotografia_file.file' => 'La fotografía debe ser un archivo válido',
            'fotografia_file.mimes' => 'La fotografía debe ser de tipo: jpg, jpeg, png',
            'fotografia_file.max' => 'La fotografía no puede exceder 5MB',
            
            // Mensajes para documentos adicionales estructurados
            'documentos_adicionales_tipos.array' => 'Los tipos de documentos adicionales deben ser un arreglo válido',
            'documentos_adicionales_tipos.*.required_with' => 'El tipo de documento es obligatorio cuando se sube un archivo',
            'documentos_adicionales_tipos.*.integer' => 'El tipo de documento debe ser un número entero',
            'documentos_adicionales_tipos.*.exists' => 'El tipo de documento seleccionado no es válido',
            
            'documentos_adicionales_descripciones.array' => 'Las descripciones de documentos adicionales deben ser un arreglo válido',
            'documentos_adicionales_descripciones.*.string' => 'La descripción del documento debe ser un texto válido',
            'documentos_adicionales_descripciones.*.max' => 'La descripción del documento no puede exceder 500 caracteres',
            
            'documentos_adicionales_fechas_vencimiento.array' => 'Las fechas de vencimiento deben ser un arreglo válido',
            'documentos_adicionales_fechas_vencimiento.*.date' => 'La fecha de vencimiento debe ser una fecha válida',
            'documentos_adicionales_fechas_vencimiento.*.after' => 'La fecha de vencimiento debe ser posterior a hoy',
            
            'documentos_adicionales_archivos.array' => 'Los archivos de documentos adicionales deben ser un arreglo válido',
            'documentos_adicionales_archivos.*.required_with' => 'El archivo es obligatorio cuando se especifica un tipo de documento',
            'documentos_adicionales_archivos.*.file' => 'Cada documento adicional debe ser un archivo válido',
            'documentos_adicionales_archivos.*.mimes' => 'Los documentos adicionales deben ser de tipo: pdf, doc, docx, jpg, jpeg, png, webp',
            'documentos_adicionales_archivos.*.max' => 'Cada documento adicional no puede exceder 10MB',
            
            // Mensajes para documentos adicionales (legacy)
            'documentos_adicionales.*.file' => 'Cada documento adicional debe ser un archivo válido',
            'documentos_adicionales.*.mimes' => 'Los documentos adicionales deben ser de tipo: pdf, doc, docx, jpg, jpeg, png, webp',
            'documentos_adicionales.*.max' => 'Cada documento adicional no puede exceder 10MB',
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
