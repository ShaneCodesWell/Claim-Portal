<textarea 
    name="{{ $name }}" 
    id="{{ $id }}" 
    rows="{{ $rows }}"
    @if($required) required @endif
    @if($placeholder) placeholder="{{ $placeholder }}" @endif
    {{ $attributes->merge(['class' => 'w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none outline-none']) }}
>{{ $value }}</textarea>