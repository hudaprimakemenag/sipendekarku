@if ($paginator->hasPages())
    <nav style="display: flex; align-items: center; justify-content: space-between; gap: 8px; font-size: 12px;" aria-label="Pagination">
        <span style="color: var(--slate-500);">
            {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }} data
        </span>
        <div style="display: flex; align-items: center; gap: 4px;">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span style="padding: 4px 10px; border: 1px solid var(--slate-200, #e2e8f0); border-radius: 6px; color: var(--slate-300, #cbd5e1); cursor: not-allowed; font-size: 12px;">‹ Prev</span>
            @else
                <button wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled"
                    style="padding: 4px 10px; border: 1px solid var(--slate-200, #e2e8f0); border-radius: 6px; background: white; color: var(--slate-600, #475569); cursor: pointer; font-size: 12px; transition: all .15s;"
                    onmouseover="this.style.background='var(--slate-50,#f8fafc)'"
                    onmouseout="this.style.background='white'">‹ Prev</button>
            @endif

            {{-- Page numbers (show max 5) --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span style="padding: 4px 6px; color: var(--slate-400);">…</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span style="padding: 4px 9px; border-radius: 6px; background: var(--violet-600, #7c3aed); color: white; font-weight: 600; font-size: 12px;">{{ $page }}</span>
                        @else
                            <button wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                style="padding: 4px 9px; border: 1px solid var(--slate-200, #e2e8f0); border-radius: 6px; background: white; color: var(--slate-600, #475569); cursor: pointer; font-size: 12px; transition: all .15s;"
                                onmouseover="this.style.background='var(--slate-50,#f8fafc)'"
                                onmouseout="this.style.background='white'">{{ $page }}</button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <button wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled"
                    style="padding: 4px 10px; border: 1px solid var(--slate-200, #e2e8f0); border-radius: 6px; background: white; color: var(--slate-600, #475569); cursor: pointer; font-size: 12px; transition: all .15s;"
                    onmouseover="this.style.background='var(--slate-50,#f8fafc)'"
                    onmouseout="this.style.background='white'">Next ›</button>
            @else
                <span style="padding: 4px 10px; border: 1px solid var(--slate-200, #e2e8f0); border-radius: 6px; color: var(--slate-300, #cbd5e1); cursor: not-allowed; font-size: 12px;">Next ›</span>
            @endif
        </div>
    </nav>
@endif
