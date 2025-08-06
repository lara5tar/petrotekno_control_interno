<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - Petrotekno Control Interno</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <style>[x-cloak] { display: none !important; }</style>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Alpine.js Controllers -->
    <script>
        window.formController = function() {
            return {
                crearUsuario: false,
                passwordType: 'aleatoria',
                fileStatus: {},
                init() {
                    this.crearUsuario = false;
                    this.passwordType = 'aleatoria';
                    this.fileStatus = {};
                },
                handleFileInput(event, type) {
                    const file = event.target.files[0];
                    if (file) {
                        this.fileStatus[type] = `Archivo seleccionado: ${file.name}`;
                    }
                }
            }
        }
    </script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        petroyellow: '#FCCA00',
                        petrodark: '#161615',
                    }
                }
            }
        }
    </script>
    
    <!-- Script para aplicar estado inicial sin animación -->
    <script>
        // Aplicar estado inmediatamente antes de que se muestre la página
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            
            // Deshabilitar transiciones temporalmente
            sidebar.style.transition = 'none';
            
            if (isCollapsed) {
                sidebar.classList.remove('md:w-64');
                sidebar.classList.add('md:w-16');
                // Ocultar textos inmediatamente
                const labels = sidebar.querySelectorAll('.sidebar-label');
                const title = sidebar.querySelector('.sidebar-title');
                const subtitle = sidebar.querySelector('.sidebar-subtitle');
                labels.forEach(label => label.classList.add('hidden'));
                if (title) title.classList.add('hidden');
                if (subtitle) subtitle.classList.add('hidden');
            } else {
                sidebar.classList.remove('md:w-16');
                sidebar.classList.add('md:w-64');
            }
            
            // Reactivar transiciones después de un pequeño delay
            setTimeout(() => {
                sidebar.style.transition = '';
                sidebar.classList.add('transition-all', 'duration-300', 'ease-in-out');
            }, 50);
        });
    </script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col md:flex-row">
    <!-- Menú lateral (amarillo) -->
    <div id="sidebar" class="bg-petroyellow w-full md:w-64 md:min-h-screen flex flex-col">
        <x-sidebar />
    </div>
    
    <!-- Contenido principal -->
    <div class="flex-1">
        <!-- Barra superior (negra) -->
        <div class="bg-petrodark text-white p-4 flex justify-between items-center">
            <div class="flex items-center">
                <!-- Botón para móviles -->
                <button class="md:hidden mr-4" id="menu-toggle-mobile">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <!-- Botón para escritorio -->
                <button class="hidden md:block mr-4" id="menu-toggle-desktop">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <h1 class="text-xl font-medium">@yield('header', 'Dashboard')</h1>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Botón de Alertas -->
                <a href="{{ route('kilometrajes.alertas') }}" 
                   class="relative p-2 rounded-full hover:bg-gray-700 transition-colors duration-200 group"
                   title="Ver alertas de mantenimiento">
                    <!-- Icono de campana -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white group-hover:text-petroyellow transition-colors duration-200" fill="currentColor" viewBox="0 0 24 24">
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
                <span class="text-petroyellow">{{ Auth::user()->name ?? 'Usuario' }}</span>
                <span class="text-sm text-gray-400">v1.0</span>
            </div>
        </div>
        
        <!-- Contenido de la página -->
        <div class="p-6">
            <!-- Alertas globales -->
            @if(session('success'))
                @php
                    $containsPassword = str_contains(session('success'), 'Contraseña generada');
                @endphp
                
                @if($containsPassword)
                    {{-- Alert especial para contraseñas generadas --}}
                    <div class="mb-6 bg-gradient-to-r from-green-50 to-blue-50 border-2 border-green-400 text-green-800 px-6 py-4 rounded-lg shadow-lg relative" role="alert" id="password-alert">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <h3 class="text-lg font-bold text-green-800 mb-2">¡Usuario Creado Exitosamente!</h3>
                                <div class="text-sm">{!! session('success') !!}</div>
                            </div>
                            <button type="button" class="ml-4 flex-shrink-0 text-green-400 hover:text-green-600" onclick="document.getElementById('password-alert').style.display='none'">
                                <span class="sr-only">Cerrar</span>
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @else
                    {{-- Alert normal para otros mensajes de éxito --}}
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert" id="success-alert">
                        <strong class="font-bold">¡Éxito!</strong>
                        <span class="block sm:inline">{!! session('success') !!}</span>
                        <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="document.getElementById('success-alert').style.display='none'">
                            <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <title>Cerrar</title>
                                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                            </svg>
                        </span>
                    </div>
                @endif
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert" id="error-alert">
                    <strong class="font-bold">¡Error!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="document.getElementById('error-alert').style.display='none'">
                        <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <title>Cerrar</title>
                            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                        </svg>
                    </span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert" id="validation-errors">
                    <strong class="font-bold">¡Errores de validación!</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="document.getElementById('validation-errors').style.display='none'">
                        <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <title>Cerrar</title>
                            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                        </svg>
                    </span>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
    
    <!-- Script para el menú -->
    <script>
        // Funcionalidad para móviles (ocultar/mostrar)
        document.getElementById('menu-toggle-mobile').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('hidden');
        });
        
        // Funcionalidad para escritorio (colapsar/expandir)
        document.getElementById('menu-toggle-desktop').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const isCollapsed = sidebar.classList.contains('md:w-16');
            
            if (isCollapsed) {
                // Expandir
                sidebar.classList.remove('md:w-16');
                sidebar.classList.add('md:w-64');
                // Mostrar textos
                const labels = sidebar.querySelectorAll('.sidebar-label');
                const title = sidebar.querySelector('.sidebar-title');
                const subtitle = sidebar.querySelector('.sidebar-subtitle');
                labels.forEach(label => label.classList.remove('hidden'));
                if (title) title.classList.remove('hidden');
                if (subtitle) subtitle.classList.remove('hidden');
                // Guardar estado
                localStorage.setItem('sidebarCollapsed', false);
            } else {
                // Colapsar
                sidebar.classList.remove('md:w-64');
                sidebar.classList.add('md:w-16');
                // Ocultar textos
                const labels = sidebar.querySelectorAll('.sidebar-label');
                const title = sidebar.querySelector('.sidebar-title');
                const subtitle = sidebar.querySelector('.sidebar-subtitle');
                labels.forEach(label => label.classList.add('hidden'));
                if (title) title.classList.add('hidden');
                if (subtitle) subtitle.classList.add('hidden');
                // Guardar estado
                localStorage.setItem('sidebarCollapsed', true);
            }
        });
    </script>
    
    <!-- Script para auto-ocultar alertas -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-ocultar alertas después de 5 segundos
            const alerts = ['success-alert', 'error-alert', 'validation-errors'];
            alerts.forEach(alertId => {
                const alert = document.getElementById(alertId);
                if (alert) {
                    setTimeout(() => {
                        alert.style.opacity = '0';
                        setTimeout(() => {
                            alert.style.display = 'none';
                        }, 300);
                    }, 5000);
                }
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>
