<x-layouts.staff>
    {{-- {{ dd($staff) }} --}}
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-users text-blue-500 text-2xl"></i>
                Staff Management
            </h2>
            <p class="text-gray-500 text-sm mt-1">
                Edit existing staff members individually.
            </p>
        </div>
    </div>

    <!-- Two column layout: left form, right bulk upload -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Single Staff Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-user-edit text-blue-500"></i> Edit Staff Member
            </h3>
            <form action="{{ route('staff.update', $staff->id) }}" id="singleStaffForm" class="space-y-4" method="POST">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                        <input type="text" name="name" value="{{ $staff->name }}" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                        <input type="email" name="email" value="{{ $staff->email }}" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="text" name="phone" value="{{ $staff->phone }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select name="role" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            @foreach ($roles as $role)
                                <option value="{{ $role }}" {{ $staff->role == $role ? 'selected' : '' }}>
                                    {{ $roleLabels[$role] }}
                                </option>
                            @endforeach
                        </select>
                    </div> 
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                        <select name="department_id" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="">Select Department</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}" {{ $staff->department_id == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Branch
                        </label>

                        <select name="branch_id" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="">Select Branch</option>

                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" {{ $staff->branch_id == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    {{-- <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                        <input type="password" name="password" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password *</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    </div> --}}
                </div>
                <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Administrator Access
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_admin" value="1" class="sr-only peer" {{ $staff->is_admin ? 'checked' : '' }}>
                            <div
                                class="relative w-11 h-6 bg-gray-200 rounded-full
                                    peer peer-checked:after:translate-x-full
                                    after:content-[''] after:absolute after:top-0.5
                                    after:left-0.5 after:bg-white after:rounded-full
                                    after:h-5 after:w-5 after:transition-all
                                    peer-checked:bg-blue-600">
                            </div>
                            <span class="ml-3 text-sm text-gray-700">
                                Grant admin privileges
                            </span>
                        </label>
                    </div>
                <div class="flex justify-end pt-2">
                    <button type="submit"
                        class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition shadow-sm">
                        <i class="fas fa-save mr-2"></i>Update Staff
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.staff>
