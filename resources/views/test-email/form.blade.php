@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">
            🧪 Prueba de Correo Electrónico
        </h1>
        
        <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-700">
                <strong>Destinatario:</strong> analara.stay@gmail.com<br>
                <strong>Propósito:</strong> Envío de correo de prueba
            </p>
        </div>

        <button id="sendTestEmail" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 ease-in-out transform hover:scale-105">
            📧 Enviar Correo de Prueba
        </button>

        <div id="result" class="mt-4 hidden">
            <!-- Resultado se mostrará aquí -->
        </div>

        <div id="loading" class="mt-4 hidden text-center">
            <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-blue-500 bg-blue-100">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Enviando correo...
            </div>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                ← Volver al inicio
            </a>
        </div>
    </div>
</div>

<script>
document.getElementById('sendTestEmail').addEventListener('click', function() {
    const button = this;
    const loading = document.getElementById('loading');
    const result = document.getElementById('result');
    
    button.disabled = true;
    button.classList.add('opacity-50', 'cursor-not-allowed');
    loading.classList.remove('hidden');
    result.classList.add('hidden');
    
    fetch('{{ route("test-email.send") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        loading.classList.add('hidden');
        result.classList.remove('hidden');
        
        if (data.success) {
            result.innerHTML = '<div class="p-4 bg-green-50 border border-green-200 rounded-lg"><div class="flex items-center"><div class="flex-shrink-0"><svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg></div><div class="ml-3"><p class="text-sm font-medium text-green-800">✅ ' + data.message + '</p><p class="text-xs text-green-600 mt-1">Enviado: ' + data.timestamp + '</p></div></div></div>';
        } else {
            result.innerHTML = '<div class="p-4 bg-red-50 border border-red-200 rounded-lg"><div class="flex items-center"><div class="flex-shrink-0"><svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg></div><div class="ml-3"><p class="text-sm font-medium text-red-800">❌ ' + data.message + '</p><p class="text-xs text-red-600 mt-1">Error: ' + data.timestamp + '</p></div></div></div>';
        }
    })
    .catch(error => {
        loading.classList.add('hidden');
        result.classList.remove('hidden');
        result.innerHTML = '<div class="p-4 bg-red-50 border border-red-200 rounded-lg"><p class="text-sm font-medium text-red-800">❌ Error de conexión: ' + error.message + '</p></div>';
    })
    .finally(() => {
        button.disabled = false;
        button.classList.remove('opacity-50', 'cursor-not-allowed');
    });
});
</script>
@endsection