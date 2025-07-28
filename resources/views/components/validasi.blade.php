@props([
    'label' => '',
    'name' => '',
    'type' => 'text',
    'value' => '',
    'required' => false,
    'textarea' => false,
])

<div>
    <label for="{{ $name }}" class="block text-sm text-gray-500 font-medium mb-1">
        {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
    </label>

    @if($textarea)
        <textarea
            name="{{ $name }}"
            id="{{ $name }}"
            rows="3"
            class="w-full border @error($name) border-red-500 @else border-gray-300 @enderror rounded-md text-sm"
            @if($required) required @endif
        >{{ old($name, $value) }}</textarea>
    @else
        <input
            type="{{ $type }}"  
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ old($name, $value) }}"
            class="w-full border @error($name) border-red-500 @else border-gray-300 @enderror rounded-md text-sm"
            @if($required) required @endif
        >
    @endif

    @error($name)
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>
