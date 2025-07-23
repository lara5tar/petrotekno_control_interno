# TODO - Lista de tareas pendientes

## 🔴 Tareas Críticas

### Sistema de Asignaciones
- [x] **#TODO**: Implementar sistema de notificaciones automáticas cuando una asignación esté cerca de vencer (30 días sin liberación) ✅
- [x] **#FIXME**: Validar que el kilometraje_final sea mayor al kilometraje_inicial en el método liberar() ✅
- [x] **#TODO**: Agregar validación de negocio para no permitir asignaciones a obras canceladas o completadas ✅
- [ ] **#TODO**: Implementar cálculo automático de próximo mantenimiento basado en kilometraje recorrido en asignaciones

## 🟡 Mejoras de Funcionalidad

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

**Última actualización**: 23 de Julio de 2025 - v1.4.0
**Responsable**: Backend Development Team
**Funcionalidades completadas**: Transferencia de asignaciones, campos de combustible, documentación JSON `contenido`
