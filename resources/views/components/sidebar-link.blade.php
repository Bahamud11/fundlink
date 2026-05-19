@props(['active', 'icon'])

@php
$classes = ($active ?? false)
            ? 'flex items-center space-x-4 p-4 rounded-lg bg-blue-600 text-white transition-all duration-300 font-bold group'
            : 'flex items-center space-x-4 p-4 rounded-lg text-gray-400 hover:text-gray-600 transition-all duration-200 font-bold group';

$iconClasses = ($active ?? false)
            ? 'text-white'
            : 'text-gray-400 group-hover:text-gray-600 transition-colors duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} wire:navigate>
    <div class="{{ $iconClasses }}">
        @if($icon === 'home')
            <!-- Layout Grid Icon -->
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" rx="1" />
                <rect x="14" y="3" width="7" height="7" rx="1" />
                <rect x="14" y="14" width="7" height="7" rx="1" />
                <rect x="3" y="14" width="7" height="7" rx="1" />
            </svg>
        @elseif($icon === 'receipt')
            <!-- Rp Circle Icon -->
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <circle cx="12" cy="12" r="9" />
                <path d="M10 8h3a2 2 0 1 1 0 4h-3v4" />
                <path d="M12 12l2 4" />
            </svg>
        @elseif($icon === 'office')
            <!-- Branches Icon -->
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path d="M12 3v3" />
                <path d="M12 18v3" />
                <path d="M3 12h3" />
                <path d="M18 12h3" />
                <path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                <path d="M18 6l-1.5 1.5" />
                <path d="M7.5 16.5L6 18" />
                <path d="M6 6l1.5 1.5" />
                <path d="M16.5 16.5L18 18" />
            </svg>
        @elseif($icon === 'users')
            <!-- User Outline Icon -->
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                <circle cx="10" cy="7" r="4" />
            </svg>
        @elseif($icon === 'profile')
            <!-- User Circle Icon -->
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
            </svg>
        @endif
    </div>
    <span class="text-lg">{{ $slot }}</span>
</a>
