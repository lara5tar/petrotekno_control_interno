#!/bin/bash

file="app/Http/Controllers/MantenimientoController.php"

echo "Arreglando controlador MantenimientoController..."

# Eliminar 'tipoServicio' de los with() y load()
sed -i '' "s/, 'tipoServicio'//g" "$file"
sed -i '' "s/'tipoServicio', //g" "$file"
sed -i '' "s/\['vehiculo', 'tipoServicio'\]/['vehiculo']/g" "$file"

# Reemplazar referencias a tipoServicio->nombre_tipo_servicio con tipo_servicio
sed -i '' 's/tipoServicio->nombre_tipo_servicio/tipo_servicio/g' "$file"

echo "Controlador MantenimientoController arreglado."
