{{--
    Generic schema-driven claim form renderer.

    Props (passed via the <x-dynamic-form> tag):
      :schema      — decoded form_templates.schema array (sections/fields)
      :values      — flat [field_key => value] array. Simple fields hold
                     scalars; repeatable-group/table fields hold arrays of
                     row-assoc-arrays, e.g. $values['your_vehicle_injured']
                     = [['name' => 'A', 'age' => 30, ...], ...]
      :context     — 'customer' | 'staff'
      :is-edit     — bool            (becomes $isEdit below)
      form-id      — string, used as the <form id="">  (becomes $formId below)
      :action      — form action URL
      :method      — 'POST' | 'PUT'

    Field naming convention (so plain FormData -> Laravel array parsing
    works with zero manual JS field-collection code):
      simple field            -> form_data[key]
      branch sub-field        -> form_data[key]
      conditional child field -> form_data[key]
      repeatable-group row    -> form_data[key][i][subkey]
      repeatable-table row    -> form_data[key][i][colkey]
--}}
@props([
    'schema' => ['sections' => []],
    'values' => [],
    'context' => 'customer',
    'isEdit' => false,
    'formId' => 'claimForm',
    'action' => '',
    'method' => 'POST',
])

@php
    $f = $values ?? [];
    $isStaff = ($context ?? 'customer') === 'staff';
@endphp

<form id="{{ $formId }}" data-action="{{ $action }}" data-context="{{ $context }}" class="dynamic-claim-form">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif

    @foreach ($schema['sections'] ?? [] as $section)
        <section class="mb-8" data-section-key="{{ $section['key'] }}">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                {{ $section['title'] }}
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($section['fields'] ?? [] as $field)
                    <x-dynamic-form-field :field="$field" :values="$f" :is-staff="$isStaff" />
                @endforeach
            </div>
        </section>
    @endforeach

    {{-- Fixed, non-schema sections stay exactly as before — not admin-editable --}}
    {{ $slot ?? '' }}
</form>