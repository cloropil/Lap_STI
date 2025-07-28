@php
$colorClass = match($color) {
    'blue' => 'bg-blue-100 border-blue-300 text-blue-700',
    'green' => 'bg-green-100 border-green-300 text-green-700',
    'yellow' => 'bg-yellow-100 border-yellow-300 text-yellow-800',
    default => 'bg-gray-100 border-gray-300 text-gray-800',
};
@endphp

<div class="border rounded-lg p-4 {{ $colorClass }}">
    <h5 class="text-sm font-medium text-gray-700 mb-1">{{ $title }}</h5>
    <p class="text-3xl font-bold">{{ $value }}</p>
</div>
