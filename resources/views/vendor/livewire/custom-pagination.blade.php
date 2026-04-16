@php
// Scroll disabled intentionally — pagination stays in place for better UX
$scrollIntoViewJsSnippet = '';
@endphp

<style>
    .custom-pagination-modern .page-link {
        border-radius: 8px !important;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid transparent;
        color: #6c757d;
        font-weight: 600;
        margin: 0 4px;
        transition: all 0.2s ease;
        background-color: transparent;
    }
    .custom-pagination-modern .page-item.active .page-link {
        background-color: #0d6efd !important;
        color: white !important;
        box-shadow: 0 4px 6px -1px rgba(13, 110, 253, 0.2);
    }
    .custom-pagination-modern .page-item.disabled .page-link {
        color: #cbd5e0;
        background-color: transparent;
    }
    .custom-pagination-modern .page-item:not(.active):not(.disabled) .page-link:hover {
        background-color: #f8f9fa;
        color: #0d6efd;
        border-color: #e2e8f0;
    }
    .fly-up-dropdown {
        top: auto !important;
        bottom: calc(100% + 5px) !important;
        margin-top: 0 !important;
    }
</style>
<div>
<div>
        <nav class="d-flex justify-items-center justify-content-between">
            <div class="d-flex justify-content-between flex-fill d-sm-none">
                <ul class="pagination">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link">@lang('pagination.previous')</span>
                        </li>
                    @else
                        <li class="page-item">
                            <button type="button" dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}" class="page-link" wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled">@lang('pagination.previous')</button>
                        </li>
                    @endif

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <li class="page-item">
                            <button type="button" dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}" class="page-link" wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled">@lang('pagination.next')</button>
                        </li>
                    @else
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link" aria-hidden="true">@lang('pagination.next')</span>
                        </li>
                    @endif
                </ul>
            </div>

            <div class="d-none flex-sm-fill d-sm-flex align-items-sm-center justify-content-sm-between">
                <div class="d-flex align-items-center">
                    <p class="small text-muted mb-0 mr-3" style="margin-bottom: 0;">
                        {!! __('Showing') !!}
                        <span class="font-weight-bold">{{ $paginator->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="font-weight-bold">{{ $paginator->lastItem() }}</span>
                        {!! __('of') !!}
                        <span class="font-weight-bold">{{ $paginator->total() }}</span>
                        {!! __('entries') !!}
                    </p>
                    <div class="d-flex align-items-center border-left pl-3 ml-2 border-secondary" style="border-left: 1px solid #cbd5e0;" x-data="{ open: false }" @click.away="open = false">
                        <span class="text-muted small mr-2 font-weight-bold">Tampilkan</span>
                        
                        <div class="position-relative">
                            <button type="button" @click="open = !open" class="btn bg-white shadow-sm d-flex align-items-center justify-content-between px-3 rounded-pill" style="border: 1px solid #e2e8f0; height: 32px; width: 75px;">
                                <span class="font-weight-bold text-primary" style="font-size: 0.85rem;" x-text="$wire.perPage"></span>
                                <i class="fe fe-chevron-down ml-1 text-muted" :style="open ? 'transform: rotate(180deg);' : ''" style="transition: transform 0.2s; font-size: 0.8rem;"></i>
                            </button>
                            
                            <div class="dropdown-menu shadow-lg rounded-lg p-1 fly-up-dropdown" :class="{ 'd-block': open }" x-show="open" x-transition style="display: none; position: absolute; left: 0; z-index: 1060; min-width: 75px; border: 1px solid #edf2f7;">
                                <button type="button" class="dropdown-item text-center font-weight-bold rounded-sm mb-1" :class="{ 'bg-primary text-white': $wire.perPage == 10 }" style="padding: 0.4rem;" @click="$wire.set('perPage', 10); open = false">10</button>
                                <button type="button" class="dropdown-item text-center font-weight-bold rounded-sm mb-1" :class="{ 'bg-primary text-white': $wire.perPage == 15 }" style="padding: 0.4rem;" @click="$wire.set('perPage', 15); open = false">15</button>
                                <button type="button" class="dropdown-item text-center font-weight-bold rounded-sm mb-1" :class="{ 'bg-primary text-white': $wire.perPage == 25 }" style="padding: 0.4rem;" @click="$wire.set('perPage', 25); open = false">25</button>
                                <button type="button" class="dropdown-item text-center font-weight-bold rounded-sm mb-1" :class="{ 'bg-primary text-white': $wire.perPage == 50 }" style="padding: 0.4rem;" @click="$wire.set('perPage', 50); open = false">50</button>
                                <button type="button" class="dropdown-item text-center font-weight-bold rounded-sm" :class="{ 'bg-primary text-white': $wire.perPage == 100 }" style="padding: 0.4rem;" @click="$wire.set('perPage', 100); open = false">100</button>
                            </div>
                        </div>

                        <span class="text-muted small ml-2 font-weight-bold">Data</span>
                    </div>
                </div>

                <div>
                    <ul class="pagination custom-pagination-modern m-0">
                        {{-- Previous Page Link --}}
                        @if ($paginator->onFirstPage())
                            <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                                <span class="page-link" aria-hidden="true">&lsaquo;</span>
                            </li>
                        @else
                            <li class="page-item">
                                <button type="button" dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}" class="page-link" wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" aria-label="@lang('pagination.previous')">&lsaquo;</button>
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($elements as $element)
                            {{-- "Three Dots" Separator --}}
                            @if (is_string($element))
                                <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                            @endif

                            {{-- Array Of Links --}}
                            @if (is_array($element))
                                @foreach ($element as $page => $url)
                                    @if ($page == $paginator->currentPage())
                                        <li class="page-item active" wire:key="paginator-{{ $paginator->getPageName() }}-page-{{ $page }}" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                                    @else
                                        <li class="page-item" wire:key="paginator-{{ $paginator->getPageName() }}-page-{{ $page }}"><button type="button" class="page-link" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" >{{ $page }}</button></li>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($paginator->hasMorePages())
                            <li class="page-item">
                                <button type="button" dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}" class="page-link" wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" aria-label="@lang('pagination.next')">&rsaquo;</button>
                            </li>
                        @else
                            <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                                <span class="page-link" aria-hidden="true">&rsaquo;</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>
</div>
</div>
