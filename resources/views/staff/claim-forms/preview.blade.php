<x-layouts.staff>
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Previewing: {{ $formTemplate->name }}</h2>
            <p class="text-sm text-gray-500">
                Version {{ $formTemplate->version }} · Read-only structural preview — nothing here is saved.
            </p>
        </div>
        <a href="{{ route('claim-form') }}" class="text-sm text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-1"></i> Back to templates
        </a>
    </div>

    <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-6 text-xs text-amber-700">
        <i class="fas fa-eye mr-1"></i>
        Preview mode — fields are shown as they'll appear to a claimant, but are disabled here. No claim data is attached.
    </div>

    {{--
        pointer-events-none disables all interaction without needing every
        field partial to support a "disabled" state individually. Simplest
        thing that works for a v1 preview — revisit if you need staff to be
        able to click into fields (e.g. to test conditional logic live).
    --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 pointer-events-none opacity-90">
        <x-dynamic-form
            :schema="$formTemplate->schema"
            :values="[]"
            context="staff"
            :is-edit="false"
            form-id="previewForm"
            action="#"
            method="POST"
        />
    </div>
</x-layouts.staff>