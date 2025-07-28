@props([
    'label' => '',
    'name' => '',
    'required' => false,
    'options' => [],
    'selected' => '',
])

<div>
    <label for="{{ $name }}" class="block text-sm text-gray-500 font-medium mb-1">
        {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
    </label>
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        @if($required) required @endif
        class="w-full border border-gray-300 rounded-md text-sm"
    >
        <option value="">-- Pilih --</option>
        @foreach ($options as $key => $val)
            <option value="{{ $key }}" {{ (old($name, $selected) == $key) ? 'selected' : '' }}>{{ $val }}</option>
        @endforeach
    </select>
    @error($name)
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>
