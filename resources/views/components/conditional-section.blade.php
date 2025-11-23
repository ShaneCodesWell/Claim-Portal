<div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        {{ $question }} @if ($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    <div class="flex gap-4 mb-3">
        <label class="flex items-center">
            <input type="radio" name="{{ $name }}" value="yes"
                @if ($required) required @endif class="mr-2 conditional-radio"
                data-target="{{ $yesSectionId }}" />
            <span class="text-sm text-gray-700">{{ $yesLabel }}</span>
        </label>
        <label class="flex items-center">
            <input type="radio" name="{{ $name }}" value="no"
                @if ($required) required @endif class="mr-2 conditional-radio"
                data-target="{{ $yesSectionId }}" />
            <span class="text-sm text-gray-700">{{ $noLabel }}</span>
        </label>
    </div>

    <div id="{{ $yesSectionId }}" class="conditional-section hidden overflow-hidden mt-3 p-2">
        {{ $slot }}
    </div>
</div>