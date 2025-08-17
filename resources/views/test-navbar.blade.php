<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Navbar - Alertas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .petroyellow { color: #FFD700; }
        .bg-petroblue { background-color: #1B365D; }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navbar simplificado para pruebas -->
    <header class="bg-petroblue shadow-lg sticky top-0 z-40">
        <div class="flex items-center justify-between h-16 px-6">
            <div class="flex items-center">
                <h1 class="text-xl font-medium text-white">Test Navbar - PetroTekno</h1>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Botón de Alertas -->
                <a href="/kilometrajes/alertas/mantenimiento" 
                   class="relative p-2 rounded-full hover:bg-gray-700 transition-colors duration-200 group"
                   title="Ver alertas de mantenimiento">
                    <!-- Icono de campana -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white group-hover:text-yellow-400 transition-colors duration-200" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M10 20h4c0 1.1-.9 2-2 2s-2-.9-2-2zm10-2.65V19H4v-1.65l2-1.88v-5.15C6 7.4 7.56 5.1 10 4.34v-.38c0-1.42 1.49-2.5 2.99-1.76.65.32 1.01 1.03 1.01 1.76v.39c2.44.75 4 3.06 4 5.98v5.15l2 1.88z"/>
                    </svg>
                    
                    <!-- Badge con número de alertas -->
                    @if($alertasCount > 0)
                        <span class="absolute -top-1 -right-1 h-5 w-5 {{ $tieneAlertasUrgentes ? 'bg-red-500' : 'bg-yellow-500' }} text-white text-xs font-bold rounded-full flex items-center justify-center animate-pulse">
                            {{ $alertasCount > 99 ? '99+' : $alertasCount }}
                        </span>
                    @endif
                </a>
                
                <!-- Información del usuario -->
                <span class="text-yellow-400">Test User</span>
            </div>
        </div>
    </header>

    <!-- Contenido principal -->
    <div class="container mx-auto mt-8 p-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-4">Prueba del Botón de Alertas</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-blue-800 mb-2">Estado de Alertas</h3>
                    <ul class="space-y-2">
                        <li><strong>Total de alertas:</strong> {{ $alertasCount }}</li>
                        <li><strong>Alertas urgentes:</strong> {{ $tieneAlertasUrgentes ? 'Sí' : 'No' }}</li>
                        <li><strong>Color del badge:</strong> 
                            <span class="px-2 py-1 rounded text-white text-sm {{ $tieneAlertasUrgentes ? 'bg-red-500' : 'bg-yellow-500' }}">
                                {{ $tieneAlertasUrgentes ? 'Rojo (Urgente)' : 'Amarillo (Normal)' }}
                            </span>
                        </li>
                    </ul>
                </div>

                <div class="bg-green-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-green-800 mb-2">Instrucciones</h3>
                    <ol class="list-decimal list-inside space-y-2">
                        <li>Haz clic en el botón de campana en el navbar</li>
                        <li>Deberías ser redirigido a la página de alertas</li>
                        <li>El badge debe mostrar el número correcto de alertas</li>
                        <li>Si hay alertas críticas, el badge será rojo</li>
                    </ol>
                </div>
            </div>

            <div class="mt-6 text-center">
                <a href="/kilometrajes/alertas/mantenimiento" 
                   class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                    Ir a Alertas Directamente
                </a>
            </div>
        </div>
    </div>
</body>
</html>
