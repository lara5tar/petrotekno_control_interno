<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TiposDocumentoSeeder extends Seeder
{
    /**
     * Crear los tipos de documentos básicos para el personal.
     * Estos son los documentos que se pueden asociar al personal del sistema.
     */
    public function run(): void
    {
        $this->command->info('📄 Creando tipos de documentos...');

        // Verificar si ya existen tipos de documento para evitar duplicados
        $existingTypes = DB::table('catalogo_tipos_documento')->count();
        
        if ($existingTypes > 0) {
            $this->command->warn('⚠️ Los tipos de documento ya existen, omitiendo...');
            return;
        }

        $tiposDocumento = [
            [
                'nombre_tipo_documento' => 'INE',
                'descripcion' => 'Credencial para Votar (INE)',
                'requiere_vencimiento' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_tipo_documento' => 'CURP',
                'descripcion' => 'Clave Única de Registro de Población',
                'requiere_vencimiento' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_tipo_documento' => 'RFC',
                'descripcion' => 'Registro Federal de Contribuyentes',
                'requiere_vencimiento' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_tipo_documento' => 'NSS',
                'descripcion' => 'Número de Seguro Social',
                'requiere_vencimiento' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_tipo_documento' => 'Contrato',
                'descripcion' => 'Contrato de Trabajo',
                'requiere_vencimiento' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_tipo_documento' => 'Certificado Médico',
                'descripcion' => 'Certificado Médico de Aptitud',
                'requiere_vencimiento' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_tipo_documento' => 'Comprobante Domicilio',
                'descripcion' => 'Comprobante de Domicilio',
                'requiere_vencimiento' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_tipo_documento' => 'Fotografía',
                'descripcion' => 'Fotografía del Personal',
                'requiere_vencimiento' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('catalogo_tipos_documento')->insert($tiposDocumento);

        $this->command->info('✅ Tipos de documentos creados exitosamente:');
        $this->command->info('   🔸 INE (Con vencimiento)');
        $this->command->info('   🔸 CURP');
        $this->command->info('   🔸 RFC');
        $this->command->info('   🔸 NSS');
        $this->command->info('   🔸 Contrato (Con vencimiento)');
        $this->command->info('   🔸 Certificado Médico (Con vencimiento)');
        $this->command->info('   🔸 Comprobante Domicilio');
        $this->command->info('   🔸 Fotografía');
    }
}
