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
</head>
<body class="bg-gray-100 min-h-screen flex flex-col md:flex-row">
    <!-- Menú lateral (amarillo) -->
    <x-sidebar />
    
    <!-- Contenido principal -->
    <div class="flex-1">
        <!-- Barra superior (negra) -->
        <div class="bg-petrodark text-white p-4 flex justify-between items-center">
            <div class="flex items-center">
                <button class="md:hidden mr-4" id="menu-toggle">
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
    
    <!-- Script para el menú móvil -->
    <script>
        document.getElementById('menu-toggle').addEventListener('click', function() {
            const menu = document.querySelector('.bg-petroyellow');
            menu.classList.toggle('hidden');
        });
    </script>
    
    @stack('scripts')
</body>
</html>
