<x-layouts.staff>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header with back button -->
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('organization') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-store text-blue-500 text-2xl"></i>
                    Edit Branch
                </h2>
                <p class="text-gray-500 text-sm mt-1">Update branch information</p>
            </div>
        </div>

        <!-- Edit Form Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50">
                <h3 class="font-semibold text-gray-800">
                    <i class="fas fa-edit text-blue-500 mr-2"></i>
                    Branch Details
                </h3>
            </div>
            <div class="p-6">
                <form action="{{ route('branches.update', $branch->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Branch Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Branch Name <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="name" placeholder="Head Office" value="{{ $branch->name }}"
                                class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                        </div>

                        <!-- Branch Code -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Branch Code <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="code" placeholder="HO-001" value="{{ $branch->code }}"
                                class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" placeholder="branch@vanguardassurance.com"
                                value="{{ $branch->email }}"
                                class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="phone" placeholder="030 266 6485" value="{{ $branch->phone }}"
                                class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                        </div>

                        <!-- Branch Head (optional) -->
                        {{-- <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Branch Head</label>
                            <select name="branch_head"
                                class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                                <option value="">Select Branch Head</option>
                                @foreach ($staffMembers as $staff)
                                    <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                @endforeach
                            </select>
                        </div> --}}

                        <!-- Address (full width) -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <textarea name="address" rows="2"
                                class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                placeholder="P.O. Box 1868, Accra Central, Ghana">{{ $branch->address }}</textarea>
                        </div>

                        <!-- Status (is_active) -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Is Active
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                                    {{ $branch->is_active ? 'checked' : '' }}>
                                <div
                                    class="relative w-11 h-6 bg-gray-200 rounded-full
                                    peer peer-checked:after:translate-x-full
                                    after:content-[''] after:absolute after:top-0.5
                                    after:left-0.5 after:bg-white after:rounded-full
                                    after:h-5 after:w-5 after:transition-all
                                    peer-checked:bg-blue-600">
                                </div>
                                <span class="ml-3 text-sm text-gray-700">
                                    Is Active
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end gap-3">
                        <a href="{{ route('organization') }}?tab=tab-branches"
                            class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white hover:bg-gray-50 transition">
                            <i class="fas fa-arrow-left text-xs"></i>
                            Back
                        </a>
                        <a href="{{ route('organization') }}?tab=tab-branches"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">Cancel</a>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition">Update
                            Branch</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.staff>
