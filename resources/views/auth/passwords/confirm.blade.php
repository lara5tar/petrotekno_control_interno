@extends('layouts.auth')

@section('title', 'Confirmar Contraseña')

@section('content')
    <h2 class="text-xl font-semibold text-petrodark mb-4">{{ __('Confirmar Contraseña') }}</h2>
    <p class="text-sm text-gray-700 mb-6">{{ __('Por favor confirme su contraseña antes de continuar.') }}</p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-4">
            <label for="password" class="block text-gray-800 font-medium mb-2">{{ __('Contraseña') }}</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="w-full px-3 py-2 bg-white border border-gray-300 text-gray-800 rounded-md focus:outline-none focus:ring-2 focus:ring-petrodark focus:border-transparent @error('password') border-red-500 @enderror">
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <button type="submit" class="w-full bg-petrodark hover:bg-gray-800 text-white font-medium py-3 px-4 rounded-md transition duration-200">
                {{ __('Confirmar Contraseña') }}
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