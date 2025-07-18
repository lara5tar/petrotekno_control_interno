@extends('layouts.auth')

@section('title', 'Registro')

@section('content')
    <h2 class="text-xl font-semibold text-petrodark mb-4">{{ __('Registro No Disponible') }}</h2>
    <p class="text-sm text-gray-700 mb-6">{{ __('El registro público no está disponible. Contacte al administrador.') }}</p>
    
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded mb-4">
        <p class="text-sm">{{ __('Solo los administradores pueden crear nuevas cuentas de usuario.') }}</p>
    </div>

    <div class="mb-6">
        <a href="{{ route('login') }}" class="w-full inline-block text-center bg-petrodark hover:bg-gray-800 text-white font-medium py-3 px-4 rounded-md transition duration-200">
            {{ __('Volver al Login') }}
        </a>
    </div>
@endsection