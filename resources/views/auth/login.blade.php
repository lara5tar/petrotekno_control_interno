<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - Petrotekno Control Interno</title>

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
                        laravel: '#FF2D20',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-yellow-400 min-h-screen flex items-center justify-center">
    <div class="absolute top-3 right-5 text-gray-800 font-medium">v1.0</div>
    
    <div class="w-full max-w-md p-6 md:p-8">
        <div class="text-center mb-8">
            <img src="{{ asset('images/petrotekno_logo.svg') }}" alt="Petrotekno Logo" class="max-w-[200px] h-auto mx-auto" onerror="this.onerror=null; this.src='{{ asset('images/petrotekno_logo.png') }}'; if(this.src.indexOf('petrotekno_logo.png') !== -1) this.onerror=null;">
        </div>
        
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gray-900 text-white p-4 flex items-center gap-3 font-medium text-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
                Control de Inventario de Vehículos
            </div>
            
            <div class="p-6">
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <p>{{ __('Error en las credenciales.') }}</p>
                    </div>
                @endif
                
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="email" class="block text-gray-800 font-medium mb-2">{{ __('Correo Electrónico') }}</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent">
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="block text-gray-800 font-medium mb-2">{{ __('Contraseña') }}</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent">
                    </div>
                    
                    <div class="mb-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}
                                class="h-4 w-4 text-yellow-500 focus:ring-yellow-400 border-gray-300 rounded">
                            <label for="remember" class="ml-2 text-sm text-gray-700">
                                {{ __('Recordar credenciales') }}
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <button type="submit" class="w-full bg-gray-900 hover:bg-black text-white font-medium py-3 px-4 rounded-md transition duration-200">
                            {{ __('Iniciar Sesión') }}
                        </button>
                    </div>
                    
                    <div class="flex justify-between items-center text-sm">
                        @if (Route::has('password.request'))
                            <a class="text-gray-800 hover:underline" href="{{ route('password.request') }}">
                                {{ __('¿Olvidó su contraseña?') }}
                            </a>
                        @endif
                        
                        @if (Route::has('register'))
                            <a class="text-gray-800 hover:underline" href="{{ route('register') }}">
                                {{ __('Registrarse') }}
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>