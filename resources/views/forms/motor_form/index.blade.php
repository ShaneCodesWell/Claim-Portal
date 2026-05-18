<x-layouts.app>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-claimant-info :policy="$policy" :customer="$customer" />
        <div class="lg:col-span-2">
            @include('partials.forms.motor-form', [
                'formData' => $formData,
                'action'   => route('claims.store'),
                'method'   => 'POST',
                'claim'    => null,
                'context'  => 'customer',
            ])
        </div>
    </div>
</x-layouts.app>