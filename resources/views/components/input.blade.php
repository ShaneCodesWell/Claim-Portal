<input type="{{ $type }}" name="{{ $name }}" id="{{ $id }}"
    @if ($required) required @endif
    @if ($placeholder) placeholder="{{ $placeholder }}" @endif
    @if ($value) value="{{ $value }}" @endif
    {{ $attributes->merge(['class' => 'w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none']) }} />
