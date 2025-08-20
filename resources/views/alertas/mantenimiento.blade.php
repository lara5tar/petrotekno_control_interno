@extends('layouts.app')

@section('title', 'Alertas de Mantenimiento')

@section('content')
<div class="min-h-screen bg-gray-50 p-4">
    <div class="max-w-7xl mx-auto">
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">🚨 Alertas de Mantenimiento</h1>
                    <p class="text-gray-600 mt-1">Monitoreo del estado de mantenimiento de vehículos</p>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Enlace a vista unificada -->
                    <a href="{{ route('alertas.unificada') }}" 
                       class="inline-flex items-center px-4 py-2 border border-blue-300 text-sm font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        🚨 Ver Centro de Alertas Unificado
                    </a>
                    
                    <!-- Leyenda de estados -->
                    <div class="flex items-center space-x-4 text-sm">
                        <div class="flex items-center space-x-1">
                            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                            <span class="text-gray-700">Vencido</span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                            <span class="text-gray-700">Próximo</span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <span class="text-gray-700">OK</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            @php
                $contadores = [
                    'total' => count($alertas),
                    'vencidos' => 0,
                    'proximos' => 0,
                    'ok' => 0
                ];
                
                foreach ($alertas as $vehiculo) {
                    // Contar alertas de mantenimiento
                    foreach ($vehiculo['alertas'] as $alerta) {
                        if ($alerta['estado'] === 'Vencido') $contadores['vencidos']++;
                        elseif ($alerta['estado'] === 'Próximo') $contadores['proximos']++;
                        else $contadores['ok']++;
                    }
                    
                    // Contar alertas de documentos
                    foreach ($vehiculo['documentos'] as $documento) {
                        if ($documento['estado'] === 'Vencido') $contadores['vencidos']++;
                        elseif ($documento['estado'] === 'Próximo a Vencer') $contadores['proximos']++;
                        elseif ($documento['estado'] === 'OK') $contadores['ok']++;
                        // 'Sin Fecha' no se cuenta en ninguna categoría específica
                    }
                }
            @endphp
            
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        🚗
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600">Total Vehículos</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $contadores['total'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-lg">
                        ⚠️
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600">Vencidos</p>
                        <p class="text-2xl font-semibold text-red-600">{{ $contadores['vencidos'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        🔔
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600">Próximos</p>
                        <p class="text-2xl font-semibold text-yellow-600">{{ $contadores['proximos'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        ✅
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600">Al día</p>
                        <p class="text-2xl font-semibold text-green-600">{{ $contadores['ok'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6" x-data="{ filtroEstado: 'todos' }">
            <div class="flex items-center space-x-4">
                <label class="text-sm font-medium text-gray-700">Filtrar por estado:</label>
                <select x-model="filtroEstado" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="todos">Todos los estados</option>
                    <option value="Vencido">Solo vencidos</option>
                    <option value="Próximo">Solo próximos (mantenimiento)</option>
                    <option value="Próximo a Vencer">Solo próximos (documentos)</option>
                    <option value="OK">Solo al día</option>
                    <option value="Sin Fecha">Sin fecha definida</option>
                </select>
            </div>
        </div>

        <!-- Tabla de alertas -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Vehículo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                KM Actual
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                🔧 Motor
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ⚙️ Transmisión
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                🛢️ Hidráulico
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                🛡️ Póliza
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                📋 Derecho
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($alertas as $vehiculo)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700">🚗</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $vehiculo['marca'] }} {{ $vehiculo['modelo'] }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $vehiculo['placas'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 font-medium">
                                    {{ number_format($vehiculo['kilometraje_actual']) }} km
                                </div>
                            </td>
                            
                            @foreach(['motor', 'transmision', 'hidraulico'] as $sistema)
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $alerta = $vehiculo['alertas'][$sistema];
                                    $badgeClass = '';
                                    $dotClass = '';
                                    
                                    switch ($alerta['estado']) {
                                        case 'Vencido':
                                            $badgeClass = 'bg-red-100 text-red-800 border-red-200';
                                            $dotClass = 'bg-red-500';
                                            break;
                                        case 'Próximo':
                                            $badgeClass = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                                            $dotClass = 'bg-yellow-500';
                                            break;
                                        default:
                                            $badgeClass = 'bg-green-100 text-green-800 border-green-200';
                                            $dotClass = 'bg-green-500';
                                    }
                                @endphp
                                
                                <div class="space-y-1">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-2 h-2 {{ $dotClass }} rounded-full"></div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $badgeClass }}">
                                            {{ $alerta['estado'] }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        @if($alerta['kilometros_restantes'] > 0)
                                            {{ number_format($alerta['kilometros_restantes']) }} km restantes
                                        @else
                                            {{ number_format(abs($alerta['kilometros_restantes'])) }} km excedidos
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        Próximo: {{ number_format($alerta['kilometraje_proximo_servicio']) }} km
                                    </div>
                                </div>
                            </td>
                            @endforeach
                            
                            <!-- Columnas de documentos -->
                            @foreach(['poliza', 'derecho'] as $tipoDocumento)
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $documento = $vehiculo['documentos'][$tipoDocumento];
                                    $badgeClass = '';
                                    $dotClass = '';
                                    
                                    switch ($documento['estado']) {
                                        case 'Vencido':
                                            $badgeClass = 'bg-red-100 text-red-800 border-red-200';
                                            $dotClass = 'bg-red-500';
                                            break;
                                        case 'Próximo a Vencer':
                                            $badgeClass = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                                            $dotClass = 'bg-yellow-500';
                                            break;
                                        case 'OK':
                                            $badgeClass = 'bg-green-100 text-green-800 border-green-200';
                                            $dotClass = 'bg-green-500';
                                            break;
                                        case 'Sin Fecha':
                                        default:
                                            $badgeClass = 'bg-gray-100 text-gray-800 border-gray-200';
                                            $dotClass = 'bg-gray-500';
                                    }
                                @endphp
                                
                                <div class="space-y-1">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-2 h-2 {{ $dotClass }} rounded-full"></div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $badgeClass }}">
                                            {{ $documento['estado'] }}
                                        </span>
                                    </div>
                                    
                                    @if($documento['estado'] !== 'Sin Fecha')
                                        <div class="text-xs text-gray-500">
                                            @if($documento['dias_restantes'] >= 0)
                                                {{ $documento['dias_restantes'] }} días restantes
                                            @else
                                                {{ abs($documento['dias_restantes']) }} días vencido
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            Vence: {{ $documento['fecha_vencimiento_formateada'] }}
                                        </div>
                                    @else
                                        <div class="text-xs text-gray-400">
                                            Fecha no especificada
                                        </div>
                                    @endif
                                </div>
                            </td>
                            @endforeach
                            
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('vehiculos.show', $vehiculo['id']) }}" 
                                       class="text-blue-600 hover:text-blue-900 transition-colors duration-150">
                                        Ver detalles
                                    </a>
                                    <a href="{{ route('mantenimientos.create', ['vehiculo_id' => $vehiculo['id']]) }}" 
                                       class="text-green-600 hover:text-green-900 transition-colors duration-150">
                                        Nuevo mantenimiento
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center">
                                <div class="text-gray-500">
                                    <div class="text-4xl mb-2">🚗</div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">No hay vehículos registrados</h3>
                                    <p class="text-sm text-gray-500">Agrega vehículos para comenzar a monitorear sus mantenimientos y documentos.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <div class="text-blue-400">ℹ️</div>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Información sobre las alertas</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="font-medium mb-2">🔧 Alertas de Mantenimiento:</h4>
                                <ul class="list-disc list-inside space-y-1">
                                    <li><strong>Vencido:</strong> El vehículo ha superado el kilometraje recomendado.</li>
                                    <li><strong>Próximo:</strong> Faltan {{ config('alertas.mantenimiento_km_umbral', 1000) }} km o menos para el mantenimiento.</li>
                                    <li><strong>OK:</strong> El vehículo está al día con sus mantenimientos.</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-medium mb-2">📋 Alertas de Documentos:</h4>
                                <ul class="list-disc list-inside space-y-1">
                                    <li><strong>Vencido:</strong> El documento ya ha vencido.</li>
                                    <li><strong>Próximo a Vencer:</strong> Faltan {{ config('alertas.vencimiento_documentos_dias', 30) }} días o menos.</li>
                                    <li><strong>OK:</strong> El documento está vigente.</li>
                                    <li><strong>Sin Fecha:</strong> No se ha especificado fecha de vencimiento.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Filtrado dinámico de la tabla
document.addEventListener('alpine:init', () => {
    Alpine.data('alertasController', () => ({
        filtroEstado: 'todos',
        
        init() {
            // Aquí podrías agregar lógica adicional si es necesaria
        },
        
        mostrarFila(vehiculo) {
            if (this.filtroEstado === 'todos') return true;
            
            // Verificar si alguna alerta del vehículo coincide con el filtro
            const alertas = vehiculo.alertas;
            const alertasCoinciden = Object.values(alertas).some(alerta => alerta.estado === this.filtroEstado);
            
            // Verificar si algún documento del vehículo coincide con el filtro
            const documentos = vehiculo.documentos;
            const documentosCoinciden = Object.values(documentos).some(documento => documento.estado === this.filtroEstado);
            
            return alertasCoinciden || documentosCoinciden;
        }
    }))
})
</script>
@endsection
