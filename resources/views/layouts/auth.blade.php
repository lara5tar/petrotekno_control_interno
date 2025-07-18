<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Autenticación') - Petrotekno Control Interno</title>

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
<body class="bg-white min-h-screen flex items-center justify-center">
    <!-- Versión en esquina superior derecha -->
    <div class="absolute top-3 right-5 text-gray-600 font-medium">v1.0</div>
    
    <!-- Contenedor principal centrado -->
    <div class="w-full max-w-md p-6 md:p-8">
        <div class="bg-petroyellow rounded-lg shadow-lg overflow-hidden">
            <div class="p-6 text-center">
                <!-- Logo/Título -->
                <h1 class="text-4xl font-bold text-petrodark mb-6">PETROTEKNO</h1>
                
                <!-- Contenido específico de cada vista -->
                @yield('content')
            </div>
        </div>
        
        <!-- Enlace de regreso al login si no estamos en login -->
        @if(!request()->routeIs('login'))
            <div class="mt-4 text-center">
                <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-petrodark transition duration-200">
                    ← Volver al inicio de sesión
                </a>
            </div>
        @endif
    </div>
</body>
</html>