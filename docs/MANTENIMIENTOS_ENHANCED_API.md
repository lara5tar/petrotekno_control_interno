# 📋 Mantenimientos - Nuevas Funcionalidades API

## 🎯 Resumen

Este documento detalla las nuevas funcionalidades agregadas al modelo `Mantenimiento` que están disponibles para integración frontend.

---

## 🔧 Nuevos Scopes de Filtrado

### 1. Filtrar por Vehículo Específico

**Endpoint**: `GET /api/mantenimientos?vehiculo_id={id}`

```javascript
// Obtener mantenimientos de un vehículo específico
const getMantenimientosByVehiculo = async (vehiculoId) => {
    const response = await fetch(`/api/mantenimientos?vehiculo_id=${vehiculoId}`, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    });
    return response.json();
};

// Ejemplo de uso
const mantenimientos = await getMantenimientosByVehiculo(123);
```

### 2. Filtrar por Fecha Específica

**Endpoint**: `GET /api/mantenimientos?fecha={YYYY-MM-DD}`

```javascript
// Obtener mantenimientos de una fecha específica
const getMantenimientosByFecha = async (fecha) => {
    const response = await fetch(`/api/mantenimientos?fecha=${fecha}`, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    });
    return response.json();
};

// Ejemplo: mantenimientos de hoy
const hoy = new Date().toISOString().split('T')[0]; // YYYY-MM-DD
const mantenimientosHoy = await getMantenimientosByFecha(hoy);
```

### 3. Filtrar por Rango de Fechas

**Endpoint**: `GET /api/mantenimientos?fecha_inicio={YYYY-MM-DD}&fecha_fin={YYYY-MM-DD}`

```javascript
// Obtener mantenimientos en un rango de fechas
const getMantenimientosByRango = async (fechaInicio, fechaFin) => {
    const params = new URLSearchParams({
        fecha_inicio: fechaInicio,
        fecha_fin: fechaFin
    });
    
    const response = await fetch(`/api/mantenimientos?${params}`, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    });
    return response.json();
};

// Ejemplo: mantenimientos del mes actual
const inicioMes = '2025-07-01';
const finMes = '2025-07-31';
const mantenimientosMes = await getMantenimientosByRango(inicioMes, finMes);
```

---

## 💰 Nuevo Campo: Costo Formateado

### Descripción
Se agregó un accessor `costo_formateado` que devuelve el costo en formato de moneda con símbolo de peso mexicano.

### Respuesta de la API
```json
{
    "id": 1,
    "vehiculo_id": 123,
    "descripcion": "Cambio de aceite",
    "costo": 1500.50,
    "costo_formateado": "$1,500.50",
    "fecha_inicio": "2025-07-15",
    "fecha_fin": "2025-07-16",
    "duracion_dias": 1,
    "created_at": "2025-07-15T10:00:00.000000Z",
    "updated_at": "2025-07-15T10:00:00.000000Z"
}
```

### Uso en Frontend
```javascript
// Mostrar costo formateado directamente
const displayMantenimiento = (mantenimiento) => {
    return `
        <div class="mantenimiento-card">
            <h3>${mantenimiento.descripcion}</h3>
            <p>Costo: ${mantenimiento.costo_formateado}</p>
            <p>Duración: ${mantenimiento.duracion_dias} días</p>
        </div>
    `;
};
```

---

## 🛠️ Funciones de Utilidad

### Función Completa de Filtrado
```javascript
class MantenimientosAPI {
    constructor(baseURL, token) {
        this.baseURL = baseURL;
        this.token = token;
        this.headers = {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        };
    }

    async getMantenimientos(filtros = {}) {
        const params = new URLSearchParams();
        
        // Filtros disponibles
        if (filtros.vehiculo_id) {
            params.append('vehiculo_id', filtros.vehiculo_id);
        }
        
        if (filtros.fecha) {
            params.append('fecha', filtros.fecha);
        }
        
        if (filtros.fecha_inicio && filtros.fecha_fin) {
            params.append('fecha_inicio', filtros.fecha_inicio);
            params.append('fecha_fin', filtros.fecha_fin);
        }
        
        if (filtros.tipo_servicio_id) {
            params.append('tipo_servicio_id', filtros.tipo_servicio_id);
        }
        
        // Paginación
        if (filtros.page) {
            params.append('page', filtros.page);
        }
        
        if (filtros.per_page) {
            params.append('per_page', filtros.per_page);
        }
        
        const url = `${this.baseURL}/api/mantenimientos${params.toString() ? '?' + params.toString() : ''}`;
        
        const response = await fetch(url, {
            method: 'GET',
            headers: this.headers
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return response.json();
    }

    // Métodos específicos de filtrado
    async getByVehiculo(vehiculoId, page = 1) {
        return this.getMantenimientos({
            vehiculo_id: vehiculoId,
            page
        });
    }

    async getByFecha(fecha) {
        return this.getMantenimientos({
            fecha
        });
    }

    async getByRangoFechas(fechaInicio, fechaFin, page = 1) {
        return this.getMantenimientos({
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin,
            page
        });
    }

    async getRecientes(dias = 30) {
        const fechaFin = new Date().toISOString().split('T')[0];
        const fechaInicio = new Date(Date.now() - dias * 24 * 60 * 60 * 1000)
            .toISOString().split('T')[0];
        
        return this.getByRangoFechas(fechaInicio, fechaFin);
    }
}

// Uso
const api = new MantenimientosAPI('http://localhost:8000', userToken);

// Ejemplos de uso
const mantenimientosVehiculo = await api.getByVehiculo(123);
const mantenimientosHoy = await api.getByFecha('2025-07-18');
const mantenimientosUltimos30Dias = await api.getRecientes(30);
```

---

## 📊 Casos de Uso Comunes

### 1. Dashboard de Mantenimientos
```javascript
const buildMantenimientosDashboard = async (vehiculoId) => {
    try {
        // Mantenimientos recientes
        const recientes = await api.getRecientes(30);
        
        // Mantenimientos del vehículo específico
        const porVehiculo = vehiculoId ? 
            await api.getByVehiculo(vehiculoId) : 
            null;
        
        // Mantenimientos de hoy
        const hoy = new Date().toISOString().split('T')[0];
        const mantenimientosHoy = await api.getByFecha(hoy);
        
        return {
            recientes: recientes.data,
            porVehiculo: porVehiculo?.data,
            hoy: mantenimientosHoy.data,
            costoTotal: recientes.data.reduce((sum, m) => sum + (m.costo || 0), 0)
        };
    } catch (error) {
        console.error('Error cargando dashboard:', error);
        throw error;
    }
};
```

### 2. Reporte de Costos
```javascript
const generarReporteCostos = async (fechaInicio, fechaFin) => {
    const mantenimientos = await api.getByRangoFechas(fechaInicio, fechaFin);
    
    const reporte = {
        total_mantenimientos: mantenimientos.data.length,
        costo_total: 0,
        costo_promedio: 0,
        por_tipo_servicio: {},
        por_vehiculo: {}
    };
    
    mantenimientos.data.forEach(m => {
        const costo = parseFloat(m.costo || 0);
        reporte.costo_total += costo;
        
        // Agrupar por tipo de servicio
        const tipoServicio = m.tipo_servicio?.nombre_tipo_servicio || 'Sin clasificar';
        if (!reporte.por_tipo_servicio[tipoServicio]) {
            reporte.por_tipo_servicio[tipoServicio] = {
                cantidad: 0,
                costo_total: 0
            };
        }
        reporte.por_tipo_servicio[tipoServicio].cantidad++;
        reporte.por_tipo_servicio[tipoServicio].costo_total += costo;
        
        // Agrupar por vehículo
        const vehiculoInfo = `${m.vehiculo?.marca || ''} ${m.vehiculo?.modelo || ''} (${m.vehiculo?.placas || ''})`.trim();
        if (!reporte.por_vehiculo[vehiculoInfo]) {
            reporte.por_vehiculo[vehiculoInfo] = {
                cantidad: 0,
                costo_total: 0
            };
        }
        reporte.por_vehiculo[vehiculoInfo].cantidad++;
        reporte.por_vehiculo[vehiculoInfo].costo_total += costo;
    });
    
    reporte.costo_promedio = reporte.total_mantenimientos > 0 ? 
        reporte.costo_total / reporte.total_mantenimientos : 
        0;
    
    return reporte;
};
```

### 3. Calendario de Mantenimientos
```javascript
const getMantenimientosParaCalendario = async (year, month) => {
    const primerDia = new Date(year, month - 1, 1).toISOString().split('T')[0];
    const ultimoDia = new Date(year, month, 0).toISOString().split('T')[0];
    
    const mantenimientos = await api.getByRangoFechas(primerDia, ultimoDia);
    
    // Agrupar por fecha
    const porFecha = {};
    mantenimientos.data.forEach(m => {
        const fecha = m.fecha_inicio;
        if (!porFecha[fecha]) {
            porFecha[fecha] = [];
        }
        porFecha[fecha].push({
            id: m.id,
            descripcion: m.descripcion,
            vehiculo: m.vehiculo?.placas,
            costo_formateado: m.costo_formateado,
            tipo_servicio: m.tipo_servicio?.nombre_tipo_servicio
        });
    });
    
    return porFecha;
};
```

---

## ⚡ Performance y Optimización

### Caching Recomendado
```javascript
class MantenimientosCacheService {
    constructor(apiService, cacheTime = 5 * 60 * 1000) { // 5 minutos
        this.api = apiService;
        this.cache = new Map();
        this.cacheTime = cacheTime;
    }
    
    generateCacheKey(filtros) {
        return JSON.stringify(filtros);
    }
    
    async getMantenimientos(filtros = {}) {
        const cacheKey = this.generateCacheKey(filtros);
        const cached = this.cache.get(cacheKey);
        
        if (cached && Date.now() - cached.timestamp < this.cacheTime) {
            return cached.data;
        }
        
        const data = await this.api.getMantenimientos(filtros);
        this.cache.set(cacheKey, {
            data,
            timestamp: Date.now()
        });
        
        return data;
    }
    
    clearCache() {
        this.cache.clear();
    }
}
```

---

## 🚨 Consideraciones Importantes

### Validaciones
- Las fechas deben estar en formato ISO (`YYYY-MM-DD`)
- El `vehiculo_id` debe existir en la base de datos
- Los campos de fecha son opcionales pero recomendados para filtrado

### Límites de Paginación
- Máximo 100 registros por página
- Por defecto: 15 registros por página

### Autorización
- Se requiere token de autenticación válido
- Los permisos dependen del rol del usuario:
  - **Admin**: Acceso completo
  - **Supervisor**: Solo lectura  
  - **Operador**: Sin acceso directo

---

*Documentación actualizada: Julio 2025*  
*Versión: Issue #16 - Mantenimientos Enhancement*
