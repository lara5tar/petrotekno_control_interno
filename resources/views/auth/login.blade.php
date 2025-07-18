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
                        petroyellow: '#FCCA00',
                        petrodark: '#161615',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-white min-h-screen flex items-center justify-center">
    <div class="absolute top-3 right-5 text-gray-600 font-medium">v1.0</div>
    
    <div class="w-full max-w-md p-6 md:p-8">
        <div class="bg-petroyellow rounded-lg shadow-lg overflow-hidden">
            <div class="p-6 text-center">
                <h1 class="text-4xl font-bold text-petrodark mb-6">PETROTEKNO</h1>
                
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        @if($errors->has('email') || $errors->has('password'))
                            <p>{{ __('Las credenciales proporcionadas son incorrectas.') }}</p>
                        @else
                            <p>{{ __('Error en las credenciales.') }}</p>
                        @endif
                    </div>
                @endif
                
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="email" class="block text-gray-800 font-medium mb-2">{{ __('Correo Electrónico') }}</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                            class="w-full px-3 py-2 bg-white border border-gray-300 text-gray-800 rounded-md focus:outline-none focus:ring-2 focus:ring-petrodark focus:border-transparent @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="block text-gray-800 font-medium mb-2">{{ __('Contraseña') }}</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="w-full px-3 py-2 bg-white border border-gray-300 text-gray-800 rounded-md focus:outline-none focus:ring-2 focus:ring-petrodark focus:border-transparent @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}
                                class="h-4 w-4 bg-white text-petrodark focus:ring-petrodark border-gray-300 rounded">
                            <label for="remember" class="ml-2 text-sm text-gray-700">
                                {{ __('Recordar credenciales') }}
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <button type="submit" class="w-full bg-petrodark hover:bg-gray-800 text-white font-medium py-3 px-4 rounded-md transition duration-200">
                            {{ __('Iniciar Sesión') }}
                        </button>
                    </div>
                    
                    <div class="flex justify-between items-center text-sm">
                        @if (Route::has('password.request'))
                            <a class="text-gray-800 hover:text-petrodark" href="{{ route('password.request') }}">
                                {{ __('¿Olvidó su contraseña?') }}
                            </a>
                        @endif
                        
                        @if (Route::has('register'))
                            <a class="text-gray-800 hover:text-petrodark" href="{{ route('register') }}">
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