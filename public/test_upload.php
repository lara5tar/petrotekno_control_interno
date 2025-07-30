<?php

// Mostrar errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
    $file = $_FILES['test_file'];
    
    if ($file['error'] === UPLOAD_ERR_INI_SIZE) {
        $message = '<div style="color: red;">Error: El archivo excede el tamaño máximo permitido por PHP (upload_max_filesize).</div>';
    } elseif ($file['error'] === UPLOAD_ERR_FORM_SIZE) {
        $message = '<div style="color: red;">Error: El archivo excede el tamaño máximo permitido por el formulario.</div>';
    } elseif ($file['error'] === UPLOAD_ERR_PARTIAL) {
        $message = '<div style="color: red;">Error: El archivo se subió parcialmente.</div>';
    } elseif ($file['error'] === UPLOAD_ERR_NO_FILE) {
        $message = '<div style="color: red;">Error: No se seleccionó ningún archivo.</div>';
    } elseif ($file['error'] === UPLOAD_ERR_NO_TMP_DIR) {
        $message = '<div style="color: red;">Error: Falta la carpeta temporal.</div>';
    } elseif ($file['error'] === UPLOAD_ERR_CANT_WRITE) {
        $message = '<div style="color: red;">Error: No se pudo escribir el archivo en el disco.</div>';
    } elseif ($file['error'] === UPLOAD_ERR_EXTENSION) {
        $message = '<div style="color: red;">Error: Una extensión de PHP detuvo la subida del archivo.</div>';
    } elseif ($file['error'] === UPLOAD_ERR_OK) {
        $message = '<div style="color: green;">¡Archivo subido correctamente!</div>';
        $message .= '<div>Nombre: ' . htmlspecialchars($file['name']) . '</div>';
        $message .= '<div>Tamaño: ' . number_format($file['size'] / 1024 / 1024, 2) . ' MB</div>';
        $message .= '<div>Tipo: ' . htmlspecialchars($file['type']) . '</div>';
    } else {
        $message = '<div style="color: red;">Error desconocido: ' . $file['error'] . '</div>';
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de Subida de Archivos</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        h1 { color: #333; }
        .info { background: #f5f5f5; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        form { background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        input[type="file"] { margin-bottom: 15px; }
        button { background: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #45a049; }
        .result { margin-top: 20px; padding: 15px; border-radius: 5px; background: #f9f9f9; }
    </style>
</head>
<body>
    <h1>Prueba de Subida de Archivos</h1>
    
    <div class="info">
        <h2>Configuración actual de PHP:</h2>
        <p>upload_max_filesize: <?php echo ini_get('upload_max_filesize'); ?></p>
        <p>post_max_size: <?php echo ini_get('post_max_size'); ?></p>
        <p>max_file_uploads: <?php echo ini_get('max_file_uploads'); ?></p>
    </div>
    
    <form action="" method="post" enctype="multipart/form-data">
        <h2>Subir archivo de prueba:</h2>
        <input type="file" name="test_file" required>
        <button type="submit">Subir Archivo</button>
    </form>
    
    <?php if ($message): ?>
    <div class="result">
        <h2>Resultado:</h2>
        <?php echo $message; ?>
    </div>
    <?php endif; ?>
</body>
</html>