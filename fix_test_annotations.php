<?php

// Script para convertir annotations @test a nueva sintaxis
$testDirectories = [
    'tests/Feature',
    'tests/Unit'
];

foreach ($testDirectories as $directory) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory)
    );
    
    foreach ($iterator as $file) {
        if ($file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            
            // Patron para encontrar /** @test */ seguido de function nombre()
            $pattern = '/\/\*\*\s*@test\s*\*\/\s*\n\s*public\s+function\s+([a-zA-Z_][a-zA-Z0-9_]*)\s*\(/';
            
            $newContent = preg_replace_callback($pattern, function($matches) {
                $functionName = $matches[1];
                
                // Si ya empieza con test_, no hacer nada
                if (strpos($functionName, 'test_') === 0) {
                    return $matches[0];
                }
                
                // Convertir a test_nombre_funcion
                $newFunctionName = 'test_' . $functionName;
                
                // Reemplazar sin el @test
                return "    public function {$newFunctionName}(";
            }, $content);
            
            if ($newContent !== $content) {
                file_put_contents($file->getPathname(), $newContent);
                echo "Procesado: " . $file->getPathname() . "\n";
            }
        }
    }
}

echo "Conversion completada!\n";
