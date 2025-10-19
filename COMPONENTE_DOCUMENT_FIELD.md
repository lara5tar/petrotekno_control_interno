# Componente Document Field

## 📋 Descripción

El componente `document-field` es un componente reutilizable de Blade para mostrar campos de documentos de forma consistente en toda la aplicación.

## 📁 Ubicación

```
resources/views/components/document-field.blade.php
```

## 🎯 Propósito

Proporcionar una interfaz uniforme para mostrar documentos con:
- Iconos consistentes (azul cuando disponible, gris cuando no)
- Botones estandarizados (Ver en azul, Descargar en verde)
- Gestión automática de documentos desde la base de datos o URLs directas
- Diseño responsive y profesional

## 🔧 Parámetros

| Parámetro | Tipo | Requerido | Default | Descripción |
|-----------|------|-----------|---------|-------------|
| `title` | string | ✅ Sí | `''` | Título del documento (ej: "INE", "Licencia de Manejo") |
| `subtitle` | string\|null | ❌ No | `null` | HTML con información adicional (fechas, números, etc.) |
| `hasDocument` | boolean | ❌ No | `false` | Indica si existe un documento en la BD |
| `documentId` | int\|null | ❌ No | `null` | ID del documento para botones con data-attribute |
| `directUrl` | string\|null | ❌ No | `null` | URL directa del archivo (sin `storage/`) |
| `showDownload` | boolean | ❌ No | `true` | Mostrar/ocultar botón de descarga |

## 💡 Uso

### Ejemplo 1: Documento Simple

```blade
<x-document-field 
    title="INE"
    :hasDocument="true"
    :documentId="$documento->id"
/>
```

### Ejemplo 2: Con Subtítulo Personalizado

```blade
@php
    $subtitle = '<p class="text-xs text-gray-500">Vence: 31/12/2025</p>';
@endphp

<x-document-field 
    title="Licencia de Manejo"
    :subtitle="$subtitle"
    :hasDocument="true"
    :documentId="$documentoLicencia->id"
/>
```

### Ejemplo 3: Con URL Directa (sin documento en BD)

```blade
<x-document-field 
    title="Comprobante de Domicilio"
    :directUrl="$personal->url_comprobante_domicilio"
/>
```

### Ejemplo 4: Documento No Disponible

```blade
<x-document-field 
    title="CV Profesional"
/>
```

### Ejemplo 5: Uso Completo (Implementado en Personal)

```blade
@php
    $documentosObligatorios = [
        'INE' => ['titulo' => 'Identificación Oficial (INE)', 'url_field' => 'url_ine'],
        'CURP' => ['titulo' => 'CURP', 'url_field' => 'url_curp'],
        'RFC' => ['titulo' => 'RFC', 'url_field' => 'url_rfc'],
        'NSS' => ['titulo' => 'NSS (Número de Seguro Social)', 'url_field' => 'url_nss'],
    ];
@endphp

<ul class="divide-y divide-gray-200">
    @foreach($documentosObligatorios as $tipoDoc => $config)
        @php
            $documento = $documentosPorTipo[$tipoDoc] ?? null;
            $tieneDocumento = !is_null($documento) && is_object($documento);
            $urlField = $personal->{$config['url_field']} ?? null;
            
            $subtitle = null;
            if($tieneDocumento && isset($documento->fecha_vencimiento) && $documento->fecha_vencimiento) {
                $subtitle = '<p class="text-xs text-gray-500">Vence: ' . \Carbon\Carbon::parse($documento->fecha_vencimiento)->format('d/m/Y') . '</p>';
            }
        @endphp
        
        <x-document-field 
            :title="$config['titulo']"
            :subtitle="$subtitle"
            :hasDocument="$tieneDocumento"
            :documentId="$tieneDocumento ? $documento->id : null"
            :directUrl="$urlField"
        />
    @endforeach
</ul>
```

## 🎨 Estilos y Comportamiento

### Estados Visuales

1. **Documento Disponible** (desde BD o URL directa):
   - Icono azul (`text-blue-600`)
   - Botón "Ver" azul (`bg-blue-500`)
   - Botón "Descargar" verde (solo si `hasDocument=true`)

2. **Documento No Disponible**:
   - Icono gris (`text-gray-600`)
   - Texto "Faltante" en rojo
   - Sin botones

### Botones

- **Ver**: Siempre azul, abre el documento
  - Con `documentId`: Usa clases `.btn-view-document` para JavaScript
  - Con `directUrl`: Usa `onclick="viewPersonalDocument(url)"`

- **Descargar**: Siempre verde, descarga el documento
  - Solo aparece cuando `hasDocument=true` y `showDownload=true`
  - Usa clase `.btn-download-document` para JavaScript

## 🔄 Migración

### Antes (código duplicado):

```blade
<li class="py-3 flex items-center justify-between">
    <div class="flex items-center">
        <svg class="w-4 h-4 mr-2 text-{{ $hasDoc ? 'blue' : 'gray' }}-600">...</svg>
        <div>
            <span class="text-sm font-medium text-gray-800">INE</span>
            <p class="text-xs text-gray-500">Información adicional</p>
        </div>
    </div>
    @if($hasDoc)
        <div class="flex space-x-2">
            <button class="bg-blue-500...">Ver</button>
            <button class="bg-green-500...">Descargar</button>
        </div>
    @else
        <span class="text-xs text-red-500">Faltante</span>
    @endif
</li>
```

### Después (componente reutilizable):

```blade
<x-document-field 
    title="INE"
    :hasDocument="$hasDoc"
    :documentId="$documento->id"
/>
```

## ✅ Ventajas

1. **Código DRY**: Sin repetición de código HTML
2. **Mantenimiento**: Un solo lugar para actualizar el diseño
3. **Consistencia**: Todos los documentos se ven igual
4. **Flexibilidad**: Soporta múltiples fuentes de documentos
5. **Escalabilidad**: Fácil agregar nuevos campos de documentos

## 📊 Archivos Modificados

1. **Creado**: `resources/views/components/document-field.blade.php`
2. **Modificado**: `resources/views/personal/show.blade.php`
   - Reemplazados ~150 líneas de código duplicado
   - Ahora usa 7 llamadas al componente (INE, CURP, RFC, NSS, Licencia, Comprobante, CV)

## 🚀 Próximos Pasos

Este componente puede ser usado en:
- Módulo de Vehículos (documentos de vehículos)
- Módulo de Obras (documentos de proyectos)
- Cualquier otro módulo que requiera mostrar documentos

## 🔍 Notas Técnicas

- El componente usa `@props` de Blade para definir parámetros
- Soporta HTML en el parámetro `subtitle` usando `{!! !!}`
- Usa operador `??` para valores null-safe
- Compatible con JavaScript existente (clases `.btn-view-document` y `.btn-download-document`)
