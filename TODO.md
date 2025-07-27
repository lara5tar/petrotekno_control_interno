# TODO - Sistema de Control Interno

## 🔥 **CRÍTICO - Autenticación Web**
- [ ] Crear `app/Http/Controllers/Auth/LoginController.php` para web
- [ ] Crear `app/Http/Controllers/Auth/RegisterController.php` para web  
- [ ] Crear `app/Http/Controllers/Auth/LogoutController.php` para web
- [ ] Implementar middleware `auth:web` en rutas protegidas
- [ ] Configurar guards de autenticación en `config/auth.php`
- [ ] Crear vistas de login/register en `resources/views/auth/`
- [ ] Configurar redirecciones post-login/logout
- [ ] Implementar "Remember Me" functionality
- [ ] Configurar protección CSRF en formularios
- [ ] Testear flujo completo de autenticación web

## ✅ **COMPLETADO - Crear Personal con Usuario**
- [x] Conectar formulario web con PersonalManagementController
- [x] Adaptar CreatePersonalRequest para campos del formulario web
- [x] Implementar método storeWeb() para manejo de formularios web
- [x] Configurar validaciones para información básica y usuario
- [x] Actualizar ruta web para usar controlador real
- [x] Configurar rol por defecto (Operador) para usuarios nuevos

## 🚧 **PENDIENTE - Manejo de Archivos en Formulario Personal**
- [ ] Implementar procesamiento de archivos multipart/form-data en storeWeb()
- [ ] Configurar almacenamiento de documentos en storage/app/public/documentos/
- [ ] Crear validaciones específicas para tipos de archivo (PDF, JPG, PNG)
- [ ] Implementar límites de tamaño de archivo (5MB por archivo)
- [ ] Mapear campos de archivo del formulario a tipos de documento:
  - [ ] identificacion_file → Tipo "INE"
  - [ ] curp_file → Tipo "CURP"  
  - [ ] rfc_file → Tipo "RFC"
  - [ ] nss_file → Tipo "NSS"
  - [ ] licencia_file → Tipo "Licencia de Conducir"
  - [ ] comprobante_domicilio_file → Tipo "Comprobante de Domicilio"
  - [ ] cv_file → Tipo "CV Profesional"
- [ ] Crear helper para generar nombres únicos de archivos
- [ ] Implementar eliminación de archivos temporales en caso de error
- [ ] Agregar logging para subida de documentos
- [ ] Crear tests para validación y procesamiento de archivos

## 🔧 **MEJORAS TÉCNICAS**
- [ ] Implementar notificación por email con credenciales temporales
- [ ] Crear job en cola para envío de emails
- [ ] Implementar soft deletes para personal y usuarios
- [ ] Agregar auditoría de cambios en personal
- [ ] Crear API endpoints para gestión de documentos
- [ ] Implementar compresión automática de imágenes
- [ ] Agregar preview de documentos en interfaz web

## 📋 **VALIDACIONES ADICIONALES**
- [ ] Validar formato de CURP (18 caracteres alfanuméricos)
- [ ] Validar formato de RFC (10-13 caracteres)
- [ ] Validar formato de NSS (11 dígitos)
- [ ] Implementar validación de duplicados por CURP/RFC
- [ ] Agregar validación de email único en tiempo real
- [ ] Crear validaciones personalizadas para documentos mexicanos

## 🧪 **TESTING**
- [ ] Crear tests para PersonalManagementController::storeWeb()
- [ ] Tests de validación de formulario web
- [ ] Tests de creación de usuario asociado
- [ ] Tests de manejo de errores y rollback
- [ ] Tests de integración formulario-controlador-base de datos
- [ ] Tests de subida y validación de archivos
- [ ] Tests de seguridad para tipos de archivo maliciosos

## 🔴 Tareas Críticas

### Sistema de Alertas de Mantenimiento ✅ **COMPLETADO**
- [x] **#TODO**: Implementar sistema completo de alertas de mantenimiento automatizado ✅
- [x] **#TODO**: Crear sistema de notificaciones por email para mantenimientos vencidos ✅
- [x] **#TODO**: Implementar configuración flexible de destinatarios y horarios ✅
- [x] **#TODO**: Agregar campo sistema_vehiculo a mantenimientos (motor, transmisión, hidráulico) ✅
- [x] **#TODO**: Crear API endpoints para configuración desde frontend ✅
- [x] **#FIXME**: Corregir validaciones de tipo_servicio (cambio de ID a enum) ✅ **24/01/2025**

### Sistema de Asignaciones
- [x] **#TODO**: Implementar sistema de notificaciones automáticas cuando una asignación esté cerca de vencer (30 días sin liberación) ✅
- [x] **#FIXME**: Validar que el kilometraje_final sea mayor al kilometraje_inicial en el método liberar() ✅
- [x] **#TODO**: Agregar validación de negocio para no permitir asignaciones a obras canceladas o completadas ✅
- [x] **#TODO**: Implementar cálculo automático de próximo mantenimiento basado en kilometraje recorrido en asignaciones ✅

## 🟡 Mejoras de Funcionalidad

### Frontend - Sistema de Alertas de Mantenimiento 🚨 **ALTA PRIORIDAD**
- [ ] **#CRITICAL**: Actualizar campo `tipo_servicio` en formularios (cambio a enum)
- [ ] **#CRITICAL**: Agregar campo `sistema_vehiculo` en `create.blade.php` 
- [ ] **#CRITICAL**: Agregar campo `sistema_vehiculo` en `edit.blade.php`
- [ ] **#TODO**: Crear dashboard de alertas (`resources/views/alertas/dashboard.blade.php`)
- [ ] **#TODO**: Crear página de configuración (`resources/views/alertas/configuracion.blade.php`)
- [ ] **#TODO**: Implementar controller Blade para alertas
- [ ] **#TODO**: Agregar rutas web para sistema de alertas

### Documentación y Comunicación
- [ ] **#TODO**: Actualizar README.md con nuevos cambios estructurales
- [x] **#TODO**: Crear ejemplos de uso del campo `contenido` para diferentes tipos de documentos ✅
- [ ] **#TODO**: Documentar estrategias de migración de datos existentes
- [x] **#TODO**: Crear guía de buenas prácticas para el uso del campo JSON `contenido` ✅

### Asignaciones - Características avanzadas
- [x] **#TODO**: Implementar sistema de transferencia de asignaciones entre operadores ✅
- [x] **#TODO**: Agregar campo para registrar combustible inicial/final en asignaciones ✅
- [x] **#TODO**: Implementar alertas por tiempo excesivo de asignación (más de X días activa) ✅
- [x] **#TODO**: Crear endpoint para obtener estadísticas avanzadas de productividad por operador ✅

### Validaciones
- [x] **#FIXME**: Agregar validación para evitar asignar vehículos en mantenimiento ✅
- [x] **#TODO**: Validar que la fecha_asignacion no sea posterior a la fecha actual ✅
- [ ] **#TODO**: Implementar soft validation para asignaciones que excedan cierto kilometraje

## 🟢 Optimizaciones

### Performance
- [ ] **#TODO**: Agregar índices compuestos para consultas frecuentes de asignaciones activas
- [ ] **#TODO**: Implementar cache para consultas de estadísticas de asignaciones
- [ ] **#TODO**: Optimizar queries N+1 en las relaciones del controlador

### Reportes
- [ ] **#TODO**: Crear endpoint para generar reporte PDF de asignaciones por período
- [ ] **#TODO**: Implementar exportación a Excel de historial de asignaciones
- [ ] **#TODO**: Agregar dashboard en tiempo real de asignaciones activas

## 📋 Próximos Módulos

### Sistema de Kilometrajes
- [x] **#TODO**: Crear migración para tabla `kilometrajes` ✅
- [x] **#TODO**: Implementar modelo Kilometraje con validaciones ✅
- [x] **#TODO**: Crear controlador para registro de kilometrajes ✅
- [x] **#TODO**: Integrar kilometrajes con sistema de asignaciones ✅

### Integraciones
- [x] **#TODO**: Conectar asignaciones con sistema de mantenimientos ✅
- [x] **#TODO**: Implementar sincronización con sistema de documentos ✅
- [ ] **#TODO**: Crear API webhooks para notificar cambios de estado de asignaciones

## ✅ Completado

### Completado Recientemente (Julio 23, 2025) ✅

#### Sistema de Alertas de Mantenimiento Automatizado (NUEVO) 🚀
- [x] **#FEATURE**: Sistema completo de alertas de mantenimiento con intervalos automáticos
  - [x] Campo `sistema_vehiculo` agregado a mantenimientos (motor|transmision|hidraulico|general)
  - [x] Tabla `configuracion_alertas` para configuración runtime flexible
  - [x] Observer `MantenimientoObserver` para eventos automáticos
  - [x] Command `EnviarAlertasDiarias` para envío programado
  - [x] Job `RecalcularAlertasVehiculo` para procesamiento asíncrono
  - [x] Services: `ConfiguracionAlertasService` y `AlertasMantenimientoService`
  - [x] Controller `ConfiguracionAlertasController` con API completa
  - [x] Actualización automática de kilometraje de vehículos
  - [x] Sistema anti-spam con cooldown configurable
  - [x] Generación de reportes PDF (simulado, listo para templates reales)
  - [x] Documentación completa para integración frontend

#### Funcionalidad No Crítica Implementada
- [x] **#DOCS**: Documentación del campo `contenido` JSON con ejemplos prácticos
- [x] **#FEATURE**: Sistema de transferencia de asignaciones entre operadores
  - [x] Request de validación `TransferirAsignacionRequest`
  - [x] Método `transferir()` en AsignacionController
  - [x] Vista Blade `transferir.blade.php` 
  - [x] Rutas API y web para transferencia
- [x] **#FEATURE**: Campos de combustible en asignaciones
  - [x] Migración para agregar campos: `combustible_inicial`, `combustible_final`, `combustible_suministrado`, `costo_combustible`, `historial_combustible`
  - [x] Actualización del modelo Asignacion con fillable y casts
  - [x] Accessors para cálculos: `combustible_consumido`, `eficiencia_combustible`
  - [x] Métodos de negocio: `agregarCombustible()`, `getResumenCombustible()`

### Deuda Técnica Resuelta (Julio 23, 2025) ✅
- [x] **#FIXME**: Corregida foreign key en migración de documentos para tabla mantenimientos
- [x] **#TODO**: Implementado envío de email con credenciales para nuevos usuarios  
- [x] **#TODO**: Removido código temporal en PersonalManagementController (CreatePersonalRequest implementado)
- [x] **#TODO**: Implementada sanitización XSS en SecurityTest y middleware SanitizeInput
- [x] **#TODO**: Removidos markTestIncomplete de tests que ya funcionan (MantenimientoController, VehiculoController)
- [x] **#TODO**: Actualizado TODO.md con estado real de implementaciones

### Sistema de Asignaciones ✅
- [x] Migración create_asignaciones_table.php creada
- [x] Modelo Asignacion.php implementado con relaciones y validaciones
- [x] Factory AsignacionFactory.php creado
- [x] Seeder AsignacionSeeder.php implementado
- [x] Controlador AsignacionController.php con CRUD completo
- [x] Rutas API configuradas
- [x] Validaciones de negocio implementadas
- [x] Tests básicos de funcionalidad

### Actualizaciones de Base de Datos (Julio 2025) ✅
- [x] **#ESTRUCTURA**: Eliminado campo `nombre_usuario` de tabla `users`
- [x] **#ESTRUCTURA**: Agregado campo `contenido` (JSON) a tabla `documentos`
- [x] **#REFACTOR**: Actualizados todos los modelos para usar solo `email` como identificador único
- [x] **#REFACTOR**: Refactorizados controladores, requests y middleware
- [x] **#REFACTOR**: Actualizadas factories y seeders para nueva estructura
- [x] **#TESTS**: Refactorizados todos los tests (451 tests pasando)
- [x] **#MIGRATIONS**: Creadas migraciones de actualización de estructura
- [x] **#VALIDATION**: Actualizadas validaciones de usuario y documentos

---

## 📝 Notas para desarrolladores futuros

### Decisiones de diseño importantes:
1. **Soft Deletes**: Implementado para mantener historial completo
2. **Validaciones únicas**: Un vehículo y un operador solo pueden tener una asignación activa
3. **Logs automáticos**: Cada asignación registra automáticamente la acción en LogAccion
4. **Scopes útiles**: Implementados scopes para consultas frecuentes (activas, liberadas, porFecha, etc.)

### Patrones utilizados:
- **Factory Pattern**: Para generación de datos de prueba
- **Observer Pattern**: Para logs automáticos en eventos del modelo
- **Repository Pattern**: Pendiente de implementar para consultas complejas

---

**Última actualización**: 23 de Julio de 2025 - v1.5.0
**Responsable**: Backend Development Team
**Funcionalidades completadas**: Sistema de Alertas de Mantenimiento Automatizado, Transferencia de asignaciones, campos de combustible, documentación JSON `contenido`

### 🚀 Nueva Versión v1.5.0 - Sistema de Alertas de Mantenimiento

**Características destacadas:**
- ✅ Alertas automáticas por intervalos de kilometraje (motor, transmisión, hidráulico)
- ✅ Actualización automática de kilometraje de vehículos
- ✅ Sistema de configuración flexible desde frontend
- ✅ Envío inteligente de emails con anti-spam
- ✅ Reportes en PDF (listo para templates)
- ✅ API completa para gestión de configuración
- ✅ Background jobs para performance óptima
- ✅ Observer pattern para eventos automáticos

**Próximos pasos recomendados:**
1. Implementar templates reales de email y PDF
2. Integrar frontend con nuevos endpoints API
3. Configurar cron job para envío diario programado
4. Crear dashboard de alertas para supervisores
