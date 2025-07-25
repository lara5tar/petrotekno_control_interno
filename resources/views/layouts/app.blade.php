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
            <div class="flex items-center">
                <span class="text-petroyellow mr-4">{{ Auth::user()->name ?? 'Usuario' }}</span>
                <span class="text-sm text-gray-400">v1.0</span>
            </div>
        </div>
        
        <!-- Contenido de la página -->
        <div class="p-6">
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
    
    @stack('scripts')
</body>
</html>
