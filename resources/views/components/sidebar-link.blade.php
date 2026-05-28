@props(['active', 'icon'])

@php
$classes = ($active ?? false)
            ? 'flex items-center space-x-4 p-4 rounded-lg bg-blue-600 text-white transition-all duration-300 font-bold group'
            : 'flex items-center space-x-4 p-4 rounded-lg text-gray-400 hover:text-gray-600 transition-all duration-200 font-bold group';

if ($active ?? false) {
    $imgName = ($icon === 'profil') ? 'profile filled' : ($icon . ' filled');
} else {
    $imgName = $icon;
}
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} wire:navigate>
    <img src="{{ asset('images/' . $imgName . '.svg') }}" class="h-6 w-6 object-contain {{ $active ? 'brightness-0 invert' : 'opacity-70 group-hover:opacity-100 transition-opacity' }}" alt="{{ $slot }} icon">
    <span class="text-lg">{{ $slot }}</span>
</a>
