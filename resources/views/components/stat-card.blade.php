@props([
    'title',
    'value',
    'icon',
    'color' => 'blue'
])

@php
    $colors = [
        'blue' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'icon' => 'text-blue-600'],
        'green' => ['bg' => 'bg-green-100', 'text' => 'text-green-600', 'icon' => 'text-green-600'],
        'yellow' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-600', 'icon' => 'text-yellow-600'],
        'purple' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'icon' => 'text-purple-600'],
    ];
    $colorConfig = $colors[$color] ?? $colors['blue'];
@endphp

<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center">
        <div class="p-3 {{ $colorConfig['bg'] }} rounded-full">
            {!! $icon !!}
        </div>
        <div class="ml-4">
            <h3 class="text-sm font-medium text-gray-600">{{ $title }}</h3>
            <p class="text-2xl font-bold text-gray-900">{{ $value }}</p>
        </div>
    </div>
</div>