# 🚗 Módulo de Kilometrajes - Control Interno Petrotekno

## 📋 Descripción General

El módulo de kilometrajes permite gestionar y monitorear las lecturas del odómetro de los vehículos de la empresa, facilitando el control del uso vehicular y la programación de mantenimientos preventivos.

## ✨ Características Principales

### 🔍 Gestión de Registros
- **Listado completo** con filtros avanzados por vehículo, obra, fecha y búsqueda de texto
- **Creación de registros** con validación inteligente de kilometrajes progresivos
- **Visualización detallada** de cada registro con información contextual
- **Edición controlada** con validaciones de consistencia
- **Eliminación segura** con confirmación y actualización automática

### 📊 Funcionalidades Avanzadas
- **Historial por vehículo** con timeline visual y estadísticas
- **Alertas de mantenimiento** automáticas basadas en intervalos configurados
- **Validación progresiva** que asegura que los kilometrajes siempre aumenten
- **Actualización automática** del kilometraje actual del vehículo

### 🔐 Control de Acceso
- **Permisos granulares**: ver, crear, editar, eliminar kilometrajes
- **Auditoría completa** con registro de usuario y timestamps
- **Filtros de seguridad** basados en roles y permisos

## 🗂️ Estructura del Módulo

### Rutas Web
```
/kilometrajes                    # Listado principal
/kilometrajes/create            # Formulario de creación
/kilometrajes/{id}              # Ver detalles
/kilometrajes/{id}/edit         # Formulario de edición
/kilometrajes/vehiculo/{id}/historial  # Historial por vehículo
/kilometrajes/alertas/mantenimiento    # Alertas de mantenimiento
```

### Rutas API
```
GET    /api/kilometrajes                    # Lista con filtros
POST   /api/kilometrajes                    # Crear nuevo registro
GET    /api/kilometrajes/{id}               # Ver detalles
PUT    /api/kilometrajes/{id}               # Actualizar registro
DELETE /api/kilometrajes/{id}               # Eliminar registro
GET    /api/kilometrajes/vehiculo/{id}/historial    # Historial
GET    /api/kilometrajes/alertas-mantenimiento      # Alertas
```

## 🎯 Casos de Uso

### 1. Registro de Kilometraje
**Escenario**: Un conductor regresa de una obra y necesita registrar el kilometraje del vehículo

**Flujo**:
1. Acceder a "Nuevo Registro" desde el módulo de kilometrajes
2. Seleccionar el vehículo (se muestra el último kilometraje registrado)
3. Ingresar el nuevo kilometraje (con validación automática)
4. Seleccionar la obra asociada (opcional)
5. Agregar observaciones sobre el recorrido
6. Guardar el registro

**Validaciones**:
- El kilometraje debe ser mayor al último registrado
- No puede ser excesivamente mayor (alerta si supera 100,000 km)
- La fecha no puede ser futura

### 2. Monitoreo de Mantenimientos
**Escenario**: El supervisor necesita ver qué vehículos requieren mantenimiento

**Flujo**:
1. Acceder a "Alertas de Mantenimiento"
2. Revisar vehículos con alertas urgentes (< 1,000 km restantes)
3. Ver próximos mantenimientos por tipo (motor, transmisión, hidráulico)
4. Programar el mantenimiento correspondiente

### 3. Análisis de Uso Vehicular
**Escenario**: Revisar el historial de uso de un vehículo específico

**Flujo**:
1. Desde el listado, seleccionar "Ver Historial" de un vehículo
2. Analizar la línea de tiempo de registros
3. Revisar estadísticas (recorrido total, promedio diario)
4. Identificar patrones de uso

## 🛠️ Características Técnicas

### Modelo de Datos
```php
// Tabla: kilometrajes
- id: bigint (PK)
- vehiculo_id: bigint (FK -> vehiculos)
- kilometraje: integer (unsigned)
- fecha_captura: date
- usuario_captura_id: bigint (FK -> users)
- obra_id: bigint (FK -> obras, nullable)
- observaciones: text (nullable)
- timestamps
```

### Relaciones
- **Vehículo**: Cada registro pertenece a un vehículo específico
- **Usuario**: Cada registro tiene un usuario que lo registró
- **Obra**: Opcionalmente asociado a una obra específica

### Validaciones
- **Progresividad**: Los kilometrajes deben ser progresivos por vehículo
- **Límites**: Máximo 9,999,999 km, mínimo 1 km
- **Fechas**: No puede ser futura, máximo la fecha actual
- **Observaciones**: Máximo 500 caracteres

### Índices de Base de Datos
- `vehiculo_id, fecha_captura` (optimización de consultas por vehículo)
- `fecha_captura` (filtros de fecha)
- `usuario_captura_id` (auditoría)
- `obra_id` (filtros por obra)
- `vehiculo_id, kilometraje` (unique constraint para evitar duplicados)

## 🎨 Interfaz de Usuario

### Diseño Responsivo
- **Desktop**: Layout de 2-3 columnas con sidebar
- **Tablet**: Layout adaptable con menú colapsable
- **Mobile**: Vista de lista optimizada con acciones contextuales

### Elementos Visuales
- **Timeline**: Para historial de vehículos
- **Cards**: Para alertas de mantenimiento
- **Badges**: Para estados y kilometrajes
- **Íconos**: FontAwesome para acciones y estados

### Colores del Sistema
- **Primario**: Azul (#007bff) para elementos principales
- **Urgente**: Rojo (#dc3545) para alertas críticas
- **Advertencia**: Amarillo (#ffc107) para próximos mantenimientos
- **Éxito**: Verde (#28a745) para confirmaciones
- **Info**: Azul claro (#17a2b8) para información

## 🔧 Configuración

### Permisos Requeridos
```php
'ver_kilometrajes'      => 'Permite ver los kilometrajes registrados'
'crear_kilometrajes'    => 'Permite crear nuevos registros de kilometraje'
'editar_kilometrajes'   => 'Permite editar registros existentes'
'eliminar_kilometrajes' => 'Permite eliminar registros de kilometraje'
```

### Variables de Configuración
- **Límite de páginas**: 15 registros por página en listados
- **Límite de alertas**: 1,000 km para alertas urgentes
- **Límite de incremento**: 100,000 km para alertas de incremento excesivo

## 🚀 Instalación y Configuración

### 1. Rutas
Las rutas ya están configuradas en `routes/web.php` y `routes/api.php`

### 2. Permisos
```bash
php artisan db:seed --class=KilometrajePermissionSeeder
```

### 3. Datos de Prueba
```bash
php artisan db:seed --class=KilometrajeSeeder
```

### 4. Acceso en Menú
El módulo aparece automáticamente en el sidebar para usuarios con permisos `ver_kilometrajes`

## 📈 Métricas y Reportes

### Alertas de Mantenimiento
- Vehículos con mantenimiento urgente (< 1,000 km)
- Próximos mantenimientos por tipo
- Vehículos sin registros recientes

### Estadísticas de Uso
- Promedio de kilometraje diario por vehículo
- Recorrido total por período
- Vehículos más utilizados

### Auditoría
- Registros por usuario
- Modificaciones y eliminaciones
- Patrones de uso por obra

## 🔄 Integración con Otros Módulos

### Vehículos
- Actualización automática del `kilometraje_actual` en la tabla vehículos
- Cálculo de alertas basado en intervalos de mantenimiento configurados

### Obras
- Asociación opcional de registros con obras específicas
- Filtros y reportes por obra

### Usuarios
- Auditoría de quien registra cada kilometraje
- Permisos diferenciados por rol

## 🐛 Resolución de Problemas

### Kilometrajes Inconsistentes
- **Síntoma**: Error al intentar registrar un kilometraje menor
- **Solución**: Verificar el último registro y corregir si es necesario
- **Prevención**: Las validaciones automáticas previenen este problema

### Alertas No Aparecen
- **Síntoma**: No se muestran alertas de mantenimiento
- **Causa**: Vehículo sin intervalos de mantenimiento configurados
- **Solución**: Configurar intervalos en el módulo de vehículos

### Problemas de Permisos
- **Síntoma**: Usuario no puede acceder al módulo
- **Solución**: Verificar que el usuario tenga el permiso `ver_kilometrajes`
- **Comando**: `php artisan permission:sync`

## 📝 Notas de Desarrollo

### Performance
- Índices optimizados para consultas frecuentes
- Carga lazy de relaciones cuando no se necesitan
- Paginación en todos los listados

### Seguridad
- Validación server-side de todos los inputs
- Protección CSRF en formularios
- Sanitización de observaciones

### Mantenibilidad
- Código bien documentado
- Separación clara de responsabilidades
- Pruebas unitarias recomendadas

## 🎯 Roadmap Futuro

### Funcionalidades Planeadas
- **Importación masiva** desde archivos CSV/Excel
- **Exportación de reportes** en PDF/Excel
- **Notificaciones automáticas** por email para alertas urgentes
- **Dashboard de métricas** con gráficos interactivos
- **Predicción de mantenimientos** basada en patrones de uso
- **Integración con GPS** para registro automático

### Mejoras Técnicas
- **API REST completa** con documentación Swagger
- **Cache de consultas** para mejor performance
- **Eventos de Laravel** para integraciones
- **Jobs en cola** para procesos pesados

---

*Documentación del Módulo de Kilometrajes v1.0*  
*Sistema de Control Interno - Petrotekno*
