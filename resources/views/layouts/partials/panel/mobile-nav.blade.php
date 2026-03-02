<nav class="theme-mobile-nav fixed inset-x-0 bottom-0 z-30 border-t p-2 backdrop-blur lg:hidden">
    <div class="mx-auto flex max-w-full gap-2 overflow-x-auto px-1 pb-1">
        @foreach ($navItems as $item)
            @php
                $activePatterns = explode('|', $item['active']);
                $isActive = collect($activePatterns)->contains(fn ($pattern) => request()->routeIs($pattern));
            @endphp
            <a href="{{ route($item['route'], $item['params'] ?? []) }}"
               class="min-w-[84px] shrink-0 rounded-lg px-2 py-2 text-center text-[11px] font-bold uppercase tracking-wide {{ $isActive ? 'theme-nav-mobile-active' : 'theme-nav-mobile-link' }}">
                {{ $item['label'] }}
            </a>
        @endforeach
    </div>
</nav>
