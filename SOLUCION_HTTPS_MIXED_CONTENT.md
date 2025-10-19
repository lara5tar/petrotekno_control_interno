# 🔒 Solución: Error de Contenido Mixto HTTPS

## 📋 Problema

Al generar PDFs en el servidor `https://petrotekno.app/`, aparece el error:
```
Mixed Content: The site at 'https://petrotekno.app/' was loaded over a secure connection, 
but the file at 'https://petrotekno.app/vehiculos/descargar-reporte-pdf' was redirected 
through an insecure connection.
```

## 🔍 Causa

Laravel está generando URLs con `http://` en lugar de `https://` porque:
1. No detecta correctamente que está detrás de un proxy HTTPS (nginx/Cloudflare)
2. Los headers del proxy no están siendo confiados

## ✅ Solución Implementada

### 1. **Actualizado `AppServiceProvider.php`**

```php
public function boot(): void
{
    // Forzar HTTPS en producción o cuando APP_URL usa HTTPS
    if (app()->environment('production') || str_starts_with(config('app.url'), 'https://')) {
        URL::forceScheme('https');
        $this->app['request']->server->set('HTTPS', 'on');
    }
    
    // También detectar si estamos detrás de un proxy (como nginx)
    if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        URL::forceScheme('https');
        $this->app['request']->server->set('HTTPS', 'on');
    }
    
    // ... resto del código
}
```

### 2. **Creado `TrustProxies.php` Middleware**

Archivo: `app/Http/Middleware/TrustProxies.php`

```php
protected $proxies = '*'; // Confiar en todos los proxies
```

### 3. **Actualizado `bootstrap/app.php`**

```php
->withMiddleware(function (Middleware $middleware): void {
    // Confiar en proxies (importante para HTTPS detrás de nginx/cloudflare)
    $middleware->trustProxies(at: '*');
    
    // ... resto del código
})
```

## 🚀 Pasos para Aplicar en Servidor

### 1. **Subir archivos al servidor**

Sube los siguientes archivos modificados:
- `app/Providers/AppServiceProvider.php`
- `app/Http/Middleware/TrustProxies.php` (nuevo)
- `bootstrap/app.php`

### 2. **Verificar el `.env` en producción**

Asegúrate de que tu `.env` en el servidor tenga:

```env
APP_ENV=production
APP_URL=https://petrotekno.app
```

### 3. **Limpiar cachés en el servidor**

Ejecuta estos comandos en el servidor:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 4. **Verificar configuración de Nginx**

Tu configuración de nginx debe incluir estos headers:

```nginx
server {
    listen 443 ssl http2;
    server_name petrotekno.app;

    # Headers importantes para Laravel
    location / {
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_set_header X-Forwarded-Host $host;
        proxy_set_header X-Forwarded-Port $server_port;
        
        # Si usas PHP-FPM directamente
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # ... resto de configuración SSL
}
```

### 5. **Si usas Cloudflare**

En el panel de Cloudflare:
- **SSL/TLS** → Modo: `Full (strict)`
- **SSL/TLS** → Edge Certificates → Always Use HTTPS: `On`

## 🧪 Probar la Solución

1. **Limpia caché del navegador** (Ctrl+Shift+Del)
2. **Abre el sitio en modo incógnito**
3. **Genera un PDF** de vehículos
4. **Verifica en la consola** que no haya errores de Mixed Content

### Verificar URLs Generadas

Puedes verificar que Laravel genera URLs HTTPS correctamente:

```php
// En cualquier controlador, temporalmente:
dd(url('/'), config('app.url'), request()->secure());
```

Debería mostrar:
- `url('/')`: `https://petrotekno.app`
- `config('app.url')`: `https://petrotekno.app`
- `request()->secure()`: `true`

## 📊 Archivos Modificados

| Archivo | Acción | Propósito |
|---------|--------|-----------|
| `app/Providers/AppServiceProvider.php` | Modificado | Forzar HTTPS y detectar proxies |
| `app/Http/Middleware/TrustProxies.php` | Creado | Confiar en headers de proxies |
| `bootstrap/app.php` | Modificado | Registrar TrustProxies |

## ⚠️ Notas Importantes

1. **Solo funciona en HTTPS**: Estas configuraciones forzarán HTTPS, no funcionará en HTTP
2. **Proxies confiables**: `$proxies = '*'` confía en todos los proxies (apropiado para Cloudflare/nginx)
3. **Caché**: Siempre limpia caché después de cambios en configuración

## 🔄 Rollback (si algo sale mal)

Si necesitas revertir los cambios temporalmente, comenta estas líneas en `AppServiceProvider.php`:

```php
// URL::forceScheme('https');
// $this->app['request']->server->set('HTTPS', 'on');
```

Y ejecuta:
```bash
php artisan config:clear
php artisan cache:clear
```

## ✅ Resultado Esperado

Después de aplicar estos cambios:
- ✅ PDFs se generan sin errores de Mixed Content
- ✅ Todas las URLs generadas usan HTTPS
- ✅ Redirecciones funcionan correctamente
- ✅ Assets (CSS, JS, imágenes) cargan por HTTPS

---

**Fecha**: 16 de octubre de 2025  
**Servidor**: petrotekno.app  
**Laravel**: 11.x  
**Problema**: Solucionado
