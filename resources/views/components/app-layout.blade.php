{{-- This is a simple layout component for your application --}}
<div>
    @if (isset($title))
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">{{ $title }}</h2>
        </div>
    @endif
    
    {{ $slot }}
</div>