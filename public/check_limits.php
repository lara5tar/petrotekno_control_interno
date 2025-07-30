<?php

echo "<h1>Configuración de PHP para subida de archivos</h1>";
echo "<p>upload_max_filesize: " . ini_get('upload_max_filesize') . "</p>";
echo "<p>post_max_size: " . ini_get('post_max_size') . "</p>";
echo "<p>max_file_uploads: " . ini_get('max_file_uploads') . "</p>";
echo "<p>max_input_time: " . ini_get('max_input_time') . "</p>";
echo "<p>max_execution_time: " . ini_get('max_execution_time') . "</p>";