@extends('layouts.auth')

@section('title', 'Restablecer Contraseña')

@section('content')
    <h2 class="text-xl font-semibold text-petrodark mb-4">{{ __('Restablecer Contraseña') }}</h2>
    <p class="text-sm text-gray-700 mb-6">{{ __('Ingrese su nueva contraseña') }}</p>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-4">
            <label for="email" class="block text-gray-800 font-medium mb-2">{{ __('Correo Electrónico') }}</label>
            <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus
                class="w-full px-3 py-2 bg-white border border-gray-300 text-gray-800 rounded-md focus:outline-none focus:ring-2 focus:ring-petrodark focus:border-transparent @error('email') border-red-500 @enderror">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="block text-gray-800 font-medium mb-2">{{ __('Nueva Contraseña') }}</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="w-full px-3 py-2 bg-white border border-gray-300 text-gray-800 rounded-md focus:outline-none focus:ring-2 focus:ring-petrodark focus:border-transparent @error('password') border-red-500 @enderror">
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password-confirm" class="block text-gray-800 font-medium mb-2">{{ __('Confirmar Contraseña') }}</label>
            <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password"
                class="w-full px-3 py-2 bg-white border border-gray-300 text-gray-800 rounded-md focus:outline-none focus:ring-2 focus:ring-petrodark focus:border-transparent">
        </div>

        <div class="mb-6">
            <button type="submit" class="w-full bg-petrodark hover:bg-gray-800 text-white font-medium py-3 px-4 rounded-md transition duration-200">
                {{ __('Restablecer Contraseña') }}
            </button>
        </div>
    </form>
@endsection