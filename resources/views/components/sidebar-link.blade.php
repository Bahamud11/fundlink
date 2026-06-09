@props(['active', 'icon'])

@php
$classes = ($active ?? false)
            ? 'w-full h-12 shrink-0 flex items-center gap-2 px-3 rounded-2xl bg-blue-600 text-white transition-all duration-300 font-bold group'
            : 'w-full h-12 shrink-0 flex items-center gap-2 px-3 rounded-2xl text-gray-400 hover:text-gray-600 transition-all duration-200 font-bold group';

if ($active ?? false) {
    $imgName = ($icon === 'profil') ? 'profile_filled' : ($icon . '_filled');
} else {
    $imgName = $icon;
}
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} wire:navigate>
    <img src="{{ asset('images/' . $imgName . '.svg') }}" class="h-6 w-6 object-contain shrink-0 {{ $active ? 'brightness-0 invert' : 'opacity-70 group-hover:opacity-100 transition-opacity' }}" alt="{{ $slot }} icon">
    <span class="font-['Inter'] font-medium text-lg leading-none tracking-[-0.03em] truncate">
        {{ $slot }}
    </span>
</a>
