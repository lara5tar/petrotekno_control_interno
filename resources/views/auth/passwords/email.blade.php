@extends('layouts.auth')

@section('title', 'Recuperar Contraseña')

@section('content')
    <h2 class="text-xl font-semibold text-petrodark mb-4">{{ __('Recuperar Contraseña') }}</h2>
    <p class="text-sm text-gray-700 mb-6">{{ __('Ingrese su correo electrónico para recibir un enlace de recuperación') }}</p>

    @if (session('status'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-4">
            <label for="email" class="block text-gray-800 font-medium mb-2">{{ __('Correo Electrónico') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                class="w-full px-3 py-2 bg-white border border-gray-300 text-gray-800 rounded-md focus:outline-none focus:ring-2 focus:ring-petrodark focus:border-transparent @error('email') border-red-500 @enderror">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <button type="submit" class="w-full bg-petrodark hover:bg-gray-800 text-white font-medium py-3 px-4 rounded-md transition duration-200">
                {{ __('Enviar Enlace de Recuperación') }}
            </button>
        </div>
    </form>
@endsection