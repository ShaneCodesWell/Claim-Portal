<x-layouts.staff>

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
        {{-- <a href="{{ route('create-claim-form') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl text-sm font-medium shadow-sm flex items-center gap-2">
            <i class="fas fa-plus-circle"></i> Create New Form
        </a> --}}
    </div>

    <!-- Grid of form templates -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Motor Claim Form -->
        <div
            class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md hover:border-blue-100 transition group">
            <div class="p-5 flex items-start gap-4">
                <div
                    class="h-11 w-11 shrink-0 bg-blue-50 rounded-xl flex items-center justify-center group-hover:bg-blue-100 transition">
                    <i class="fas fa-car text-blue-600 text-lg"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="font-bold text-gray-800 truncate">Motor Claim Form</h3>
                    <p class="text-xs text-gray-400 mt-0.5">
                        <i class="fas fa-clock mr-1"></i>Last edited Nov 1, 2024
                    </p>
                </div>
            </div>

            <div class="border-t border-gray-100 px-5 py-3 bg-gray-50/60">
                <a href="{{ route('claim-form-motor') }}"
                    class="inline-flex items-center gap-1.5 text-sm font-medium text-blue-600 hover:text-blue-700 transition">
                    <i class="fas fa-eye"></i> View form
                </a>
            </div>
        </div>

        <!-- Fire Claim Form -->
        <div
            class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md hover:border-blue-100 transition group">
            <div class="p-5 flex items-start gap-4">
                <div
                    class="h-11 w-11 shrink-0 bg-blue-50 rounded-xl flex items-center justify-center group-hover:bg-blue-100 transition">
                    <i class="fas fa-fire text-blue-600 text-lg"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="font-bold text-gray-800 truncate">Fire Claim Form</h3>
                    <p class="text-xs text-gray-400 mt-0.5">
                        <i class="fas fa-clock mr-1"></i>Last edited Nov 1, 2024
                    </p>
                </div>
            </div>

            <div class="border-t border-gray-100 px-5 py-3 bg-gray-50/60">
                <a href="{{ route('claim-form-fire') }}"
                    class="inline-flex items-center gap-1.5 text-sm font-medium text-blue-600 hover:text-blue-700 transition">
                    <i class="fas fa-eye"></i> View form
                </a>
            </div>
        </div>

        <!-- Travel Claim Form -->
        <div
            class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md hover:border-blue-100 transition group">
            <div class="p-5 flex items-start gap-4">
                <div
                    class="h-11 w-11 shrink-0 bg-blue-50 rounded-xl flex items-center justify-center group-hover:bg-blue-100 transition">
                    <i class="fas fa-plane text-blue-600 text-lg"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="font-bold text-gray-800 truncate">Travel Claim Form</h3>
                    <p class="text-xs text-gray-400 mt-0.5">
                        <i class="fas fa-clock mr-1"></i>Last edited Nov 1, 2024
                    </p>
                </div>
            </div>

            <div class="border-t border-gray-100 px-5 py-3 bg-gray-50/60">
                <a href="{{ route('claim-form-travel') }}"
                    class="inline-flex items-center gap-1.5 text-sm font-medium text-blue-600 hover:text-blue-700 transition">
                    <i class="fas fa-eye"></i> View form
                </a>
            </div>
        </div>
    </div>
</x-layouts.staff>
