<div class="flex items-center justify-center px-4 py-3 sm:px-6" style="border-top: 1px solid #e5e7eb; background: #fff;">
    <div>
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination" style="box-shadow: none !important; display: inline-flex; gap: 4px;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="@lang('pagination.previous')" style="display: inline-flex; align-items: center; justify-content: center; padding: 0 !important; border: 1px solid #e5e7eb !important; border-radius: 8px !important; background: #f9fafb; color: #374151; font-size: 13px; font-weight: 500; text-decoration: none; width: 36px !important; height: 36px !important; margin: 0 !important; cursor: not-allowed; box-sizing: border-box !important; opacity: 0.6;">
                    <span aria-hidden="true">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" style="width: 16px; height: 16px;">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')" style="display: inline-flex; align-items: center; justify-content: center; padding: 0 !important; border: 1px solid #e5e7eb !important; border-radius: 8px !important; background: #fff; color: #374151; font-size: 13px; font-weight: 500; text-decoration: none; width: 36px !important; height: 36px !important; margin: 0 !important; cursor: pointer; box-sizing: border-box !important;">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" style="width: 16px; height: 16px;">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span aria-disabled="true" style="display: inline-flex; align-items: center; justify-content: center; padding: 0 !important; border: 1px solid #e5e7eb !important; border-radius: 8px !important; background: #fff; color: #374151; font-size: 13px; font-weight: 500; text-decoration: none; width: 36px !important; height: 36px !important; margin: 0 !important; cursor: default; box-sizing: border-box !important;">
                        <span>{{ $element }}</span>
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" style="display: inline-flex; align-items: center; justify-content: center; padding: 0 !important; border: 1px solid #490d59 !important; border-radius: 8px !important; background-color: #490d59 !important; color: white !important; font-size: 13px; font-weight: 500; text-decoration: none; width: 36px !important; height: 36px !important; margin: 0 !important; cursor: default; box-sizing: border-box !important;">
                                <span>{{ $page }}</span>
                            </span>
                        @else
                            <a href="{{ $url }}" aria-label="Go to page {{ $page }}" style="display: inline-flex; align-items: center; justify-content: center; padding: 0 !important; border: 1px solid #e5e7eb !important; border-radius: 8px !important; background: #fff; color: #374151; font-size: 13px; font-weight: 500; text-decoration: none; width: 36px !important; height: 36px !important; margin: 0 !important; cursor: pointer; box-sizing: border-box !important;">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')" style="display: inline-flex; align-items: center; justify-content: center; padding: 0 !important; border: 1px solid #e5e7eb !important; border-radius: 8px !important; background: #fff; color: #374151; font-size: 13px; font-weight: 500; text-decoration: none; width: 36px !important; height: 36px !important; margin: 0 !important; cursor: pointer; box-sizing: border-box !important;">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" style="width: 16px; height: 16px;">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
            @else
                <span aria-disabled="true" aria-label="@lang('pagination.next')" style="display: inline-flex; align-items: center; justify-content: center; padding: 0 !important; border: 1px solid #e5e7eb !important; border-radius: 8px !important; background: #f9fafb; color: #374151; font-size: 13px; font-weight: 500; text-decoration: none; width: 36px !important; height: 36px !important; margin: 0 !important; cursor: not-allowed; box-sizing: border-box !important; opacity: 0.6;">
                    <span aria-hidden="true">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" style="width: 16px; height: 16px;">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </span>
            @endif
        </nav>
    </div>
</div>
