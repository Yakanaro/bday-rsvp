@props(['type'])

<svg {{ $attributes->merge(['class' => 'drink-icon']) }} viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($type)
        @case('tumbler')
            <path d="M5 5h14l-1.6 14.2a2 2 0 0 1-2 1.8H8.6a2 2 0 0 1-2-1.8z" fill="none"/>
            <path d="M6.2 12h11.6" fill="none"/>
            <rect x="9" y="13.5" width="3.2" height="3.2" rx=".6" transform="rotate(12 10.6 15.1)" fill="none"/>
            @break
        @case('shot')
            <path d="M7 5h10l-1.6 14.5a1.6 1.6 0 0 1-1.6 1.5H10.2a1.6 1.6 0 0 1-1.6-1.5z" fill="none"/>
            <path d="M8 10h8" fill="none"/>
            @break
        @case('soft')
            <path d="M7 7h10l-1.3 12.4a1.8 1.8 0 0 1-1.8 1.6H10.1a1.8 1.8 0 0 1-1.8-1.6z" fill="none"/>
            <path d="M12.5 7 14.5 2.5h2.5" fill="none"/>
            <path d="M7.6 12.5h8.8" fill="none"/>
            @break
        @case('wine-red')
            <path d="M7.6 7h8.8c-.5 2.8-2.2 4.5-4.4 4.5S8.1 9.8 7.6 7z" fill="currentColor" stroke="none"/>
            <path d="M7 3h10c0 5.2-2.2 8.5-5 8.5S7 8.2 7 3z" fill="none"/>
            <path d="M12 11.5V20M8.5 20.5h7" fill="none"/>
            @break
        @default
            <path d="M7 3h10c0 5.2-2.2 8.5-5 8.5S7 8.2 7 3z" fill="none"/>
            <path d="M7.6 7h8.8" fill="none"/>
            <path d="M12 11.5V20M8.5 20.5h7" fill="none"/>
    @endswitch
</svg>
