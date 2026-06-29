@props([
    'sidebar' => true,
])

@php
    $logoUrl = branding('logo.url');
    $logoText = branding('logo.text', 'H');
    $gradientFrom = branding('logo.gradient_from_hex', '#ef4444');
    $gradientTo = branding('logo.gradient_to_hex', '#f97316');
    $name = branding('name', config('app.name'));
    $hasImage = !empty($logoUrl);
    $logoStyle = $hasImage
        ? 'background:none;overflow:hidden;'
        : 'background:linear-gradient(to bottom right,' . $gradientFrom . ',' . $gradientTo . ');';
@endphp

@if ($sidebar)
    <flux:sidebar.brand name="{{ $name }}" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md"
            style="{{ $logoStyle }}">
            @if ($hasImage)
                <img src="{{ $logoUrl }}" alt="{{ $name }}" class="size-full object-cover" />
            @else
                <span class="text-sm font-bold text-white">{{ $logoText }}</span>
            @endif
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="{{ $name }}" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md"
            style="{{ $logoStyle }}">
            @if ($hasImage)
                <img src="{{ $logoUrl }}" alt="{{ $name }}" class="size-full object-cover" />
            @else
                <span class="text-sm font-bold text-white">{{ $logoText }}</span>
            @endif
        </x-slot>
    </flux:brand>
@endif
