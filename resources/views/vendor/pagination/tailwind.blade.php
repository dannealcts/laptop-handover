@if ($paginator->hasPages())
    <div class="flex flex-col sm:flex-row justify-between items-center mt-6 text-sm text-gray-700">
        <div>
            <span>
                Showing
                <span class="font-semibold">{{ $paginator->firstItem() }}</span>
                to
                <span class="font-semibold">{{ $paginator->lastItem() }}</span>
                of
                <span class="font-semibold">{{ $paginator->total() }}</span>
                results
            </span>
        </div>

        <div class="mt-2 sm:mt-0">
            <nav role="navigation" aria-label="Pagination" class="inline-flex space-x-1">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="px-3 py-1 rounded border border-gray-300 text-gray-400 cursor-not-allowed">
                        &larr;
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1 rounded border border-gray-300 text-blue-600 hover:bg-blue-50">
                        &larr;
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- Dots --}}
                    @if (is_string($element))
                        <span class="px-3 py-1 text-gray-500">{{ $element }}</span>
                    @endif

                    {{-- Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="px-3 py-1 rounded border border-blue-600 bg-blue-600 text-white">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="px-3 py-1 rounded border border-gray-300 text-blue-600 hover:bg-blue-50">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1 rounded border border-gray-300 text-blue-600 hover:bg-blue-50">
                        &rarr;
                    </a>
                @else
                    <span class="px-3 py-1 rounded border border-gray-300 text-gray-400 cursor-not-allowed">
                        &rarr;
                    </span>
                @endif
            </nav>
        </div>
    </div>
@endif
