<x-layouts.staff>
    {{-- Breadcrumb --}}
    <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="text-sm text-gray-500">
            <a href="{{ route('customers.index') }}" class="hover:underline">Customers</a>
            <span class="mx-1">/</span>
            <a href="{{ route('customers.show', $customer) }}" class="hover:underline">{{ $customer->name }}</a>
            <span class="mx-1">/</span>
            <span class="text-gray-700">Process Claim</span>
        </div>

        <button type="button" onclick="window.history.back()"
            class="bg-white border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 transition shadow-sm flex items-center gap-2">
            <i class="fas fa-arrow-left text-sm"></i>
            Go Back to Claim Details
        </button>
    </div>


    {{-- Staff-initiated banner --}}
    <div
        class="mb-4 flex items-center gap-2 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
        <i class="fas fa-user-shield"></i>
        You are processing this claim on behalf of <strong>{{ $customer->name }}</strong>.
        It will be logged under your account.
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-claimant-info :policy="$policy" :customer="$customer" />

        <div class="lg:col-span-2">
            @include($formView, [
                'formData' => $formData,
                'action' => $action,
                'method' => $method,
                'claim' => $claim,
                'context' => 'staff',
            ])
        </div>
    </div>

</x-layouts.staff>
