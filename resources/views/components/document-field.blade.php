@props([
    'title' => '',
    'subtitle' => null,
    'hasDocument' => false,
    'documentId' => null,
    'directUrl' => null,
    'showDownload' => true,
    'hasData' => false, // Si tiene datos (número de documento) aunque no tenga archivo
])

@php
    $hasAnyDocument = $hasDocument || !empty($directUrl);
    $hasAnyInfo = $hasAnyDocument || $hasData || !empty($subtitle);
@endphp

<li class="py-3 flex items-center justify-between">
    <div class="flex items-center">
        <svg class="w-4 h-4 mr-2 text-{{ $hasAnyInfo ? 'blue' : 'gray' }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <div>
            <span class="text-sm font-medium text-gray-800">{{ $title }}</span>
            @if($subtitle)
                {!! $subtitle !!}
            @elseif(!$hasAnyInfo)
                <p class="text-xs text-red-500">No disponible</p>
            @else
                <p class="text-xs text-gray-500">Documento disponible</p>
            @endif
        </div>
    </div>
    
    @if($hasDocument && $documentId)
        <div class="flex space-x-2">
            <button data-document-id="{{ $documentId }}" class="btn-view-document bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Ver
            </button>
            @if($showDownload)
                <button data-document-id="{{ $documentId }}" class="btn-download-document bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Descargar
                </button>
            @endif
        </div>
    @elseif(!empty($directUrl))
        <div class="flex space-x-2">
            <button class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200" 
                    onclick="viewPersonalDocument('{{ asset('storage/' . $directUrl) }}')"
                    title="Ver {{ $title }}">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Ver
            </button>
        </div>
    @elseif($hasData || !empty($subtitle))
        <div class="text-xs text-gray-400 italic">
            Sin archivo
        </div>
    @else
        <div class="text-xs text-red-500">
            Faltante
        </div>
    @endif
</li>
