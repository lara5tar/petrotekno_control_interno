# 🎯 Instrucciones: Cómo Probar la Búsqueda de Personal

## ✅ El error ha sido corregido

La búsqueda de personal ahora funciona correctamente después de corregir el problema con la columna `puesto` que no existía en la base de datos.

---

## 📋 Cómo Probar en el Navegador

### Paso 1: Acceder al sistema
1. Abre tu navegador
2. Ve a: `http://127.0.0.1:8000`
3. Inicia sesión con tu usuario

### Paso 2: Ir a Personal
1. En el menú principal, haz clic en **"Personal"**
2. Deberías ver el listado de personal

### Paso 3: Probar la búsqueda
1. En el campo de búsqueda (arriba de la tabla), escribe:
   - **"Admin"** - debería encontrar "Administrador Sistema"
   - **"Sistema"** - debería encontrar "Administrador Sistema"
   - Cualquier parte del nombre, RFC, NSS, INE, CURP, o categoría

### Paso 4: Verificar resultados
- ✅ Los resultados deberían aparecer **en tiempo real** mientras escribes
- ✅ No debería haber errores en la consola del navegador
- ✅ La tabla debería actualizarse automáticamente

---

## 🔍 Funcionalidades de Búsqueda

### Búsqueda en tiempo real
- Escribe mínimo **2 caracteres**
- Los resultados aparecen automáticamente después de 300ms
- No necesitas presionar Enter

### Campos de búsqueda
El sistema busca en:
1. **Nombre completo**
2. **RFC**
3. **NSS** (Número de Seguro Social)
4. **INE**
5. **CURP** ← Nuevo
6. **Número de licencia**
7. **Categoría**

### Filtros adicionales
También puedes usar los filtros:
- **Estado**: Activo / Inactivo
- **Tipo**: Selecciona una categoría específica

---

## 🧪 Pruebas desde Terminal (Opcional)

Si quieres ejecutar pruebas desde la línea de comandos:

```bash
# Prueba básica
php test-personal-search.php

# Prueba con autenticación
php test-personal-search-auth.php
```

---

## ⚠️ Si aún ves errores

### 1. Limpiar caché
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 2. Reiniciar servidor
Si usas `php artisan serve`:
1. Presiona `Ctrl + C` para detener el servidor
2. Ejecuta nuevamente: `php artisan serve`
3. Recarga la página en el navegador

### 3. Verificar consola del navegador
1. Presiona `F12` en el navegador
2. Ve a la pestaña **"Console"**
3. Verifica si hay errores JavaScript
4. Ve a la pestaña **"Network"**
5. Filtra por **"XHR"**
6. Busca la petición a `/personal/search`
7. Verifica que devuelva status **200** (OK)

---

## 📊 Respuesta Esperada

Cuando funciona correctamente, deberías ver en la Network tab:

**Request URL:** `http://127.0.0.1:8000/personal/search?buscar=Admin`

**Status:** `200 OK`

**Response JSON:**
```json
{
  "personal": [
    {
      "id": 1,
      "nombre_completo": "Administrador Sistema",
      "rfc": "",
      "nss": "",
      "ine": "",
      "curp_numero": null,
      "estatus": "activo",
      "categoria": "Admin",
      "categoria_id": 1,
      "created_at": "01/10/2025",
      "url": "http://127.0.0.1:8000/personal/1"
    }
  ],
  "total": 1,
  "limite_alcanzado": false,
  "mensaje": "Se encontraron 1 personas"
}
```

---

## 📝 Archivos Modificados

Solo se modificó **1 archivo**:
- `app/Http/Controllers/Api/PersonalSearchController.php`

**Cambios:**
- Removida búsqueda por columna `puesto` (no existe)
- Agregada búsqueda por columna `curp_numero` (existe)

---

## ✅ Confirmación

Si puedes:
- ✅ Escribir en el campo de búsqueda
- ✅ Ver resultados en tiempo real
- ✅ No ver errores en la consola
- ✅ Hacer clic en los resultados y ver los detalles

**¡Entonces todo está funcionando correctamente!** 🎉

---

**Fecha:** 1 de octubre de 2025  
**Estado:** ✅ FUNCIONANDO
