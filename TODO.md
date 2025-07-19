# TODO - Lista de tareas pendientes

## 🔴 Tareas Críticas

### Sistema de Asignaciones
- [ ] **#TODO**: Implementar sistema de notificaciones automáticas cuando una asignación esté cerca de vencer (30 días sin liberación)
- [ ] **#FIXME**: Validar que el kilometraje_final sea mayor al kilometraje_inicial en el método liberar()
- [ ] **#TODO**: Agregar validación de negocio para no permitir asignaciones a obras canceladas o completadas
- [ ] **#TODO**: Implementar cálculo automático de próximo mantenimiento basado en kilometraje recorrido en asignaciones

## 🟡 Mejoras de Funcionalidad

### Asignaciones - Características avanzadas
- [ ] **#TODO**: Implementar sistema de transferencia de asignaciones entre operadores
- [ ] **#TODO**: Agregar campo para registrar combustible inicial/final en asignaciones
- [ ] **#TODO**: Implementar alertas por tiempo excesivo de asignación (más de X días activa)
- [ ] **#TODO**: Crear endpoint para obtener estadísticas avanzadas de productividad por operador

### Validaciones
- [ ] **#FIXME**: Agregar validación para evitar asignar vehículos en mantenimiento
- [ ] **#TODO**: Validar que la fecha_asignacion no sea posterior a la fecha actual
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
- [ ] **#TODO**: Crear migración para tabla `kilometrajes`
- [ ] **#TODO**: Implementar modelo Kilometraje con validaciones
- [ ] **#TODO**: Crear controlador para registro de kilometrajes
- [ ] **#TODO**: Integrar kilometrajes con sistema de asignaciones

### Integraciones
- [ ] **#TODO**: Conectar asignaciones con sistema de mantenimientos
- [ ] **#TODO**: Implementar sincronización con sistema de documentos
- [ ] **#TODO**: Crear API webhooks para notificar cambios de estado de asignaciones

## ✅ Completado

### Sistema de Asignaciones ✅
- [x] Migración create_asignaciones_table.php creada
- [x] Modelo Asignacion.php implementado con relaciones y validaciones
- [x] Factory AsignacionFactory.php creado
- [x] Seeder AsignacionSeeder.php implementado
- [x] Controlador AsignacionController.php con CRUD completo
- [x] Rutas API configuradas
- [x] Validaciones de negocio implementadas
- [x] Tests básicos de funcionalidad

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

**Última actualización**: 19 de Julio de 2025
**Responsable**: Backend Development Team
