$tenencia = App\Models\CatalogoTipoDocumento::where('nombre_tipo_documento', 'Tenencia Vehicular')->first();
if (!$tenencia) {
    $tenencia = App\Models\CatalogoTipoDocumento::create([
        'nombre_tipo_documento' => 'Tenencia Vehicular',
        'descripcion' => 'Documento de tenencia vehicular o derecho vehicular'
    ]);
    echo 'CREADO: Tenencia Vehicular (ID: ' . $tenencia->id . ')';
} else {
    echo 'YA EXISTE: Tenencia Vehicular (ID: ' . $tenencia->id . ')';
}
echo "\n";
echo "Todos los tipos de documento:\n";
App\Models\CatalogoTipoDocumento::all()->each(function($tipo) {
    echo "- ID: {$tipo->id}, Nombre: {$tipo->nombre_tipo_documento}\n";
});