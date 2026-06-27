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
                        <input type="text" name="phone" value="{{ $staff->phone }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
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
                                <option value="{{ $department->id }}"
                                    {{ $staff->department_id == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div x-data="{
                        open: false,
                        selected: {{ isset($staff) ? $staff->branches->pluck('id') : '[]' }},
                        branches: {{ $branches->map(fn($b) => ['id' => $b->id, 'name' => $b->name]) }},
                        toggle(id) {
                            this.selected.includes(id) ?
                                this.selected = this.selected.filter(i => i !== id) :
                                this.selected.push(id);
                        },
                        isSelected(id) { return this.selected.includes(id); },
                        get label() {
                            if (this.selected.length === 0) return 'Select branch(es)';
                            if (this.selected.length === 1) {
                                return this.branches.find(b => b.id === this.selected[0])?.name ?? '';
                            }
                            return this.selected.length + ' branches selected';
                        }
                    }" class="relative">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Branch(es)</label>

                        {{-- Hidden inputs for form submission --}}
                        <template x-for="id in selected" :key="id">
                            <input type="hidden" name="branch_ids[]" :value="id">
                        </template>

                        {{-- Trigger button --}}
                        <button type="button" @click="open = !open" @click.outside="open = false"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-left text-sm bg-white flex items-center justify-between focus:outline-none focus:ring-1 focus:ring-blue-300"
                            :class="selected.length ? 'text-gray-900' : 'text-gray-400'">
                            <span x-text="label"></span>
                            <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform"
                                :class="open ? 'rotate-180' : ''"></i>
                        </button>

                        {{-- Dropdown --}}
                        <div x-show="open" x-transition
                            class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-56 overflow-y-auto">
                            <template x-for="branch in branches" :key="branch.id">
                                <button type="button" @click="toggle(branch.id)"
                                    class="w-full flex items-center justify-between px-3 py-2 text-sm hover:bg-gray-50 transition"
                                    :class="isSelected(branch.id) ? 'text-blue-700 bg-blue-50' : 'text-gray-700'">
                                    <span x-text="branch.name"></span>
                                    <i class="fas fa-check text-blue-600 text-xs" x-show="isSelected(branch.id)"></i>
                                </button>
                            </template>
                        </div>

                        {{-- Selected badges --}}
                        <div class="flex flex-wrap gap-2 mt-2" x-show="selected.length > 0">
                            <template x-for="id in selected" :key="id">
                                <span
                                    class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">
                                    <span x-text="branches.find(b => b.id === id)?.name"></span>
                                    <button type="button" @click="toggle(id)" class="hover:text-blue-900">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </span>
                            </template>
                        </div>
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
                        <input type="checkbox" name="is_admin" value="1" class="sr-only peer"
                            {{ $staff->is_admin ? 'checked' : '' }}>
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
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Claim Committee Member
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_committee_member" value="1" class="sr-only peer"
                            {{ $staff->is_committee_member ? 'checked' : '' }}>
                        <div
                            class="relative w-11 h-6 bg-gray-200 rounded-full
                                    peer peer-checked:after:translate-x-full
                                    after:content-[''] after:absolute after:top-0.5
                                    after:left-0.5 after:bg-white after:rounded-full
                                    after:h-5 after:w-5 after:transition-all
                                    peer-checked:bg-blue-600">
                        </div>
                        <span class="ml-3 text-sm text-gray-700">
                            Grant claim committee privileges
                        </span>
                    </label>
                </div>
                <div class="flex justify-end pt-2 gap-3">
                    <a href="{{ route('organization') }}?tab=tab-team"
                        class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white hover:bg-gray-50 transition">
                        <i class="fas fa-arrow-left text-xs"></i>
                        Back
                    </a>
                    <button type="submit"
                        class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition shadow-sm">
                        <i class="fas fa-save mr-2"></i>Update Staff
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.staff>
