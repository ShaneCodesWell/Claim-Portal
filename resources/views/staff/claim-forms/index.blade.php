<x-layouts.staff>

    @php
        // Cosmetic mapping only — extend as you add products.
        $productMeta = [
            'motor' => ['icon' => 'fa-car', 'color' => 'blue'],
            'fire' => ['icon' => 'fa-fire', 'color' => 'teal'],
            'general_accident' => ['icon' => 'fa-user-injured', 'color' => 'cyan'],
        ];
    @endphp

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-pen-ruler text-blue-500 text-2xl"></i>
                Claim Form Templates
            </h2>
            <p class="text-gray-500 text-sm mt-1">
                Define and manage the structure of claim forms for each insurance product.
            </p>
        </div>
        <a href="{{ route('create-claim-form') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl text-sm font-medium shadow-sm flex items-center gap-2">
            <i class="fas fa-plus-circle"></i> Create New Form
        </a>
    </div>

    <!-- Grid of form templates -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        @forelse ($templates as $template)
            @php
                $meta = $productMeta[$template->product_type] ?? ['icon' => 'fa-file-lines', 'color' => 'gray'];
                $productLabel = ucfirst(str_replace('_', ' ', $template->product_type));
            @endphp
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition">
                <div class="p-5 border-b border-gray-100 bg-linear-to-r from-{{ $meta['color'] }}-50 to-white">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 bg-{{ $meta['color'] }}-100 rounded-xl flex items-center justify-center">
                            <i class="fas {{ $meta['icon'] }} text-{{ $meta['color'] }}-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">{{ $productLabel }} Claim Form</h3>
                            <p class="text-xs text-gray-500">
                                Version {{ $template->version }} · Last edit: {{ $template->updated_at->format('Y-m-d') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Total fields:</span>
                        <span class="font-medium">{{ $template->field_count }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Used in claims:</span>
                        <span class="font-medium">{{ $template->claims_count }}</span>
                    </div>
                    <div class="flex flex-wrap gap-1 mt-2">
                        @foreach ($template->preview_keys as $key)
                            <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $key }}</span>
                        @endforeach
                        @if ($template->more_count > 0)
                            <span class="text-xs text-gray-400">+{{ $template->more_count }} more</span>
                        @endif
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3 border-t border-gray-300 flex justify-between gap-2">
                    <a href="{{ route('staff.claim-forms.preview', $template->id) }}"
                        class="text-gray-500 text-sm hover:text-gray-700"><i class="fas fa-eye mr-1"></i>View</a>
                    <a href="{{ route('staff.claim-forms.edit', $template->id) }}"
                        class="text-blue-600 text-sm font-medium hover:underline"><i class="fas fa-edit mr-1"></i>Edit Fields</a>
                    <button class="text-red-500 text-sm hover:text-red-700"><i class="fas fa-trash-alt mr-1"></i>Delete</button>
                </div>
            </div>
        @empty
            <div class="md:col-span-2 lg:col-span-3 text-center py-12 text-gray-400">
                <i class="fas fa-folder-open text-3xl mb-2"></i>
                <p>No published form templates yet.</p>
            </div>
        @endforelse

        <!-- "Create new" placeholder card -->
        <a href="{{ route('create-claim-form') }}">
            <div class="bg-white rounded-2xl border-2 border-dashed border-gray-300 flex flex-col items-center justify-center p-6 text-center hover:border-blue-300 transition cursor-pointer h-full">
                <div class="h-12 w-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-plus text-gray-400 text-xl"></i>
                </div>
                <h3 class="font-medium text-gray-700">Create New Form Template</h3>
                <p class="text-xs text-gray-400 mt-1">Add a new claim form type</p>
            </div>
        </a>
    </div>
</x-layouts.staff>