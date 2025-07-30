<?php

namespace Database\Seeders;

use App\Models\CatalogoTipoDocumento;
use Illuminate\Database\Seeder;

class CatalogoTipoDocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiposDocumento = [
            // Documentos de vehículos
            [
                'nombre_tipo_documento' => 'CV Profesional',
                'descripcion' => 'Curriculum Vitae del empleado',
                'requiere_vencimiento' => false,
            ],
            [
                'nombre_tipo_documento' => 'Tarjeta de Circulación',
                'descripcion' => 'Documento oficial que acredita la propiedad y características del vehículo',
                'requiere_vencimiento' => true,
            ],
            [
                'nombre_tipo_documento' => 'Póliza de Seguro',
                'descripcion' => 'Póliza de seguro de responsabilidad civil y/o cobertura amplia',
                'requiere_vencimiento' => true,
            ],
            [
                'nombre_tipo_documento' => 'Verificación Vehicular',
                'descripcion' => 'Certificado de verificación de emisiones contaminantes',
                'requiere_vencimiento' => true,
            ],
            [
                'nombre_tipo_documento' => 'Tenencia Vehicular',
                'descripcion' => 'Comprobante de pago de tenencia vehicular',
                'requiere_vencimiento' => true,
            ],
            [
                'nombre_tipo_documento' => 'Factura de Compra',
                'descripcion' => 'Factura original de compra del vehículo',
                'requiere_vencimiento' => false,
            ],
            [
                'nombre_tipo_documento' => 'Manual del Vehículo',
                'descripcion' => 'Manual de usuario y mantenimiento del vehículo',
                'requiere_vencimiento' => false,
            ],

            // Documentos de personal
            [
                'nombre_tipo_documento' => 'Licencia de Conducir',
                'descripcion' => 'Licencia vigente para operar vehículos',
                'requiere_vencimiento' => true,
            ],
            [
                'nombre_tipo_documento' => 'Identificación Oficial',
                'descripcion' => 'INE, pasaporte u otra identificación oficial vigente',
                'requiere_vencimiento' => true,
            ],
            [
                'nombre_tipo_documento' => 'CURP',
                'descripcion' => 'Clave Única de Registro de Población',
                'requiere_vencimiento' => false,
            ],
            [
                'nombre_tipo_documento' => 'RFC',
                'descripcion' => 'Registro Federal de Contribuyentes',
                'requiere_vencimiento' => false,
            ],
            [
                'nombre_tipo_documento' => 'NSS',
                'descripcion' => 'Número de Seguro Social',
                'requiere_vencimiento' => false,
            ],
            [
                'nombre_tipo_documento' => 'Comprobante de Estudios',
                'descripcion' => 'Certificado, diploma o título de estudios',
                'requiere_vencimiento' => false,
            ],
            [
                'nombre_tipo_documento' => 'Certificado Médico',
                'descripcion' => 'Certificado médico de aptitud para el trabajo',
                'requiere_vencimiento' => true,
            ],
            [
                'nombre_tipo_documento' => 'Contrato de Trabajo',
                'descripcion' => 'Contrato laboral firmado',
                'requiere_vencimiento' => false,
            ],
            [
                'nombre_tipo_documento' => 'CV Profesional',
                'descripcion' => 'Curriculum Vitae del empleado',
                'requiere_vencimiento' => false,
            ],
            [
                'nombre_tipo_documento' => 'Comprobante de Domicilio',
                'descripcion' => 'Comprobante de domicilio vigente',
                'requiere_vencimiento' => false,
            ],

            // Documentos de obras
            [
                'nombre_tipo_documento' => 'Permiso de Construcción',
                'descripcion' => 'Permiso municipal o estatal para realizar la obra',
                'requiere_vencimiento' => true,
            ],
            [
                'nombre_tipo_documento' => 'Licencia de Uso de Suelo',
                'descripcion' => 'Licencia que autoriza el uso específico del terreno',
                'requiere_vencimiento' => true,
            ],
            [
                'nombre_tipo_documento' => 'Planos Arquitectónicos',
                'descripcion' => 'Planos técnicos aprobados de la obra',
                'requiere_vencimiento' => false,
            ],
            [
                'nombre_tipo_documento' => 'Manifestación de Impacto Ambiental',
                'descripcion' => 'Documento de evaluación de impacto ambiental',
                'requiere_vencimiento' => false,
            ],
            [
                'nombre_tipo_documento' => 'Contrato de Obra',
                'descripcion' => 'Contrato principal de la obra',
                'requiere_vencimiento' => false,
            ],

            // Documentos de mantenimiento
            [
                'nombre_tipo_documento' => 'Orden de Servicio',
                'descripcion' => 'Orden de trabajo para mantenimiento',
                'requiere_vencimiento' => false,
            ],
            [
                'nombre_tipo_documento' => 'Factura de Servicio',
                'descripcion' => 'Factura del servicio de mantenimiento',
                'requiere_vencimiento' => false,
            ],
            [
                'nombre_tipo_documento' => 'Garantía de Servicio',
                'descripcion' => 'Garantía del trabajo realizado',
                'requiere_vencimiento' => true,
            ],
            [
                'nombre_tipo_documento' => 'Reporte de Inspección',
                'descripcion' => 'Reporte técnico de inspección pre/post servicio',
                'requiere_vencimiento' => false,
            ],

            // Documentos generales
            [
                'nombre_tipo_documento' => 'Póliza de Fianza',
                'descripcion' => 'Póliza de fianza para garantizar cumplimiento',
                'requiere_vencimiento' => true,
            ],
            [
                'nombre_tipo_documento' => 'Certificado de Calidad',
                'descripcion' => 'Certificado de cumplimiento de estándares de calidad',
                'requiere_vencimiento' => true,
            ],
            [
                'nombre_tipo_documento' => 'Acta de Entrega-Recepción',
                'descripcion' => 'Documento de entrega y recepción de bienes o servicios',
                'requiere_vencimiento' => false,
            ],
        ];

        foreach ($tiposDocumento as $tipo) {
            CatalogoTipoDocumento::create($tipo);
        }
    }
}
