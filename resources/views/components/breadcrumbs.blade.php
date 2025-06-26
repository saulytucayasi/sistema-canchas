@props(['items' => []])

<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol role="list" class="flex items-center space-x-2">
        <!-- Home Icon -->
        <li>
            <div>
                <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-500 transition-colors duration-200">
                    <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    <span class="sr-only">Dashboard</span>
                </a>
            </div>
        </li>

        @foreach($items as $index => $item)
        <li>
            <div class="flex items-center">
                <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                @if($loop->last)
                    <span class="ml-2 text-sm font-medium text-gray-500" aria-current="page">{{ $item['title'] }}</span>
                @else
                    <a href="{{ $item['url'] ?? '#' }}" class="ml-2 text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors duration-200">
                        {{ $item['title'] }}
                    </a>
                @endif
            </div>
        </li>
        @endforeach
    </ol>
</nav>