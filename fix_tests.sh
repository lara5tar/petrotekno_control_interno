#!/bin/bash

# Archivos de tests a modificar
files=(
    "tests/Feature/MantenimientoTest.php"
    "tests/Feature/MantenimientoBoundaryTest.php" 
    "tests/Feature/MantenimientoControllerHybridTest.php"
    "tests/Feature/MantenimientoSecurityTest.php"
)

for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        echo "Procesando $file..."
        
        # Eliminar imports de CatalogoTipoServicio
        sed -i '' '/use.*CatalogoTipoServicio/d' "$file"
        
        # Reemplazar propiedades de CatalogoTipoServicio
        sed -i '' '/private.*CatalogoTipoServicio/d' "$file"
        sed -i '' '/protected.*CatalogoTipoServicio/d' "$file"
        
        # Reemplazar creación de factories de CatalogoTipoServicio
        sed -i '' 's/CatalogoTipoServicio::factory()->create([^)]*)/'\''CORRECTIVO'\''/g' "$file"
        sed -i '' 's/CatalogoTipoServicio::factory()->create()/'\''CORRECTIVO'\''/g' "$file"
        
        # Reemplazar referencias a tipoServicio
        sed -i '' 's/$this->tipoServicio->id/'\''CORRECTIVO'\''/g' "$file"
        sed -i '' 's/$this->tipoServicio/'\''CORRECTIVO'\''/g' "$file"
        sed -i '' 's/$tipoServicio->id/'\''CORRECTIVO'\''/g' "$file"
        sed -i '' 's/$tipoServicio2->id/'\''PREVENTIVO'\''/g' "$file"
        
        # Reemplazar líneas de asignación de tipoServicio
        sed -i '' '/this->tipoServicio.*=/d' "$file"
        sed -i '' '/tipoServicio.*=.*CatalogoTipoServicio/d' "$file"
        
        # Reemplazar instanciaciones
        sed -i '' 's/CatalogoTipoServicio::class/Mantenimiento::class/g' "$file"
        
        # Reemplazar relaciones
        sed -i '' 's/mantenimiento->tipoServicio/mantenimiento->tipo_servicio/g' "$file"
        
        echo "Completado $file"
    else
        echo "Archivo no encontrado: $file"
    fi
done

echo "Todos los archivos procesados."
