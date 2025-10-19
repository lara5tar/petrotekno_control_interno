@extends('layouts.auth')

@section('title', 'Verificar Email')

@section('content')
    <h2 class="text-xl font-semibold text-petrodark mb-4">{{ __('Verificar Dirección de Email') }}</h2>
    
    @if (session('resent'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ __('Se ha enviado un nuevo enlace de verificación a su dirección de email.') }}
        </div>
    @endif

    <p class="text-sm text-gray-700 mb-6">
        {{ __('Antes de continuar, por favor verifique su email haciendo clic en el enlace que acabamos de enviarle.') }}
    </p>
    
    <p class="text-sm text-gray-700 mb-6">
        {{ __('Si no recibió el email') }}, puede solicitar otro:
    </p>

    <form method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <div class="mb-6">
            <button type="submit" class="w-full bg-petrodark hover:bg-gray-800 text-white font-medium py-3 px-4 rounded-md transition duration-200">
                {{ __('Reenviar Email de Verificación') }}
            </button>
        </div>
    </form>
@endsection