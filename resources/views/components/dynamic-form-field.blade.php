@props(['field', 'values' => [], 'isStaff' => false])

@php
    $key = $field['key'];
    $type = $field['type'];
    $value = $values[$key] ?? '';
    $required = !empty($field['required']);
    $wide = in_array($type, ['textarea', 'repeatable-group', 'repeatable-table', 'radio']);
@endphp

<div class="{{ $wide ? 'md:col-span-2' : '' }} mb-4" data-field-key="{{ $key }}" data-field-type="{{ $type }}">

    @switch($type)

        {{-- ── Simple text-like inputs ───────────────────────────────── --}}
        @case('text')
        @case('date')
        @case('number')
            <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ $field['label'] }} @if ($required)<span class="text-red-500">*</span>@endif
            </label>
            <input type="{{ $type === 'number' ? 'number' : ($type === 'date' ? 'date' : 'text') }}"
                name="form_data[{{ $key }}]" value="{{ $value }}" {{ $required ? 'required' : '' }}
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
            @break

        @case('textarea')
            <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ $field['label'] }} @if ($required)<span class="text-red-500">*</span>@endif
            </label>
            <textarea name="form_data[{{ $key }}]" rows="3" {{ $required ? 'required' : '' }}
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">{{ $value }}</textarea>
            @break

        {{-- ── Select, optionally with branches (e.g. driver_type) ──── --}}
        @case('select')
            <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ $field['label'] }} @if ($required)<span class="text-red-500">*</span>@endif
            </label>
            <select name="form_data[{{ $key }}]" {{ $required ? 'required' : '' }}
                data-branch-select="{{ !empty($field['branches']) ? $key : '' }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                <option value="">Select</option>
                @foreach ($field['options'] as $opt)
                    @php
                        $optValue = is_array($opt) ? $opt['value'] : $opt;
                        $optLabel = is_array($opt) ? $opt['label'] : $opt;
                        $branchKey = $field['option_branch_map'][$optValue] ?? null;
                    @endphp
                    <option value="{{ $optValue }}" data-branch="{{ $branchKey }}"
                        {{ $value === $optValue ? 'selected' : '' }}>{{ $optLabel }}</option>
                @endforeach
            </select>

            @if (!empty($field['branches']))
                @foreach ($field['branches'] as $branchKey => $branch)
                    <div data-branch-panel="{{ $key }}:{{ $branchKey }}" class="hidden mt-3 pl-4 border-l-2 border-blue-200">
                        @if (($branch['mode'] ?? null) === 'readonly_profile')
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 grid grid-cols-1 md:grid-cols-3 gap-4">
                                @foreach ($branch['fields'] as $sub)
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600">{{ $sub['label'] }}</label>
                                        <input type="text" readonly value="{{ $values[$sub['key']] ?? '' }}"
                                            class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded text-gray-700">
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($branch['fields'] as $sub)
                                    <x-dynamic-form-field :field="$sub" :values="$values" :is-staff="$isStaff" />
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
            @break

        {{-- ── Radio (yes/no) with optional conditional children ────── --}}
        @case('radio')
            <label class="block text-sm font-medium text-gray-700 mb-2">
                {{ $field['label'] }} @if ($required)<span class="text-red-500">*</span>@endif
            </label>
            <div class="flex flex-wrap gap-4">
                @foreach (($field['options'] ?? ['yes', 'no']) as $opt)
                    <label class="flex items-center">
                        <input type="radio" name="form_data[{{ $key }}]" value="{{ $opt }}"
                            data-conditional-radio="{{ !empty($field['conditional_fields']) ? $key : '' }}"
                            {{ $value === $opt ? 'checked' : '' }} class="mr-2">
                        <span class="capitalize">{{ $opt }}</span>
                    </label>
                @endforeach
            </div>

            @if (!empty($field['conditional_fields']))
                <div data-conditional-panel="{{ $key }}" class="{{ $value === 'yes' ? '' : 'hidden' }} mt-3 pl-4 border-l-2 border-blue-200">
                    @foreach ($field['conditional_fields'] as $cond)
                        <x-dynamic-form-field :field="$cond" :values="$values" :is-staff="$isStaff" />
                    @endforeach
                </div>
            @endif
            @break

        {{-- ── Repeatable group (card-style, e.g. Injured Persons) ──── --}}
        @case('repeatable-group')
            <div data-repeatable-group="{{ $key }}">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                    <label class="block text-sm font-medium text-gray-700">{{ $field['label'] }}</label>
                    <button type="button" data-add-group-row="{{ $key }}"
                        class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                        {{ $field['add_button_label'] ?? '+ Add' }}
                    </button>
                </div>

                <div class="space-y-4" data-group-rows="{{ $key }}">
                    @php $rows = $value ?: [[]]; @endphp
                    @foreach ($rows as $i => $row)
                        <div class="group-row border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @foreach ($field['subfields'] as $sub)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $sub['label'] }}</label>
                                        @if ($sub['type'] === 'textarea')
                                            <textarea name="form_data[{{ $key }}][{{ $i }}][{{ $sub['key'] }}]" rows="2"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">{{ $row[$sub['key']] ?? '' }}</textarea>
                                        @else
                                            <input type="{{ $sub['type'] === 'number' ? 'number' : ($sub['type'] === 'date' ? 'date' : 'text') }}"
                                                name="form_data[{{ $key }}][{{ $i }}][{{ $sub['key'] }}]"
                                                value="{{ $row[$sub['key']] ?? '' }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-2 flex justify-end">
                                <button type="button" data-remove-group-row class="text-red-600 hover:text-red-800 text-sm">
                                    <i class="fas fa-trash mr-1"></i> Remove
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Template used by JS to clone new rows — subfields declared once here --}}
                <template data-group-template="{{ $key }}">
                    <div class="group-row border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach ($field['subfields'] as $sub)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $sub['label'] }}</label>
                                    @if ($sub['type'] === 'textarea')
                                        <textarea data-name-template="form_data[{{ $key }}][__i__][{{ $sub['key'] }}]" rows="2"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                                    @else
                                        <input type="{{ $sub['type'] === 'number' ? 'number' : ($sub['type'] === 'date' ? 'date' : 'text') }}"
                                            data-name-template="form_data[{{ $key }}][__i__][{{ $sub['key'] }}]"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-2 flex justify-end">
                            <button type="button" data-remove-group-row class="text-red-600 hover:text-red-800 text-sm">
                                <i class="fas fa-trash mr-1"></i> Remove
                            </button>
                        </div>
                    </div>
                </template>
            </div>
            @break

        {{-- ── Repeatable table (row-based, e.g. Particulars of Claim) ── --}}
        @case('repeatable-table')
            @php
                $hasNumeric = collect($field['columns'])->contains(fn($c) => in_array($c['type'], ['number', 'calculated']));
            @endphp
            <div data-repeatable-table="{{ $key }}">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                    <label class="block text-sm font-medium text-gray-700">{{ $field['label'] }}</label>
                    <button type="button" data-add-table-row="{{ $key }}"
                        class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                        {{ $field['add_button_label'] ?? '+ Add Row' }}
                    </button>
                </div>

                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="w-full text-sm text-left border-collapse" data-table-key="{{ $key }}">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                @foreach ($field['columns'] as $col)
                                    <th class="px-3 py-2 font-semibold text-gray-600">{{ $col['label'] }}</th>
                                @endforeach
                                <th class="px-3 py-2 w-8"></th>
                            </tr>
                        </thead>
                        <tbody data-table-rows="{{ $key }}">
                            @php $rows = $value ?: [[]]; @endphp
                            @foreach ($rows as $i => $row)
                                <tr class="table-row border-b border-gray-100">
                                    @foreach ($field['columns'] as $col)
                                        <td class="px-2 py-2">
                                            <input type="{{ $col['type'] === 'date' ? 'date' : ($col['type'] === 'text' ? 'text' : 'number') }}"
                                                step="{{ $col['type'] === 'text' ? '' : '0.01' }}"
                                                name="form_data[{{ $key }}][{{ $i }}][{{ $col['key'] }}]"
                                                value="{{ $row[$col['key']] ?? '' }}"
                                                data-col-key="{{ $col['key'] }}"
                                                data-col-type="{{ $col['type'] }}"
                                                data-formula="{{ $col['formula'] ?? '' }}"
                                                {{ $col['type'] === 'calculated' ? 'readonly' : '' }}
                                                class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg {{ $col['type'] === 'calculated' ? 'bg-gray-50' : '' }}">
                                        </td>
                                    @endforeach
                                    <td class="px-2 py-2 text-center">
                                        <button type="button" data-remove-table-row class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        @if ($hasNumeric)
                            <tfoot class="bg-gray-50 border-t border-gray-200">
                                <tr>
                                    <td colspan="{{ count($field['columns']) }}" class="px-3 py-2 text-right font-semibold text-gray-700">Total:</td>
                                    <td class="px-2 py-2 font-bold text-gray-900" data-table-total="{{ $key }}">0.00</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>

                <template data-table-row-template="{{ $key }}">
                    <tr class="table-row border-b border-gray-100">
                        @foreach ($field['columns'] as $col)
                            <td class="px-2 py-2">
                                <input type="{{ $col['type'] === 'date' ? 'date' : ($col['type'] === 'text' ? 'text' : 'number') }}"
                                    step="{{ $col['type'] === 'text' ? '' : '0.01' }}"
                                    data-name-template="form_data[{{ $key }}][__i__][{{ $col['key'] }}]"
                                    data-col-key="{{ $col['key'] }}"
                                    data-col-type="{{ $col['type'] }}"
                                    data-formula="{{ $col['formula'] ?? '' }}"
                                    {{ $col['type'] === 'calculated' ? 'readonly' : '' }}
                                    class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg {{ $col['type'] === 'calculated' ? 'bg-gray-50' : '' }}">
                            </td>
                        @endforeach
                        <td class="px-2 py-2 text-center">
                            <button type="button" data-remove-table-row class="text-red-500 hover:text-red-700">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                </template>
            </div>
            @break

        @default
            <p class="text-xs text-red-500">Unknown field type: {{ $type }}</p>
    @endswitch
</div>