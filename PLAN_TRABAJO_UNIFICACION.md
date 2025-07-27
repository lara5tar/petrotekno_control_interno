# 📋 PLAN DE TRABAJO - UNIFICACIÓN SISTEMA PETROTEKNO

## 🎯 **FASE 1: CONSOLIDACIÓN BASE** (ACTUAL)

### ✅ **Completado**
- [x] Resolución de conflictos de merge en AppServiceProvider
- [x] Implementación de directivas @hasPermission y @endhasPermission
- [x] Reorganización de tabla de personal (ID Empleado al inicio)
- [x] Unificación de colores del sistema (botón Filtrar amarillo)
- [x] Creación de rama feature/unificacion-sistema-completo
- [x] Documentación del issue de unificación

### 🔄 **En Progreso**
- [ ] Validación completa de permisos en todas las vistas
- [ ] Estandarización de componentes UI
- [ ] Verificación de funcionalidad CRUD completa

### 📝 **Tareas Inmediatas**

#### 1. **Validación de Permisos** (Prioridad Alta)
```bash
# Verificar que todas las vistas usen correctamente @hasPermission
- personal/index.blade.php ✅
- personal/create.blade.php ⚠️  (revisar)
- personal/edit.blade.php ⚠️   (revisar)
- personal/show.blade.php ⚠️   (revisar)
- vehiculos/index.blade.php ✅
- vehiculos/create.blade.php ⚠️ (revisar)
- vehiculos/edit.blade.php ⚠️  (revisar)
- vehiculos/show.blade.php ⚠️  (revisar)
```

#### 2. **Estandarización de UI** (Prioridad Alta)
```bash
# Unificar colores y estilos
- Botones: bg-petroyellow hover:bg-yellow-500 text-petrodark
- Estados: Verde (activo), Gris (inactivo), Amarillo (pendiente)
- Tablas: Encabezados bg-gray-50, hover:bg-gray-50 en filas
- Formularios: border-gray-300, focus:border-petroyellow
```

#### 3. **Verificación de Funcionalidad** (Prioridad Media)
```bash
# Probar CRUD completo en cada módulo
- Personal: Crear ✅, Leer ✅, Actualizar ⚠️, Eliminar ⚠️
- Vehículos: Crear ⚠️, Leer ✅, Actualizar ⚠️, Eliminar ⚠️
- Mantenimientos: Crear ⚠️, Leer ⚠️, Actualizar ⚠️, Eliminar ⚠️
- Asignaciones: Crear ⚠️, Leer ⚠️, Actualizar ⚠️, Eliminar ⚠️
- Obras: Crear ⚠️, Leer ⚠️, Actualizar ⚠️, Eliminar ⚠️
```

## 🚀 **PRÓXIMOS PASOS INMEDIATOS**

### **Paso 1: Auditoría de Permisos**
```bash
# Comando para verificar uso de permisos
grep -r "@hasPermission" resources/views/ --include="*.blade.php"
```

### **Paso 2: Estandarización de Botones**
```bash
# Buscar botones que no usen el color del sistema
grep -r "bg-blue-" resources/views/ --include="*.blade.php"
grep -r "bg-green-" resources/views/ --include="*.blade.php"
grep -r "bg-red-" resources/views/ --include="*.blade.php"
```

### **Paso 3: Validación de Formularios**
```bash
# Verificar que todos los formularios tengan validación
find resources/views/ -name "*.blade.php" -exec grep -l "form" {} \;
```

## 📊 **MÉTRICAS DE PROGRESO**

### **Estado Actual**
- ✅ Directivas Blade: 100% implementadas
- ✅ Colores del sistema: 60% unificados
- ✅ Permisos básicos: 80% implementados
- ⚠️ CRUD completo: 40% validado
- ⚠️ UI consistente: 70% estandarizada

### **Objetivos Fase 1**
- 🎯 Permisos: 100% implementados y validados
- 🎯 UI: 95% estandarizada
- 🎯 CRUD: 90% funcional y validado
- 🎯 Documentación: 100% actualizada

## 🔧 **COMANDOS ÚTILES**

### **Verificación Rápida**
```bash
# Ejecutar verificación completa
./verificar_sistema_unificacion.sh

# Limpiar caché
php artisan view:clear
php artisan config:clear
php artisan route:clear

# Verificar rutas
php artisan route:list | grep -E "(personal|vehiculos|mantenimientos)"

# Verificar permisos en base de datos
php artisan tinker --execute="echo 'Permisos: ' . App\Models\Permission::count();"
```

### **Testing Manual**
```bash
# Iniciar servidor
php artisan serve --host=localhost --port=8001

# URLs para probar
http://localhost:8001/personal
http://localhost:8001/vehiculos
http://localhost:8001/mantenimientos
http://localhost:8001/asignaciones
http://localhost:8001/obras
```

## 📝 **CHECKLIST DIARIO**

### **Antes de comenzar**
- [ ] Verificar que estamos en la rama correcta
- [ ] Ejecutar script de verificación
- [ ] Revisar estado de la base de datos
- [ ] Confirmar que el servidor funciona

### **Durante el desarrollo**
- [ ] Probar cada cambio inmediatamente
- [ ] Verificar permisos en cada vista modificada
- [ ] Mantener consistencia de colores
- [ ] Documentar cambios importantes

### **Antes de commit**
- [ ] Ejecutar verificación completa
- [ ] Probar funcionalidad afectada
- [ ] Verificar que no se rompió nada existente
- [ ] Actualizar documentación si es necesario

## 🎯 **RESULTADO ESPERADO FASE 1**

Al completar la Fase 1, tendremos:

1. **Sistema de permisos 100% funcional** en todas las vistas
2. **UI completamente estandarizada** con colores del sistema
3. **CRUD básico validado** en todos los módulos principales
4. **Base sólida** para las siguientes fases de integración

---

**Fecha de inicio:** Hoy  
**Duración estimada:** 2-3 días  
**Responsable:** Equipo de Desarrollo  
**Rama:** feature/unificacion-sistema-completo