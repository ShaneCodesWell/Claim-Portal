<x-layouts.staff>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-pen-ruler text-indigo-500 text-2xl"></i>
                Claim Form Templates
            </h2>
            <p class="text-gray-500 text-sm mt-1">
                Define and manage the structure of claim forms for each insurance product.
            </p>
        </div>
        <a href="{{ route('create-claim-form') }}"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-xl text-sm font-medium shadow-sm flex items-center gap-2">
            <i class="fas fa-plus-circle"></i> Create New Form
        </a>
    </div>

    <!-- Grid of form templates -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Motor Claim Form -->
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition">
            <div class="p-5 border-b border-gray-100 bg-linear-to-r from-blue-50 to-white">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-blue-100 rounded-xl flex items-center justify-center"><i
                            class="fas fa-car text-blue-600 text-lg"></i></div>
                    <div>
                        <h3 class="font-bold text-gray-800">Motor Claim Form</h3>
                        <p class="text-xs text-gray-500">Version 2.1 · Last edit: 2024-11-01</p>
                    </div>
                </div>
            </div>
            <div class="p-5 space-y-3">
                <div class="flex justify-between text-sm"><span class="text-gray-500">Total fields:</span><span
                        class="font-medium">8</span></div>
                <div class="flex justify-between text-sm"><span class="text-gray-500">Used in claims:</span><span
                        class="font-medium">42</span></div>
                <div class="flex flex-wrap gap-1 mt-2">
                    <span class="text-xs bg-gray-100 px-2 py-1 rounded">incident_date</span>
                    <span class="text-xs bg-gray-100 px-2 py-1 rounded">loss_type</span>
                    {{-- <span class="text-xs bg-gray-100 px-2 py-1 rounded">amount</span> --}}
                    <span class="text-xs bg-gray-100 px-2 py-1 rounded">description</span>
                    <span class="text-xs text-gray-400">+4 more</span>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3 border-t border-gray-300 flex justify-between gap-2">
                <button class="text-indigo-600 text-sm font-medium hover:underline"><i class="fas fa-edit mr-1"></i>Edit
                    Fields</button>
                <button class="text-gray-500 text-sm hover:text-gray-700"><i
                        class="fas fa-copy mr-1"></i>Duplicate</button>
                <button class="text-red-500 text-sm hover:text-red-700"><i
                        class="fas fa-trash-alt mr-1"></i>Delete</button>
            </div>
        </div>

        <!-- Fire Claim Form -->
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition">
            <div class="p-5 border-b border-gray-100 bg-linear-to-r from-teal-50 to-white">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-teal-100 rounded-xl flex items-center justify-center"><i
                            class="fas fa-fire text-teal-600 text-lg"></i></div>
                    <div>
                        <h3 class="font-bold text-gray-800">Fire Claim Form</h3>
                        <p class="text-xs text-gray-500">Version 1.4 · Last edit: 2024-10-15</p>
                    </div>
                </div>
            </div>
            <div class="p-5 space-y-3">
                <div class="flex justify-between text-sm"><span class="text-gray-500">Total fields:</span><span
                        class="font-medium">7</span></div>
                <div class="flex justify-between text-sm"><span class="text-gray-500">Used in claims:</span><span
                        class="font-medium">28</span></div>
                <div class="flex flex-wrap gap-1"><span
                        class="text-xs bg-gray-100 px-2 py-1 rounded">incident_date</span><span
                        class="text-xs bg-gray-100 px-2 py-1 rounded">loss_type</span><span
                        class="text-xs bg-gray-100 px-2 py-1 rounded">amount</span></div>
            </div>
            <div class="bg-gray-50 px-5 py-3 border-t border-gray-300 flex justify-between gap-2">
                <button class="text-indigo-600 text-sm font-medium"><i class="fas fa-edit mr-1"></i>Edit
                    Fields</button>
                <button class="text-gray-500 text-sm"><i class="fas fa-copy mr-1"></i>Duplicate</button>
                <button class="text-red-500 text-sm"><i class="fas fa-trash-alt mr-1"></i>Delete</button>
            </div>
        </div>

        <!-- Travel Claim Form -->
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition">
            <div class="p-5 border-b border-gray-100 bg-linear-to-r from-cyan-50 to-white">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-cyan-100 rounded-xl flex items-center justify-center"><i
                            class="fas fa-plane text-cyan-600 text-lg"></i></div>
                    <div>
                        <h3 class="font-bold text-gray-800">Travel Claim Form</h3>
                        <p class="text-xs text-gray-500">Version 1.2 · Last edit: 2024-09-20</p>
                    </div>
                </div>
            </div>
            <div class="p-5 space-y-3">
                <div class="flex justify-between text-sm"><span class="text-gray-500">Total fields:</span><span
                        class="font-medium">9</span></div>
                <div class="flex justify-between text-sm"><span class="text-gray-500">Used in claims:</span><span
                        class="font-medium">15</span></div>
                <div class="flex flex-wrap gap-1"><span
                        class="text-xs bg-gray-100 px-2 py-1 rounded">destination</span><span
                        class="text-xs bg-gray-100 px-2 py-1 rounded">flight_no</span><span
                        class="text-xs bg-gray-100 px-2 py-1 rounded">amount</span><span
                        class="text-xs text-gray-400">+6 more</span></div>
            </div>
            <div class="bg-gray-50 px-5 py-3 border-t border-gray-300 flex justify-between gap-2">
                <button class="text-indigo-600 text-sm font-medium"><i class="fas fa-edit mr-1"></i>Edit
                    Fields</button>
                <button class="text-gray-500 text-sm"><i class="fas fa-copy mr-1"></i>Duplicate</button>
                <button class="text-red-500 text-sm"><i class="fas fa-trash-alt mr-1"></i>Delete</button>
            </div>
        </div>

        <!-- Life Claim Form (inactive/archived example) -->
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden opacity-75">
            <div class="p-5 border-b border-gray-100 bg-gray-50">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-gray-200 rounded-xl flex items-center justify-center"><i
                            class="fas fa-heartbeat text-gray-500 text-lg"></i></div>
                    <div>
                        <h3 class="font-bold text-gray-600">Life Claim Form</h3>
                        <p class="text-xs text-gray-400">Archived · No longer active</p>
                    </div>
                </div>
            </div>
            <div class="p-5">
                <div class="flex justify-between text-sm text-gray-400"><span>Total fields: 6</span><span>Used: 0
                        (last year)</span></div>
            </div>
            <div class="bg-gray-50 px-5 py-3 border-t border-gray-200 flex justify-between">
                <button class="text-indigo-400 text-sm"><i class="fas fa-archive mr-1"></i>Restore</button>
                <button class="text-red-400 text-sm"><i class="fas fa-trash-alt"></i></button>
            </div>
        </div>

        <!-- "Create new" placeholder card -->
        <a href="{{ route('create-claim-form') }}">
            <div
                class="bg-white rounded-2xl border-2 border-dashed border-gray-300 flex flex-col items-center justify-center p-6 text-center hover:border-indigo-300 transition cursor-pointer">
                <div class="h-12 w-12 bg-gray-100 rounded-full flex items-center justify-center mb-3"><i
                        class="fas fa-plus text-gray-400 text-xl"></i></div>
                <h3 class="font-medium text-gray-700">Create New Form Template</h3>
                <p class="text-xs text-gray-400 mt-1">Add a new claim form type</p>
            </div>
        </a>
    </div>
</x-layouts.staff>
