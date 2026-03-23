@switch($name ?? '')
    @case('spark')
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 3v4M12 17v4M4.9 4.9l2.8 2.8M16.3 16.3l2.8 2.8M3 12h4M17 12h4M4.9 19.1l2.8-2.8M16.3 7.7l2.8-2.8" stroke-width="1.8" stroke-linecap="round"/>
            <circle cx="12" cy="12" r="3.5" stroke-width="1.8"/>
        </svg>
        @break
    @case('target')
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="12" r="7.5" stroke-width="1.8"/>
            <circle cx="12" cy="12" r="3.5" stroke-width="1.8"/>
            <path d="M12 4V2.5M12 21.5V20M20 12h1.5M2.5 12H4" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        @break
    @case('setup')
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <rect x="3.5" y="5" width="17" height="14" rx="3" stroke-width="1.8"/>
            <path d="M8 9.5h8M8 14.5h5" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M18.5 4v2M5.5 4v2" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        @break
    @case('gym')
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M4 10v4M7 8v8M17 8v8M20 10v4" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M7 12h10" stroke-width="1.8" stroke-linecap="round"/>
            <rect x="2.5" y="9" width="1.5" height="6" rx=".75" stroke-width="1.8"/>
            <rect x="20" y="9" width="1.5" height="6" rx=".75" stroke-width="1.8"/>
        </svg>
        @break
    @case('money')
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <rect x="3.5" y="6" width="17" height="12" rx="2.5" stroke-width="1.8"/>
            <circle cx="12" cy="12" r="2.8" stroke-width="1.8"/>
            <path d="M7 9.5h.01M17 14.5h.01" stroke-width="2.2" stroke-linecap="round"/>
        </svg>
        @break
    @case('access')
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 4.5a7.5 7.5 0 0 1 7.5 7.5" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M12 7.5a4.5 4.5 0 0 1 4.5 4.5" stroke-width="1.8" stroke-linecap="round"/>
            <circle cx="12" cy="12" r="1.8" stroke-width="1.8"/>
            <path d="M4.5 19.5 10.7 13.3" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M4.5 19.5h4M4.5 19.5v-4" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        @break
    @case('shield')
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 3.5 18.5 6v5.2c0 4-2.7 7.6-6.5 9.3-3.8-1.7-6.5-5.3-6.5-9.3V6L12 3.5Z" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="m9.4 12.2 1.8 1.8 3.7-4.1" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break
    @case('mobile')
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <rect x="7" y="3.5" width="10" height="17" rx="2.5" stroke-width="1.8"/>
            <path d="M10.5 6.5h3" stroke-width="1.8" stroke-linecap="round"/>
            <circle cx="12" cy="17.2" r=".8" fill="currentColor" stroke="none"/>
        </svg>
        @break
    @case('support')
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M5 12a7 7 0 1 1 14 0v4a2 2 0 0 1-2 2h-1.5v-4H19" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M5 18H4a2 2 0 0 1-2-2v-4h3V18Z" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M9.5 20.5h4" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        @break
    @case('clock')
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="12" r="8.5" stroke-width="1.8"/>
            <path d="M12 7.5v5l3 1.8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break
    @case('growth')
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M4 18.5h16" stroke-width="1.8" stroke-linecap="round"/>
            <path d="m6 15 4-4 3 3 5-6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M15 8h3v3" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break
    @case('control')
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <rect x="4" y="5" width="16" height="14" rx="3" stroke-width="1.8"/>
            <path d="M8 9.5h8M8 12.5h5M8 15.5h3" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        @break
    @default
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="12" r="8" stroke-width="1.8"/>
        </svg>
@endswitch
