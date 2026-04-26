<x-layouts.staff>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header with back button -->
        <div class="flex items-center gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-sitemap text-blue-500 text-2xl"></i>
                    Edit Department
                </h2>
                <p class="text-gray-500 text-sm mt-1">
                    Update department information</p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50">
                <h3 class="font-semibold text-gray-800">
                    <i class="fas fa-edit text-blue-500 mr-2"></i>
                    Department Details
                </h3>
            </div>
            <div class="p-6">
                <form action="{{ route('departments.update', $department->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Department Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Department Name <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $department->name) }}"
                                placeholder="e.g., Claims Department"
                                class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                        </div>

                        <!-- Department Code -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Department Code <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="code" value="{{ old('code', $department->code) }}"
                                placeholder="e.g., CLAIM-01"
                                class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                        </div>

                        <!-- Branch  -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Branch</label>
                            <select name="branch_id"
                                class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                                <option value="">Select Branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ old('branch_id', $department->branch_id) == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Department Head -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Department Head
                            </label>
                            <select name="department_head_id"
                                class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                                <option value="">Select Department Head</option>
                                @foreach ($staffMembers as $staff)
                                    <option value="{{ $staff->id }}"
                                        {{ old('department_head_id', $department->department_head_id) == $staff->id ? 'selected' : '' }}>
                                        {{ $staff->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Description (full width) -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" rows="3"
                                class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                placeholder="Brief description of the department's responsibilities">{{ old('description', $department->description) }}</textarea>
                        </div>

                        <!-- Status (is_active) -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Is Active
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                                    {{ old('is_active', $department->is_active) ? 'checked' : '' }}>
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
                        <a href="{{ route('organization') }}?tab=tab-departments"
                            class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white hover:bg-gray-50 transition">
                            <i class="fas fa-arrow-left text-xs"></i>
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition">Update
                            Department
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.staff>
