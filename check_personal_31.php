<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Personal;

echo "Checking documents for Personal ID 31...\n";

$personal = Personal::with([
    'categoria', 
    'usuario',
    'documentos' => function ($query) {
        $query->with('tipoDocumento')
              ->select('id', 'tipo_documento_id', 'descripcion', 'fecha_vencimiento', 'personal_id', 'contenido', 'created_at', 'updated_at');
    }
])->find(31);

if ($personal) {
    echo "Personal found: {$personal->nombre_completo}\n";
    echo "Total documents: " . $personal->documentos->count() . "\n\n";
    
    // Simulate the same logic as in PersonalController
    $documentosPorTipo = [];
    $tiposDocumentoMap = [
        8 => 'identificacion',  // Identificación Oficial
        9 => 'curp',           // CURP
        10 => 'rfc',           // RFC
        11 => 'nss',           // NSS
        7 => 'licencia',       // Licencia de Conducir
        15 => 'cv',            // CV Profesional
        16 => 'domicilio'      // Comprobante de Domicilio
    ];
    
    foreach ($personal->documentos as $documento) {
        echo "Processing document ID: {$documento->id}\n";
        echo "Document type: " . gettype($documento) . "\n";
        echo "Document class: " . get_class($documento) . "\n";
        
        $tipoId = $documento->tipo_documento_id;
        if (isset($tiposDocumentoMap[$tipoId])) {
            $campo = $tiposDocumentoMap[$tipoId];
            $documentosPorTipo[$campo] = $documento;
            echo "Assigned to field: {$campo}\n";
        }
        
        // Mantener compatibilidad con nombres antiguos para la vista
        $tipoNombre = $documento->tipoDocumento->nombre_tipo_documento;
        if ($tipoNombre === 'Identificación Oficial') {
            $documentosPorTipo['INE'] = $documento;
            echo "Also assigned to INE\n";
        } else {
            $documentosPorTipo[$tipoNombre] = $documento;
            echo "Assigned to: {$tipoNombre}\n";
        }
        echo "---\n";
    }
    
    echo "\nFinal documentosPorTipo array:\n";
    foreach ($documentosPorTipo as $key => $doc) {
        echo "Key: {$key}, Type: " . gettype($doc) . "\n";
        if (is_object($doc)) {
            echo "  Object class: " . get_class($doc) . "\n";
            echo "  Has tipoDocumento: " . (isset($doc->tipoDocumento) ? 'Yes' : 'No') . "\n";
        }
    }
    
    // Test the specific check from the view
    if(isset($documentosPorTipo['identificacion']) || isset($documentosPorTipo['INE'])) {
        $doc = $documentosPorTipo['identificacion'] ?? $documentosPorTipo['INE'];
        echo "\nTesting view logic:\n";
        echo "Doc type: " . gettype($doc) . "\n";
        if (is_object($doc)) {
            echo "Doc class: " . get_class($doc) . "\n";
            echo "Has tipoDocumento: " . (isset($doc->tipoDocumento) ? 'Yes' : 'No') . "\n";
            if (isset($doc->tipoDocumento)) {
                echo "tipoDocumento type: " . gettype($doc->tipoDocumento) . "\n";
                echo "tipoDocumento class: " . get_class($doc->tipoDocumento) . "\n";
            }
        }
    }
} else {
    echo "Personal with ID 31 not found\n";
}