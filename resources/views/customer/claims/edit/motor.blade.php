<x-layouts.app>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div>
            {{-- <x-claimant-info :policy="$claim->policy" :customer="$claim->customer" /> --}}
            <button type="button" onclick="window.history.back()"
                class="bg-white border border-gray-300 hover:bg-gray-50 px-4 py-2 mt-4 rounded-lg text-sm font-medium text-gray-700 transition shadow-sm flex items-center gap-2">
                <i class="fas fa-arrow-left text-sm"></i>
                Go Back to Claim Details
            </button>
        </div>
        <div class="lg:col-span-2">
            @include('partials.forms.motor-form', [
                'formData' => $claim->form_data,
                'action' => route('claims.update', $claim),
                'method' => 'PUT',
                'claim' => $claim,
                'context' => 'customer',
            ])
        </div>
    </div>
</x-layouts.app>
