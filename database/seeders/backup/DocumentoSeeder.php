<?php

namespace Database\Seeders;

use App\Models\Documento;
use App\Models\CatalogoTipoDocumento;
use App\Models\Personal;
use App\Models\Vehiculo;
use App\Models\Obra;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener datos necesarios
        $tiposDocumento = CatalogoTipoDocumento::all();
        $personal = Personal::all();
        $vehiculos = Vehiculo::all();
        $obras = Obra::all();

        // Verificar que existan datos necesarios
        if ($tiposDocumento->isEmpty() || $personal->isEmpty() || $vehiculos->isEmpty() || $obras->isEmpty()) {
            $this->command->warn('⚠️ Faltan datos necesarios para crear documentos.');
            return;
        }

        // Documentos básicos
        $documentos = [
            [
                'tipo_documento_id' => $tiposDocumento->first()->id,
                'personal_id' => $personal->first()->id,
                'descripcion' => 'Documento de personal - Licencia de conducir',
                'fecha_vencimiento' => Carbon::now()->addMonths(12),
                'ruta_archivo' => 'documentos/personal/licencia.pdf',
            ],
            [
                'tipo_documento_id' => $tiposDocumento->first()->id,
                'vehiculo_id' => $vehiculos->first()->id,
                'descripcion' => 'Documento de vehículo - Tarjeta de circulación',
                'fecha_vencimiento' => Carbon::now()->addMonths(24),
                'ruta_archivo' => 'documentos/vehiculos/tarjeta.pdf',
            ],
            [
                'tipo_documento_id' => $tiposDocumento->first()->id,
                'obra_id' => $obras->first()->id,
                'descripcion' => 'Documento de obra - Permiso de construcción',
                'ruta_archivo' => 'documentos/obras/permiso.pdf',
            ],
        ];

        foreach ($documentos as $documentoData) {
            Documento::create($documentoData);
        }

        // Crear algunos documentos adicionales usando factory
        Documento::factory(10)->create();

        // Mostrar estadísticas
        $this->command->info('✅ Documentos creados exitosamente.');
        $this->command->info('📄 Total documentos: ' . Documento::count());
        $this->command->info('👥 Documentos de personal: ' . Documento::whereNotNull('personal_id')->count());
        $this->command->info('🚗 Documentos de vehículos: ' . Documento::whereNotNull('vehiculo_id')->count());
        $this->command->info('🏗️ Documentos de obras: ' . Documento::whereNotNull('obra_id')->count());
    }
}