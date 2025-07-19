@props(['items' => []])

<div class="mb-4">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            @foreach($items as $index => $item)
                <li class="inline-flex items-center">
                    @if($index === 0)
                        {{-- Primera item (Home) --}}
                        @if(isset($item['url']))
                            <a href="{{ $item['url'] }}" class="text-gray-700 hover:text-petroyellow flex items-center">
                                @if(isset($item['icon']) && $item['icon'])
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                    </svg>
                                @endif
                                {{ $item['label'] }}
                            </a>
                        @else
                            <span class="text-gray-500 flex items-center">
                                @if(isset($item['icon']) && $item['icon'])
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                    </svg>
                                @endif
                                {{ $item['label'] }}
                            </span>
                        @endif
                    @else
                        {{-- Separador --}}
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            @if(isset($item['url']) && $index < count($items) - 1)
                                {{-- Link intermedio --}}
                                <a href="{{ $item['url'] }}" class="text-gray-700 hover:text-petroyellow ml-1 md:ml-2">
                                    {{ $item['label'] }}
                                </a>
                            @else
                                {{-- Último item (actual) --}}
                                <span class="text-gray-500 ml-1 md:ml-2">{{ $item['label'] }}</span>
                            @endif
                        </div>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
</div>
