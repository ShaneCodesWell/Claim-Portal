<x-layouts.app>
    <a href="{{ route('dashboard') }}"
        class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white hover:bg-gray-50 transition">
        <i class="fas fa-arrow-left text-xs"></i>
        Back
    </a>
    <div class="flex justify-center">
        <div class="grid grid-cols-1 gap-6 px-4 sm:px-8 lg:px-32 w-full">
            <div class="lg:col-span-2">
                @include('partials.forms.motor-form', [
                    'formData' => $formData,
                    'action' => route('claims.store'),
                    'method' => 'POST',
                    'claim' => null,
                    'context' => 'customer',
                ])
            </div>
        </div>
    </div>
</x-layouts.app>
