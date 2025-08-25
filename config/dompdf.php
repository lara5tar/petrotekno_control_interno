<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Settings for DomPDF
    |--------------------------------------------------------------------------
    |
    | Here you can define all the settings for DomPDF
    |
    */
    'show_warnings' => false,   // Mostrar advertencias
    'orientation' => 'portrait',
    'convert_entities' => true,
    'size' => 'A4',
    'font_path' => storage_path('fonts/'), // Directorio para fuentes adicionales
    'font_cache' => storage_path('fonts/'),
    'temp_dir' => sys_get_temp_dir(),      // Directorio temporal para archivos
    'chroot' => realpath(base_path()),      // Directorio raíz para las imágenes
    'allowed_protocols' => [
        'file://' => ['rules' => []],
        'http://' => ['rules' => []],
        'https://' => ['rules' => []],
    ],
    'log_output_file' => null,
    'enable_font_subsetting' => false,
    'pdf_backend' => 'CPDF',
    'default_media_type' => 'screen',
    'default_paper_size' => 'a4',
    'default_paper_orientation' => 'portrait',
    'default_font' => 'serif',
    'dpi' => 96,
    'enable_php' => false,
    'enable_javascript' => true,
    'enable_remote' => true,        // IMPORTANTE: Permitir carga de imágenes remotas
    'font_height_ratio' => 1.1,
    'enable_html5_parser' => false,
];
