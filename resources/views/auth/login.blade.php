@extends('layouts.auth')

@section('title', 'Iniciar Sesión')

@section('content')
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
        
        @if (Route::has('password.request'))
            <div class="text-center">
                <a class="text-sm text-gray-800 hover:text-petrodark" href="{{ route('password.request') }}">
                    {{ __('¿Olvidó su contraseña?') }}
                </a>
            </div>
        @endif
    </form>
@endsection