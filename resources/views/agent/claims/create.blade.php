<x-layouts.agent>
    {{-- Breadcrumb --}}
    <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <button type="button" onclick="window.history.back()"
            class="bg-white border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 transition shadow-sm flex items-center gap-2">
            <i class="fas fa-arrow-left text-sm"></i>
            Go Back to Claim Details
        </button>
    </div>

    {{-- Agent-initiated banner --}}
    <div
        class="mb-4 flex items-center gap-2 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
        <i class="fas fa-user-shield"></i>
        You are processing this claim on behalf of <strong>{{ $customer->name }}</strong>.
        It will be logged under your account.
    </div>

    {{-- Centered form --}}
    <div class="max-w-4xl mx-auto">
        @include($formView, [
            'formData' => $formData,
            'action' => $action,
            'method' => $method,
            'claim' => $claim,
            'context' => 'agent',
        ])
    </div>
</x-layouts.agent>
