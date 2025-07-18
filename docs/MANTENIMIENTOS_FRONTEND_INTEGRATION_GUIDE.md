# Guía de Integración Frontend - Módulo de Mantenimientos

## Información para el Agente IA Frontend

Esta documentación está diseñada para facilitar al agente de IA la generación de código frontend relacionado con el módulo de mantenimientos. Incluye patrones, estructuras de datos y ejemplos de implementación.

## 🔄 Flujos de Trabajo Principales

### 1. Flujo de Listado de Mantenimientos

```typescript
// Tipos de datos
interface Mantenimiento {
  id: number;
  vehiculo_id: number;
  tipo_servicio_id: number;
  proveedor?: string;
  descripcion: string;
  fecha_inicio: string;
  fecha_fin?: string;
  kilometraje_servicio: number;
  costo?: number;
  vehiculo?: Vehiculo;
  tipo_servicio?: TipoServicio;
  documentos?: Documento[];
}

interface FiltrosMantenimiento {
  vehiculo_id?: number;
  tipo_servicio_id?: number;
  proveedor?: string;
  fecha_inicio?: string;
  fecha_fin?: string;
  page?: number;
  per_page?: number;
  sort_by?: 'fecha_inicio' | 'fecha_fin' | 'costo' | 'kilometraje_servicio';
  sort_direction?: 'asc' | 'desc';
}
```

**Estado del Componente:**
```typescript
const [mantenimientos, setMantenimientos] = useState<Mantenimiento[]>([]);
const [loading, setLoading] = useState(false);
const [filtros, setFiltros] = useState<FiltrosMantenimiento>({});
const [paginacion, setPaginacion] = useState({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0
});
```

**Función de Carga:**
```typescript
const cargarMantenimientos = async (filtrosAplicados = filtros) => {
  setLoading(true);
  try {
    const queryParams = new URLSearchParams();
    Object.entries(filtrosAplicados).forEach(([key, value]) => {
      if (value !== undefined && value !== '') {
        queryParams.append(key, value.toString());
      }
    });

    const response = await fetch(`/api/mantenimientos?${queryParams}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });

    const data = await response.json();
    setMantenimientos(data.data);
    setPaginacion(data.meta);
  } catch (error) {
    console.error('Error cargando mantenimientos:', error);
  } finally {
    setLoading(false);
  }
};
```

### 2. Flujo de Creación/Edición de Mantenimientos

**Formulario de Datos:**
```typescript
interface MantenimientoForm {
  vehiculo_id: number | '';
  tipo_servicio_id: number | '';
  proveedor: string;
  descripcion: string;
  fecha_inicio: string;
  fecha_fin: string;
  kilometraje_servicio: number | '';
  costo: number | '';
}

const initialForm: MantenimientoForm = {
  vehiculo_id: '',
  tipo_servicio_id: '',
  proveedor: '',
  descripcion: '',
  fecha_inicio: '',
  fecha_fin: '',
  kilometraje_servicio: '',
  costo: ''
};
```

**Validaciones del Frontend:**
```typescript
const validarFormulario = (form: MantenimientoForm): Record<string, string> => {
  const errores: Record<string, string> = {};

  if (!form.vehiculo_id) errores.vehiculo_id = 'Vehículo es requerido';
  if (!form.tipo_servicio_id) errores.tipo_servicio_id = 'Tipo de servicio es requerido';
  if (!form.descripcion.trim()) errores.descripcion = 'Descripción es requerida';
  if (!form.fecha_inicio) errores.fecha_inicio = 'Fecha de inicio es requerida';
  if (!form.kilometraje_servicio) errores.kilometraje_servicio = 'Kilometraje es requerido';
  
  // Validar que kilometraje sea positivo
  if (form.kilometraje_servicio && form.kilometraje_servicio <= 0) {
    errores.kilometraje_servicio = 'Kilometraje debe ser mayor a 0';
  }
  
  // Validar fechas
  if (form.fecha_fin && form.fecha_inicio && form.fecha_fin < form.fecha_inicio) {
    errores.fecha_fin = 'Fecha de fin debe ser posterior a fecha de inicio';
  }
  
  // Validar costo
  if (form.costo && form.costo < 0) {
    errores.costo = 'El costo no puede ser negativo';
  }

  return errores;
};
```

**Función de Guardado:**
```typescript
const guardarMantenimiento = async (form: MantenimientoForm, id?: number) => {
  const errores = validarFormulario(form);
  if (Object.keys(errores).length > 0) {
    setErrores(errores);
    return false;
  }

  setGuardando(true);
  try {
    const url = id ? `/api/mantenimientos/${id}` : '/api/mantenimientos';
    const method = id ? 'PUT' : 'POST';
    
    const response = await fetch(url, {
      method,
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        ...form,
        vehiculo_id: Number(form.vehiculo_id),
        tipo_servicio_id: Number(form.tipo_servicio_id),
        kilometraje_servicio: Number(form.kilometraje_servicio),
        costo: form.costo ? Number(form.costo) : null
      })
    });

    if (response.ok) {
      const data = await response.json();
      // Mostrar mensaje de éxito
      // Recargar lista o redirigir
      return true;
    } else {
      const errorData = await response.json();
      setErrores(errorData.errors || { general: errorData.message });
      return false;
    }
  } catch (error) {
    console.error('Error guardando mantenimiento:', error);
    setErrores({ general: 'Error de conexión' });
    return false;
  } finally {
    setGuardando(false);
  }
};
```

### 3. Flujo de Estadísticas

**Componente de Dashboard:**
```typescript
interface EstadisticasMantenimiento {
  total_mantenimientos: number;
  costo_total: string;
  costo_promedio: string;
  mantenimientos_por_tipo: Array<{
    tipo_servicio: string;
    cantidad: number;
    costo_total: string;
  }>;
  vehiculos_mas_mantenidos: Array<{
    vehiculo_id: number;
    marca: string;
    modelo: string;
    placas: string;
    total_mantenimientos: number;
    costo_total: string;
  }>;
}

const cargarEstadisticas = async (filtros?: { year?: number; month?: number; vehiculo_id?: number }) => {
  try {
    const queryParams = new URLSearchParams();
    if (filtros?.year) queryParams.append('year', filtros.year.toString());
    if (filtros?.month) queryParams.append('month', filtros.month.toString());
    if (filtros?.vehiculo_id) queryParams.append('vehiculo_id', filtros.vehiculo_id.toString());

    const response = await fetch(`/api/mantenimientos/stats?${queryParams}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });

    const data = await response.json();
    return data.data as EstadisticasMantenimiento;
  } catch (error) {
    console.error('Error cargando estadísticas:', error);
    throw error;
  }
};
```

### 4. Flujo de Alertas de Mantenimiento

**Componente de Alertas:**
```typescript
interface AlertaMantenimiento {
  vehiculo_id: number;
  marca: string;
  modelo: string;
  placas: string;
  kilometraje_actual: number;
  ultimo_mantenimiento: {
    fecha: string;
    kilometraje: number;
    tipo_servicio: string;
  };
  kilometros_desde_ultimo: number;
  requiere_atencion: boolean;
  intervalos: {
    motor: number;
    transmision: number;
    hidraulico: number;
  };
}

const cargarProximosMantenimientos = async (limite_km = 1000) => {
  try {
    const response = await fetch(`/api/mantenimientos/proximos-por-kilometraje?limite_km=${limite_km}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });

    const data = await response.json();
    return data.data as AlertaMantenimiento[];
  } catch (error) {
    console.error('Error cargando alertas:', error);
    throw error;
  }
};
```

## 🎨 Componentes UI Sugeridos

### 1. Lista de Mantenimientos

```jsx
const ListaMantenimientos = () => {
  // ... estados y funciones

  return (
    <div className="container mx-auto p-6">
      {/* Filtros */}
      <div className="bg-white rounded-lg shadow p-6 mb-6">
        <h2 className="text-lg font-semibold mb-4">Filtros</h2>
        <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
          <select 
            value={filtros.vehiculo_id || ''} 
            onChange={(e) => setFiltros({...filtros, vehiculo_id: Number(e.target.value) || undefined})}
            className="border rounded-md px-3 py-2"
          >
            <option value="">Todos los vehículos</option>
            {vehiculos.map(v => (
              <option key={v.id} value={v.id}>{v.marca} {v.modelo} - {v.placas}</option>
            ))}
          </select>
          
          <select 
            value={filtros.tipo_servicio_id || ''} 
            onChange={(e) => setFiltros({...filtros, tipo_servicio_id: Number(e.target.value) || undefined})}
            className="border rounded-md px-3 py-2"
          >
            <option value="">Todos los tipos</option>
            {tiposServicio.map(t => (
              <option key={t.id} value={t.id}>{t.nombre_tipo_servicio}</option>
            ))}
          </select>
          
          <input
            type="text"
            placeholder="Proveedor"
            value={filtros.proveedor || ''}
            onChange={(e) => setFiltros({...filtros, proveedor: e.target.value || undefined})}
            className="border rounded-md px-3 py-2"
          />
          
          <input
            type="date"
            placeholder="Fecha desde"
            value={filtros.fecha_inicio || ''}
            onChange={(e) => setFiltros({...filtros, fecha_inicio: e.target.value || undefined})}
            className="border rounded-md px-3 py-2"
          />
          
          <input
            type="date"
            placeholder="Fecha hasta"
            value={filtros.fecha_fin || ''}
            onChange={(e) => setFiltros({...filtros, fecha_fin: e.target.value || undefined})}
            className="border rounded-md px-3 py-2"
          />
        </div>
        
        <div className="flex gap-2 mt-4">
          <button 
            onClick={() => cargarMantenimientos(filtros)}
            className="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600"
          >
            Buscar
          </button>
          <button 
            onClick={() => {
              setFiltros({});
              cargarMantenimientos({});
            }}
            className="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600"
          >
            Limpiar
          </button>
        </div>
      </div>

      {/* Tabla de Mantenimientos */}
      <div className="bg-white rounded-lg shadow overflow-hidden">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Vehículo
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Tipo de Servicio
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Fecha
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Kilometraje
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Costo
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Acciones
              </th>
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-200">
            {loading ? (
              <tr>
                <td colSpan={6} className="px-6 py-4 text-center">
                  Cargando...
                </td>
              </tr>
            ) : mantenimientos.length === 0 ? (
              <tr>
                <td colSpan={6} className="px-6 py-4 text-center text-gray-500">
                  No hay mantenimientos registrados
                </td>
              </tr>
            ) : (
              mantenimientos.map((mantenimiento) => (
                <tr key={mantenimiento.id} className="hover:bg-gray-50">
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div>
                      <div className="text-sm font-medium text-gray-900">
                        {mantenimiento.vehiculo?.marca} {mantenimiento.vehiculo?.modelo}
                      </div>
                      <div className="text-sm text-gray-500">
                        {mantenimiento.vehiculo?.placas}
                      </div>
                    </div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {mantenimiento.tipo_servicio?.nombre_tipo_servicio}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {new Date(mantenimiento.fecha_inicio).toLocaleDateString()}
                    {mantenimiento.fecha_fin && (
                      <div className="text-xs text-gray-500">
                        hasta {new Date(mantenimiento.fecha_fin).toLocaleDateString()}
                      </div>
                    )}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {mantenimiento.kilometraje_servicio.toLocaleString()} km
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {mantenimiento.costo ? `$${Number(mantenimiento.costo).toLocaleString()}` : '-'}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button className="text-indigo-600 hover:text-indigo-900 mr-2">
                      Ver
                    </button>
                    <button className="text-yellow-600 hover:text-yellow-900 mr-2">
                      Editar
                    </button>
                    <button className="text-red-600 hover:text-red-900">
                      Eliminar
                    </button>
                  </td>
                </tr>
              ))
            )}
          </tbody>
        </table>
        
        {/* Paginación */}
        <div className="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
          <div className="flex-1 flex justify-between sm:hidden">
            <button 
              disabled={paginacion.current_page === 1}
              className="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
              Anterior
            </button>
            <button 
              disabled={paginacion.current_page === paginacion.last_page}
              className="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
              Siguiente
            </button>
          </div>
          <div className="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
              <p className="text-sm text-gray-700">
                Mostrando{' '}
                <span className="font-medium">{((paginacion.current_page - 1) * paginacion.per_page) + 1}</span>
                {' '}a{' '}
                <span className="font-medium">
                  {Math.min(paginacion.current_page * paginacion.per_page, paginacion.total)}
                </span>
                {' '}de{' '}
                <span className="font-medium">{paginacion.total}</span>
                {' '}resultados
              </p>
            </div>
            {/* Componente de paginación aquí */}
          </div>
        </div>
      </div>
    </div>
  );
};
```

### 2. Dashboard de Estadísticas

```jsx
const DashboardMantenimientos = () => {
  const [estadisticas, setEstadisticas] = useState<EstadisticasMantenimiento | null>(null);
  const [filtroAño, setFiltroAño] = useState(new Date().getFullYear());
  const [filtroMes, setFiltroMes] = useState<number | undefined>();

  useEffect(() => {
    cargarEstadisticas({ year: filtroAño, month: filtroMes });
  }, [filtroAño, filtroMes]);

  return (
    <div className="container mx-auto p-6">
      <h1 className="text-2xl font-bold mb-6">Dashboard de Mantenimientos</h1>
      
      {/* Filtros */}
      <div className="bg-white rounded-lg shadow p-4 mb-6">
        <div className="flex gap-4">
          <select 
            value={filtroAño} 
            onChange={(e) => setFiltroAño(Number(e.target.value))}
            className="border rounded-md px-3 py-2"
          >
            {Array.from({ length: 5 }, (_, i) => new Date().getFullYear() - i).map(year => (
              <option key={year} value={year}>{year}</option>
            ))}
          </select>
          
          <select 
            value={filtroMes || ''} 
            onChange={(e) => setFiltroMes(e.target.value ? Number(e.target.value) : undefined)}
            className="border rounded-md px-3 py-2"
          >
            <option value="">Todos los meses</option>
            {Array.from({ length: 12 }, (_, i) => i + 1).map(month => (
              <option key={month} value={month}>
                {new Date(0, month - 1).toLocaleDateString('es', { month: 'long' })}
              </option>
            ))}
          </select>
        </div>
      </div>

      {estadisticas && (
        <>
          {/* Cards de resumen */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div className="bg-white rounded-lg shadow p-6">
              <div className="flex items-center">
                <div className="p-2 bg-blue-500 rounded-md">
                  <svg className="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <div className="ml-4">
                  <p className="text-2xl font-semibold text-gray-900">
                    {estadisticas.total_mantenimientos}
                  </p>
                  <p className="text-gray-600">Total Mantenimientos</p>
                </div>
              </div>
            </div>

            <div className="bg-white rounded-lg shadow p-6">
              <div className="flex items-center">
                <div className="p-2 bg-green-500 rounded-md">
                  <svg className="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clipRule="evenodd"/>
                  </svg>
                </div>
                <div className="ml-4">
                  <p className="text-2xl font-semibold text-gray-900">
                    ${Number(estadisticas.costo_total).toLocaleString()}
                  </p>
                  <p className="text-gray-600">Costo Total</p>
                </div>
              </div>
            </div>

            <div className="bg-white rounded-lg shadow p-6">
              <div className="flex items-center">
                <div className="p-2 bg-yellow-500 rounded-md">
                  <svg className="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <div className="ml-4">
                  <p className="text-2xl font-semibold text-gray-900">
                    ${Number(estadisticas.costo_promedio).toLocaleString()}
                  </p>
                  <p className="text-gray-600">Costo Promedio</p>
                </div>
              </div>
            </div>
          </div>

          {/* Gráficos y tablas */}
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div className="bg-white rounded-lg shadow p-6">
              <h3 className="text-lg font-semibold mb-4">Mantenimientos por Tipo</h3>
              {/* Aquí iría un gráfico de barras o pie chart */}
              <div className="space-y-2">
                {estadisticas.mantenimientos_por_tipo.map((tipo, index) => (
                  <div key={index} className="flex justify-between items-center p-3 bg-gray-50 rounded">
                    <span className="font-medium">{tipo.tipo_servicio}</span>
                    <div className="text-right">
                      <div className="font-semibold">{tipo.cantidad}</div>
                      <div className="text-sm text-gray-600">${Number(tipo.costo_total).toLocaleString()}</div>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            <div className="bg-white rounded-lg shadow p-6">
              <h3 className="text-lg font-semibold mb-4">Vehículos con Más Mantenimientos</h3>
              <div className="space-y-2">
                {estadisticas.vehiculos_mas_mantenidos.map((vehiculo, index) => (
                  <div key={index} className="flex justify-between items-center p-3 bg-gray-50 rounded">
                    <div>
                      <div className="font-medium">{vehiculo.marca} {vehiculo.modelo}</div>
                      <div className="text-sm text-gray-600">{vehiculo.placas}</div>
                    </div>
                    <div className="text-right">
                      <div className="font-semibold">{vehiculo.total_mantenimientos} servicios</div>
                      <div className="text-sm text-gray-600">${Number(vehiculo.costo_total).toLocaleString()}</div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </>
      )}
    </div>
  );
};
```

### 3. Componente de Alertas

```jsx
const AlertasMantenimiento = () => {
  const [alertas, setAlertas] = useState<AlertaMantenimiento[]>([]);
  const [limiteKm, setLimiteKm] = useState(1000);

  useEffect(() => {
    cargarProximosMantenimientos(limiteKm);
  }, [limiteKm]);

  const getSeveridad = (kilometros: number) => {
    if (kilometros >= limiteKm) return 'alta';
    if (kilometros >= limiteKm * 0.8) return 'media';
    return 'baja';
  };

  const getSeveridadColor = (severidad: string) => {
    switch (severidad) {
      case 'alta': return 'bg-red-100 text-red-800 border-red-200';
      case 'media': return 'bg-yellow-100 text-yellow-800 border-yellow-200';
      default: return 'bg-green-100 text-green-800 border-green-200';
    }
  };

  return (
    <div className="container mx-auto p-6">
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-2xl font-bold">Alertas de Mantenimiento</h1>
        <div className="flex items-center gap-2">
          <label className="text-sm font-medium">Limite (km):</label>
          <input
            type="number"
            value={limiteKm}
            onChange={(e) => setLimiteKm(Number(e.target.value))}
            className="border rounded-md px-3 py-1 w-20"
          />
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {alertas.map((alerta) => {
          const severidad = getSeveridad(alerta.kilometros_desde_ultimo);
          const colorClass = getSeveridadColor(severidad);
          
          return (
            <div key={alerta.vehiculo_id} className={`border-l-4 p-4 rounded-r-lg ${colorClass}`}>
              <div className="flex justify-between items-start mb-2">
                <h3 className="font-semibold text-lg">
                  {alerta.marca} {alerta.modelo}
                </h3>
                <span className={`px-2 py-1 rounded text-xs font-medium ${colorClass}`}>
                  {severidad.toUpperCase()}
                </span>
              </div>
              
              <p className="text-sm font-medium mb-2">{alerta.placas}</p>
              
              <div className="space-y-1 text-sm">
                <div className="flex justify-between">
                  <span>Kilometraje actual:</span>
                  <span className="font-medium">{alerta.kilometraje_actual.toLocaleString()} km</span>
                </div>
                
                <div className="flex justify-between">
                  <span>Desde último:</span>
                  <span className="font-medium">{alerta.kilometros_desde_ultimo.toLocaleString()} km</span>
                </div>
                
                <div className="mt-3 p-2 bg-white bg-opacity-50 rounded text-xs">
                  <div className="font-medium mb-1">Último mantenimiento:</div>
                  <div>{alerta.ultimo_mantenimiento.tipo_servicio}</div>
                  <div>{new Date(alerta.ultimo_mantenimiento.fecha).toLocaleDateString()}</div>
                  <div>{alerta.ultimo_mantenimiento.kilometraje.toLocaleString()} km</div>
                </div>
                
                <div className="mt-3 p-2 bg-white bg-opacity-50 rounded text-xs">
                  <div className="font-medium mb-1">Intervalos configurados:</div>
                  <div>Motor: {alerta.intervalos.motor.toLocaleString()} km</div>
                  <div>Transmisión: {alerta.intervalos.transmision.toLocaleString()} km</div>
                  <div>Hidráulico: {alerta.intervalos.hidraulico.toLocaleString()} km</div>
                </div>
              </div>
              
              <button className="mt-4 w-full bg-white bg-opacity-70 hover:bg-opacity-100 text-gray-800 font-medium py-2 px-4 rounded transition-colors">
                Programar Mantenimiento
              </button>
            </div>
          );
        })}
      </div>

      {alertas.length === 0 && (
        <div className="text-center py-12">
          <div className="text-gray-400 text-6xl mb-4">🚗</div>
          <h3 className="text-lg font-medium text-gray-900 mb-2">
            No hay alertas de mantenimiento
          </h3>
          <p className="text-gray-600">
            Todos los vehículos están al día con sus mantenimientos.
          </p>
        </div>
      )}
    </div>
  );
};
```

## 🛡️ Consideraciones de Seguridad

### Validación de Permisos
```typescript
const checkPermission = (permission: string): boolean => {
  const user = JSON.parse(localStorage.getItem('user') || '{}');
  return user.role?.permissions?.some((p: any) => p.nombre === permission) || false;
};

// Uso en componentes
if (!checkPermission('ver_mantenimiento')) {
  return <div>No tienes permisos para ver esta sección</div>;
}
```

### Sanitización de Datos
```typescript
const sanitizeInput = (input: string): string => {
  // Remover etiquetas HTML y scripts
  return input.replace(/<[^>]*>/g, '').trim();
};

// Aplicar en formularios
const handleInputChange = (field: string, value: string) => {
  setFormData({
    ...formData,
    [field]: sanitizeInput(value)
  });
};
```

### Manejo de Errores
```typescript
const handleApiError = (error: any) => {
  if (error.status === 401) {
    // Token expirado
    localStorage.clear();
    window.location.href = '/login';
  } else if (error.status === 403) {
    // Sin permisos
    setError('No tienes permisos para realizar esta acción');
  } else if (error.status === 422) {
    // Errores de validación
    setErrores(error.errors);
  } else {
    // Error general
    setError('Ha ocurrido un error inesperado');
  }
};
```

## 📱 Consideraciones de UX/UI

### Estados de Carga
```typescript
const LoadingSpinner = ({ size = 'md' }) => {
  const sizeClasses = {
    sm: 'w-4 h-4',
    md: 'w-8 h-8',
    lg: 'w-12 h-12'
  };

  return (
    <div className={`animate-spin rounded-full border-b-2 border-blue-500 ${sizeClasses[size]}`} />
  );
};
```

### Notificaciones
```typescript
const useNotifications = () => {
  const [notifications, setNotifications] = useState<Notification[]>([]);

  const addNotification = (type: 'success' | 'error' | 'warning', message: string) => {
    const id = Date.now();
    setNotifications(prev => [...prev, { id, type, message }]);
    
    setTimeout(() => {
      setNotifications(prev => prev.filter(n => n.id !== id));
    }, 5000);
  };

  return { notifications, addNotification };
};
```

### Confirmaciones
```typescript
const useConfirmDialog = () => {
  const [isOpen, setIsOpen] = useState(false);
  const [config, setConfig] = useState({ title: '', message: '', onConfirm: () => {} });

  const confirm = (title: string, message: string, onConfirm: () => void) => {
    setConfig({ title, message, onConfirm });
    setIsOpen(true);
  };

  const handleConfirm = () => {
    config.onConfirm();
    setIsOpen(false);
  };

  return { isOpen, confirm, handleConfirm, setIsOpen, config };
};
```

## 🔗 Integración con Otros Módulos

### Relación con Vehículos
```typescript
// Obtener lista de vehículos para selects
const cargarVehiculos = async () => {
  const response = await fetch('/api/vehiculos', {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Accept': 'application/json'
    }
  });
  return response.json();
};
```

### Relación con Documentos
```typescript
// Subir documentos asociados al mantenimiento
const subirDocumento = async (mantenimientoId: number, archivo: File, descripcion: string) => {
  const formData = new FormData();
  formData.append('archivo', archivo);
  formData.append('descripcion', descripcion);
  formData.append('mantenimiento_id', mantenimientoId.toString());
  formData.append('tipo_documento_id', '1'); // ID del tipo de documento

  const response = await fetch('/api/documentos', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`
    },
    body: formData
  });

  return response.json();
};
```

## 📝 Notas Finales para el Agente IA

1. **Estructura de Carpetas Sugerida:**
   ```
   src/
   ├── components/
   │   ├── mantenimientos/
   │   │   ├── ListaMantenimientos.tsx
   │   │   ├── FormularioMantenimiento.tsx
   │   │   ├── DashboardMantenimientos.tsx
   │   │   ├── AlertasMantenimiento.tsx
   │   │   └── EstadisticasMantenimiento.tsx
   │   └── common/
   │       ├── LoadingSpinner.tsx
   │       ├── Pagination.tsx
   │       └── ConfirmDialog.tsx
   ├── hooks/
   │   ├── useMantenimientos.ts
   │   ├── useNotifications.ts
   │   └── usePermissions.ts
   ├── types/
   │   └── mantenimientos.ts
   └── utils/
       ├── api.ts
       ├── validation.ts
       └── formatting.ts
   ```

2. **Prioridades de Implementación:**
   1. Lista básica de mantenimientos
   2. Formulario de creación/edición
   3. Dashboard con estadísticas básicas
   4. Sistema de alertas
   5. Funcionalidades avanzadas (filtros, exportación, etc.)

3. **Consideraciones de Performance:**
   - Implementar debounce en búsquedas
   - Paginar siempre los resultados
   - Cachear catálogos estáticos (tipos de servicio)
   - Usar lazy loading para componentes pesados

4. **Accesibilidad:**
   - Incluir labels apropiados
   - Soporte para teclado
   - Contraste adecuado
   - Textos alternativos para iconos

Esta documentación proporciona una base sólida para que el agente de IA pueda generar código frontend funcional y bien estructurado para el módulo de mantenimientos.
