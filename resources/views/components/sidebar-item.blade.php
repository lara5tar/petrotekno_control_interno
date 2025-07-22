{{-- resources/views/components/sidebar-item.blade.php --}}
@props(['route', 'icon', 'active' => false, 'label'])

<a href="{{ $route }}" class="block py-2.5 px-4 transition duration-200 {{ $active ? 'bg-gray-100' : '' }} text-gray-700 font-medium mb-1">
    <div class="flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-gray-700 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
            {!! $icon !!}
        </svg>
        <span class="sidebar-label">{{ $label }}</span>
    </div>
</a>