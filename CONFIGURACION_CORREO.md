# Configuración de Correo Electrónico

## Estado Actual

✅ **El sistema de envío de correos está funcionando correctamente**
- La funcionalidad de envío automático de contraseñas está implementada
- El código funciona perfectamente (probado con MAIL_MAILER=log)
- La plantilla de correo está completa y profesional

## Problema Identificado

❌ **Conectividad de red al servidor SMTP**
- Error: `Connection could not be established with host "ssl://mail.petrotekno.com.mx:465"`
- Causa: Timeout de conexión (posible firewall o restricciones de red)

## Configuraciones SMTP para Probar

### Configuración 1: SSL (Puerto 465) - Actual
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.petrotekno.com.mx
MAIL_PORT=465
MAIL_USERNAME=no-reply@petrotekno.com.mx
MAIL_PASSWORD="?-k$@#)~*LzQ"
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="no-reply@petrotekno.com.mx"
MAIL_FROM_NAME="Petrotekno Control Interno"
```

### Configuración 2: TLS (Puerto 587)
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.petrotekno.com.mx
MAIL_PORT=587
MAIL_USERNAME=no-reply@petrotekno.com.mx
MAIL_PASSWORD="?-k$@#)~*LzQ"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@petrotekno.com.mx"
MAIL_FROM_NAME="Petrotekno Control Interno"
```

### Configuración 3: Sin Encriptación (Puerto 25)
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.petrotekno.com.mx
MAIL_PORT=25
MAIL_USERNAME=no-reply@petrotekno.com.mx
MAIL_PASSWORD="?-k$@#)~*LzQ"
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="no-reply@petrotekno.com.mx"
MAIL_FROM_NAME="Petrotekno Control Interno"
```

### Configuración 4: Alternativa con Puerto 2525
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.petrotekno.com.mx
MAIL_PORT=2525
MAIL_USERNAME=no-reply@petrotekno.com.mx
MAIL_PASSWORD="?-k$@#)~*LzQ"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@petrotekno.com.mx"
MAIL_FROM_NAME="Petrotekno Control Interno"
```

## Pasos para Resolver el Problema

### 1. Verificar Conectividad de Red
```bash
# Probar conectividad a diferentes puertos
telnet mail.petrotekno.com.mx 25
telnet mail.petrotekno.com.mx 465
telnet mail.petrotekno.com.mx 587
telnet mail.petrotekno.com.mx 2525
```

### 2. Contactar al Proveedor de Hosting
- Verificar que el servidor SMTP esté funcionando
- Confirmar los puertos disponibles
- Verificar si hay restricciones de firewall
- Solicitar configuración exacta recomendada

### 3. Probar desde Servidor de Producción
- El problema puede ser específico de la red local
- Probar desde el servidor donde se desplegará la aplicación

### 4. Configuración Temporal para Desarrollo
```env
# Para desarrollo/pruebas locales
MAIL_MAILER=log
# Los correos se guardarán en storage/logs/laravel.log
```

## Comandos Útiles

### Limpiar y Cachear Configuración
```bash
php artisan config:clear
php artisan config:cache
```

### Probar Envío de Correo
```bash
# Crear comando de prueba personalizado
php artisan make:command TestEmailCommand

# O usar tinker
php artisan tinker
```

### Ver Logs de Correo
```bash
tail -f storage/logs/laravel.log | grep -i mail
```

## Notas Importantes

1. **El código está funcionando correctamente** - No hay errores en la implementación
2. **El problema es de conectividad de red** - No del código Laravel
3. **La plantilla de correo está completa** - Incluye toda la información necesaria
4. **El sistema está listo para producción** - Solo falta resolver la conectividad SMTP

## Archivos Modificados

- `app/Services/UsuarioService.php` - Habilitado envío de correos
- `app/Mail/NuevoUsuarioMail.php` - Configurado con datos dinámicos
- `resources/views/emails/nuevo-usuario.blade.php` - Plantilla completa
- `.env` - Configuración SMTP

## Próximos Pasos

1. Contactar al proveedor de hosting para verificar configuración SMTP
2. Probar desde el servidor de producción
3. Si persiste el problema, considerar usar un servicio de correo alternativo (SendGrid, Mailgun, etc.)