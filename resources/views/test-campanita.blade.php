@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">Test Campanita</h1>
    
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Debug Information</h2>
        
        <p><strong>alertasCount:</strong> {{ $alertasCount ?? 'NO DEFINIDO' }}</p>
        <p><strong>tieneAlertasUrgentes:</strong> {{ $tieneAlertasUrgentes ? 'true' : 'false' }}</p>
        
        <div class="mt-4">
            <p>El ViewComposer debería estar inyectando estas variables automáticamente.</p>
        </div>
    </div>
</div>
@endsection
