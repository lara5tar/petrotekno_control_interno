# 📧 Guía de MAIL_TEST_RECIPIENTS

Este documento explica cómo configurar y usar la variable `MAIL_TEST_RECIPIENTS` para gestionar destinatarios de correos de prueba y alertas de mantenimiento.

## 🔧 **Configuración**

### **1. Variable de Entorno**

Agrega la siguiente variable en tu archivo `.env`:

```env
MAIL_TEST_RECIPIENTS="email1@dominio.com,email2@dominio.com,email3@dominio.com"
```

**Ejemplo:**
```env
MAIL_TEST_RECIPIENTS="ebravotube@gmail.com,eder.parajuegos@gmail.com"
```

### **2. Características**

- ✅ **Separar por comas**: Múltiples emails separados por comas
- ✅ **Validación automática**: Solo emails válidos son procesados
- ✅ **Eliminación de espacios**: Se limpian automáticamente los espacios
- ✅ **Fallback seguro**: Si no está configurado, se usan valores por defecto

---

## 📬 **Comportamiento del Sistema**

### **Para Alertas de Producción**

Cuando el sistema envía alertas **reales** de mantenimiento:

```
Destinatarios = Emails de Configuración del Usuario + MAIL_TEST_RECIPIENTS
```

- **Emails de configuración**: Los que el usuario agrega desde el frontend
- **Emails de prueba**: Los definidos en `MAIL_TEST_RECIPIENTS`
- **Resultado**: Se envía a ambos grupos (sin duplicados)

### **Para Pruebas del Sistema**

Cuando se ejecutan **pruebas** o **tests**:

```
Destinatarios = Solo MAIL_TEST_RECIPIENTS
```

- Solo se usan los emails de la variable `MAIL_TEST_RECIPIENTS`
- No se incluyen emails de configuración del usuario
- Ideal para desarrollo y testing

---

## 🛠️ **Implementación Técnica**

### **1. Servicio Principal**

**Archivo:** `app/Services/AlertasMantenimientoService.php`

```php
/**
 * Obtener lista completa de destinatarios (configuración + prueba)
 */
public static function obtenerDestinatarios(): array
{
    $destinatarios = [];

    // 1. Emails de configuración del usuario
    $configuracion = ConfiguracionAlerta::where('activo', true)->first();
    if ($configuracion && !empty($configuracion->emails_principales)) {
        $emailsConfiguracion = is_array($configuracion->emails_principales) 
            ? $configuracion->emails_principales 
            : json_decode($configuracion->emails_principales, true) ?? [];
        
        $destinatarios = array_merge($destinatarios, $emailsConfiguracion);
    }

    // 2. Emails de prueba desde .env
    $emailsPrueba = self::obtenerEmailsPrueba();
    $destinatarios = array_merge($destinatarios, $emailsPrueba);

    // 3. Limpiar y validar
    $destinatarios = array_filter(array_unique($destinatarios), function($email) {
        return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
    });

    return array_values($destinatarios);
}

/**
 * Obtener emails de prueba desde .env
 */
public static function obtenerEmailsPrueba(): array
{
    $emailsPrueba = env('MAIL_TEST_RECIPIENTS', '');
    
    if (empty($emailsPrueba)) {
        return [];
    }

    $emails = array_map('trim', explode(',', $emailsPrueba));
    
    return array_filter($emails, function($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    });
}
```

### **2. Job de Envío**

**Archivo:** `app/Jobs/EnviarAlertaMantenimiento.php`

```php
private function obtenerDestinatarios(): array
{
    // Si es test y se proporcionaron emails específicos, usar esos
    if ($this->esTest && !empty($this->emailsTest)) {
        return $this->emailsTest;
    }

    // Si es test sin emails específicos, usar solo emails de prueba
    if ($this->esTest) {
        return AlertasMantenimientoService::obtenerEmailsPrueba();
    }

    // Para alertas reales, usar todos los destinatarios (configuración + prueba)
    return AlertasMantenimientoService::obtenerDestinatarios();
}
```

---

## 🧪 **Testing**

### **1. Tests Automatizados**

Los tests del sistema usan automáticamente los emails de `MAIL_TEST_RECIPIENTS`:

```php
private function getTestEmails(): array
{
    $emailsPrueba = env('MAIL_TEST_RECIPIENTS', 'test@example.com,test2@example.com');
    return array_map('trim', explode(',', $emailsPrueba));
}
```

### **2. Comandos de Prueba**

**Envío de prueba específico:**
```bash
php artisan test:enviar-correo ebravotube@gmail.com --mailer=smtp --sync
```

**Envío usando MAIL_TEST_RECIPIENTS:**
```bash
# Las pruebas automáticamente usarán los emails configurados
php artisan test tests/Feature/EmailSystemTest.php
```

---

## 🔍 **API Endpoints**

### **Probar Envío sin Email Específico**

**Endpoint:** `POST /api/configuracion-alertas/probar-envio`

```json
{
    "enviar_real": true,
    "mailer": "resend"
}
```

**Comportamiento:**
- Si no se proporciona `email`, usa todos los emails de `MAIL_TEST_RECIPIENTS`
- Envía correos de prueba a cada email configurado
- Perfecto para verificar que la configuración funciona

### **Probar Envío a Email Específico**

```json
{
    "email": "admin@empresa.com",
    "enviar_real": true,
    "mailer": "resend"
}
```

**Comportamiento:**
- Envía solo al email específico proporcionado
- Útil para pruebas dirigidas

---

## 📊 **Logs y Monitoreo**

### **Logs Exitosos**

```json
{
    "destinatario": "ebravotube@gmail.com",
    "total_alertas": 2,
    "vehiculos_afectados": 2,
    "es_test": true,
    "emails_destino_configurados": [
        "admin@empresa.com",
        "ebravotube@gmail.com", 
        "eder.parajuegos@gmail.com"
    ]
}
```

### **Información de Destinatarios**

```json
{
    "total_destinatarios": 3,
    "destinatarios": [
        "admin@empresa.com",
        "ebravotube@gmail.com",
        "eder.parajuegos@gmail.com"
    ]
}
```

---

## ✅ **Ventajas del Sistema**

### **1. Flexibilidad**
- ✅ Configuración fácil desde `.env`
- ✅ Combinación automática de emails de usuario y prueba
- ✅ Separación clara entre producción y testing

### **2. Seguridad**
- ✅ Validación automática de emails
- ✅ Eliminación de duplicados
- ✅ Fallback seguro si la configuración falla

### **3. Testing**
- ✅ Tests automatizados usan emails de prueba
- ✅ No interfiere con configuración de usuario
- ✅ Ideal para desarrollo y CI/CD

### **4. Producción**
- ✅ Emails de prueba siempre incluidos en alertas reales
- ✅ Usuario puede agregar emails adicionales desde frontend
- ✅ Sistema garantiza que siempre hay destinatarios

---

## 🔧 **Configuración Recomendada**

### **Desarrollo Local**
```env
MAIL_TEST_RECIPIENTS="developer@empresa.com,qa@empresa.com"
```

### **Staging/Testing**
```env
MAIL_TEST_RECIPIENTS="staging@empresa.com,testing@empresa.com"
```

### **Producción**
```env
MAIL_TEST_RECIPIENTS="admin@empresa.com,backup@empresa.com"
```

---

## 🚨 **Troubleshooting**

### **No se envían emails**

1. **Verificar configuración:**
   ```bash
   php artisan tinker
   \App\Services\AlertasMantenimientoService::obtenerEmailsPrueba()
   ```

2. **Verificar emails válidos:**
   ```bash
   php artisan tinker
   \App\Services\AlertasMantenimientoService::obtenerDestinatarios()
   ```

### **Emails duplicados**

- El sistema automáticamente elimina duplicados
- Verifica que no haya emails repetidos en configuración y `.env`

### **Tests fallan**

- Asegúrate de que `MAIL_TEST_RECIPIENTS` esté configurado en `.env`
- Verifica que los emails sean válidos
- Ejecuta tests específicos: `php artisan test --filter="emails_prueba"`

---

## 📖 **Referencias**

- **Documentación principal:** `docs/SISTEMA_ALERTAS_MANTENIMIENTO_FRONTEND_GUIDE.md`
- **API Documentation:** `docs/MANTENIMIENTOS_API_DOCUMENTATION.md`
- **Tests:** `tests/Feature/EmailSystemTest.php`
- **Configuración anti-spam:** `docs/CONFIGURACION_GMAIL_ANTI_SPAM.md`
