<x-layouts.staff>
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: @json(session('success')),
                });
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "{{ session('error') }}"
            });
        </script>
    @endif
    <!-- Organization Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-building text-blue-500 text-2xl"></i>
            Organization Management
        </h2>
        <p class="text-gray-500 text-sm mt-1">
            Manage company profile, branches, departments, and team members.
        </p>
    </div>

    <!-- Tabs -->
    <div class="flex flex-wrap gap-1 border-b border-gray-200 mb-6">
        <button data-tab="profile"
            class="org-tab px-5 py-2.5 text-sm font-medium text-blue-600 border-b-2 border-blue-600">
            Company Profile
        </button>
        <button data-tab="branches" class="org-tab px-5 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-700">
            Branches
        </button>
        <button data-tab="departments" class="org-tab px-5 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-700">
            Departments
        </button>
        <button data-tab="team" class="org-tab px-5 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-700">
            Team & Roles
        </button>
    </div>

    <!-- Tab Content: Company Profile -->
    <div id="tab-profile" class="org-section">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50">
                <h3 class="font-semibold text-gray-800">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    Company Information
                </h3>
            </div>
            <form action="{{ route('settings.company.update') }}" id="companyProfileForm" class="space-y-4"
                method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-5">
                    <div class="flex flex-col sm:flex-row gap-6">
                        <!-- Logo Section (larger) -->
                        <div class="shrink-0 text-center">
                            <div
                                class="w-32 h-32 bg-gray-100 rounded-xl flex items-center justify-center mx-auto border border-gray-200">
                                <img src="{{ $company->logo_path ? Storage::url($company->logo_path) : asset('images/Vanguard.png') }}"
                                    alt="{{ $company->name }} Logo" class="w-36 h-12 object-contain" />
                                <input type="file" name="logo" class="hidden" id="logoInput">
                            </div>
                            <button type="button" class="mt-2 text-xs text-blue-600 hover:underline">Change logo</button>
                        </div>

                        <!-- Fields Grid (2 columns) -->
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                                <input type="text" name="name" value="{{ old('name', $company->name) }}"
                                    class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg bg-gray-50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tagline</label>
                                <input type="text" name="tagline" value="{{ old('tagline', $company->tagline) }}"
                                    class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg bg-gray-50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email (General)</label>
                                <input type="email" name="email" value="{{ old('email', $company->email) }}"
                                    class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg bg-gray-50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Claims Email</label>
                                <input type="email" name="claims_email"
                                    value="{{ old('claims_email', $company->claims_email) }}"
                                    class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg bg-gray-50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Primary Phone</label>
                                <input type="text" name="phone_primary"
                                    value="{{ old('phone_primary', $company->phone_primary) }}"
                                    class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg bg-gray-50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Secondary Phone</label>
                                <input type="text" name="phone_secondary"
                                    value="{{ old('phone_secondary', $company->phone_secondary) }}"
                                    class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg bg-gray-50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tertiary Phone</label>
                                <input type="text" name="phone_tertiary"
                                    value="{{ old('phone_tertiary', $company->phone_tertiary) }}"
                                    class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg bg-gray-50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                                <input type="url" name="website" value="{{ old('website', $company->website) }}"
                                    class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg bg-gray-50">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Postal Address</label>
                                <input type="text" name="postal_address"
                                    value="{{ old('postal_address', $company->postal_address) }}"
                                    class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg bg-gray-50">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Physical Address</label>
                                <input type="text" name="physical_address"
                                    value="{{ old('physical_address', $company->physical_address) }}"
                                    class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg bg-gray-50">
                            </div>
                        </div>
                    </div>
                    <div class="pt-4 flex justify-end gap-3">
                        <button
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Save
                            Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tab Content: Branches -->
    <div id="tab-branches" class="org-section hidden">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50 flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">
                    <i class="fas fa-store text-blue-500 mr-2"></i>
                    Branches / Locations
                </h3>
                <button
                    class="inline-flex items-center gap-2 bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm font-medium px-3 py-2 rounded-lg transition">
                    <i class="fas fa-plus-circle text-xs"></i> Add Branch
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                                Branch Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                                Branch Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                                Location / Address</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                                Phone</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($branches as $branch)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-sm  text-gray-700">{{ $branch->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $branch->code }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $branch->location }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $branch->phone }}</td>
                                <td class="px-4 py-4 text-right relative" x-data="{ open: false }"
                                    style="overflow: visible;">
                                    <button @click="open = !open"
                                        class="px-3 py-2 border border-gray-300 rounded-xl text-sm text-gray-700 hover:bg-gray-50">
                                        Actions <i class="fas fa-chevron-down text-xs ml-1"></i>
                                    </button>
                                    <div x-show="open" @click.outside="open = false" x-transition
                                        class="absolute right-4 top-12 z-50 w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-2">
                                        <a href="#"
                                            class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                            <i class="fas fa-edit text-xs text-blue-500"></i> Edit
                                        </a>
                                        <a href="#"
                                            class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                            <i class="fas fa-trash-alt text-xs text-red-500"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No branches found. Click "Add Branch" to get started.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab Content: Departments -->
    <div id="tab-departments" class="org-section hidden">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50 flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">
                    <i class="fas fa-sitemap text-blue-500 mr-2"></i>
                    Departments
                </h3>
                <button
                    class="inline-flex items-center gap-2 bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm font-medium px-3 py-2 rounded-lg transition">
                    <i class="fas fa-plus-circle text-xs"></i> Add Department
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                                Department Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                                Head of Department</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                                Employees</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($departments as $department)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm font-medium text-gray-700">{{ $department->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $department->head?->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $department->employees_count ?? 0 }}
                                </td>
                                <td class="px-4 py-4 text-right relative" x-data="{ open: false }"
                                    style="overflow: visible;">
                                    <button @click="open = !open"
                                        class="px-3 py-2 border border-gray-300 rounded-xl text-sm text-gray-700 hover:bg-gray-50">
                                        Actions <i class="fas fa-chevron-down text-xs ml-1"></i>
                                    </button>
                                    <div x-show="open" @click.outside="open = false" x-transition
                                        class="absolute right-4 top-12 z-50 w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-2">
                                        <a href="#"
                                            class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                            <i class="fas fa-edit text-xs text-blue-500"></i> Edit
                                        </a>
                                        <a href="#"
                                            class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                            <i class="fas fa-trash-alt text-xs text-red-500"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No departments found. Click "Add Department" to get started.
                                </td>
                            </tr>
                        @endforelse

                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm font-medium text-gray-700">Underwriting</td>
                            <td class="px-6 py-4 text-sm text-gray-700">Sarah Adjei</td>
                            <td class="px-6 py-4 text-sm text-gray-700">8</td>
                            <td class="px-4 py-4 text-right relative" x-data="{ open: false }"
                                style="overflow: visible;">
                                <button @click="open = !open"
                                    class="px-3 py-2 border border-gray-300 rounded-xl text-sm text-gray-700 hover:bg-gray-50">
                                    Actions <i class="fas fa-chevron-down text-xs ml-1"></i>
                                </button>
                                <div x-show="open" @click.outside="open = false" x-transition
                                    class="absolute right-4 top-12 z-50 w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-2">
                                    <a href="#"
                                        class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                        <i class="fas fa-edit text-xs text-blue-500"></i> Edit
                                    </a>
                                    <a href="#"
                                        class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                        <i class="fas fa-trash-alt text-xs text-red-500"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm font-medium text-gray-700">Finance</td>
                            <td class="px-6 py-4 text-sm text-gray-700">Kwame Asare</td>
                            <td class="px-6 py-4 text-sm text-gray-700">6</td>
                            <td class="px-4 py-4 text-right relative" x-data="{ open: false }"
                                style="overflow: visible;">
                                <button @click="open = !open"
                                    class="px-3 py-2 border border-gray-300 rounded-xl text-sm text-gray-700 hover:bg-gray-50">
                                    Actions <i class="fas fa-chevron-down text-xs ml-1"></i>
                                </button>
                                <div x-show="open" @click.outside="open = false" x-transition
                                    class="absolute right-4 top-12 z-50 w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-2">
                                    <a href="#"
                                        class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                        <i class="fas fa-edit text-xs text-blue-500"></i> Edit
                                    </a>
                                    <a href="#"
                                        class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                        <i class="fas fa-trash-alt text-xs text-red-500"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm font-medium text-gray-700">IT & Support</td>
                            <td class="px-6 py-4 text-sm text-gray-700">Nana Yaw</td>
                            <td class="px-6 py-4 text-sm text-gray-700">5</td>
                            <td class="px-4 py-4 text-right relative" x-data="{ open: false }"
                                style="overflow: visible;">
                                <button @click="open = !open"
                                    class="px-3 py-2 border border-gray-300 rounded-xl text-sm text-gray-700 hover:bg-gray-50">
                                    Actions <i class="fas fa-chevron-down text-xs ml-1"></i>
                                </button>
                                <div x-show="open" @click.outside="open = false" x-transition
                                    class="absolute right-4 top-12 z-50 w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-2">
                                    <a href="#"
                                        class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                        <i class="fas fa-edit text-xs text-blue-500"></i> Edit
                                    </a>
                                    <a href="#"
                                        class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                        <i class="fas fa-trash-alt text-xs text-red-500"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab Content: Team & Roles -->
    <div id="tab-team" class="org-section hidden">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50 flex justify-between items-center">
                <div>
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-users-cog text-blue-500"></i>
                        Team Members & Roles
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Manage access levels for claims operations</p>
                </div>
                <a href="{{ route('staff.create') }}"
                    class="inline-flex items-center gap-2 bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm font-medium px-3 py-2 rounded-lg transition">
                    <i class="fas fa-plus-circle text-xs"></i>
                    Add Staff Member
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                                Member</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                                Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                                Branch</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                                Department</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                                Contact</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($staffMembers as $staff)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-9 w-9 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-sm font-semibold">
                                            {{ strtoupper(substr($staff->name, 0, 1)) }}{{ strtoupper(substr(strrchr($staff->name, ' '), 1, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">{{ $staff->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $staff->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-700">{{ $staff->role ?? 'Claims Officer' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-700">{{ $staff->branch?->name ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="text-sm text-gray-700">{{ $staff->department?->name ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-700">{{ $staff->phone ?? 'N/A' }}</span>
                                </td>
                                <td class="px-4 py-4 text-right relative" x-data="{ open: false }"
                                    style="overflow: visible;">
                                    <button @click="open = !open"
                                        class="px-3 py-2 border border-gray-300 rounded-xl text-sm text-gray-700 hover:bg-gray-50">
                                        Actions <i class="fas fa-chevron-down text-xs ml-1"></i>
                                    </button>
                                    <div x-show="open" @click.outside="open = false" x-transition
                                        class="absolute right-4 top-12 z-50 w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-2">
                                        <!-- Edit -->
                                        <a href="{{ route('staff.edit', $staff) }}"
                                            class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                            <i class="fas fa-edit text-xs text-blue-500"></i>
                                            Edit
                                        </a>
                                        <!-- Delete -->
                                        <form method="POST" action="{{ route('staff.destroy', $staff) }}"
                                            class="delete-staff-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                                <i class="fas fa-trash-alt text-xs text-red-500"></i>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No staff members found. Click "Add Staff Member" to get started.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div
                class="bg-gray-50 px-6 py-3 border-t border-gray-300 flex justify-between items-center flex-wrap gap-3">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-users mr-1"></i>
                    @if ($staffMembers->firstItem())
                        Showing {{ $staffMembers->lastItem() }} of {{ $staffMembers->total() }} staff members
                    @else
                        No staff members found
                    @endif
                </div>
                <div class="flex gap-2">
                    {{ $staffMembers->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Tabs
            (function() {
                const tabs = document.querySelectorAll('.org-tab');
                const sections = {
                    profile: document.getElementById('tab-profile'),
                    branches: document.getElementById('tab-branches'),
                    departments: document.getElementById('tab-departments'),
                    team: document.getElementById('tab-team')
                };

                function activateTab(tabId) {
                    Object.values(sections).forEach(section => {
                        if (section) section.classList.add('hidden');
                    });

                    if (sections[tabId]) sections[tabId].classList.remove('hidden');

                    tabs.forEach(tab => {
                        const btnTabId = tab.getAttribute('data-tab');

                        if (btnTabId === tabId) {
                            tab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
                            tab.classList.remove('text-gray-500', 'hover:text-gray-700');
                        } else {
                            tab.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
                            tab.classList.add('text-gray-500', 'hover:text-gray-700');
                        }
                    });
                }

                tabs.forEach(tab => {
                    tab.addEventListener('click', () => {
                        const tabId = tab.getAttribute('data-tab');
                        if (tabId) activateTab(tabId);
                    });
                });

                const params = new URLSearchParams(window.location.search);
                const requestedTab = params.get('tab');

                if (requestedTab && sections[requestedTab.replace('tab-', '')]) {
                    activateTab(requestedTab.replace('tab-', ''));
                } else {
                    activateTab('profile');
                }
            })();

            // Logo trigger
            document.querySelectorAll('[data-logo-trigger]').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.getElementById('logoInput').click();
                });
            });

            // Delete SweetAlert
            document.querySelectorAll('.delete-staff-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const isAdmin = form.dataset.isAdmin === '1';
                    const isSelf = form.dataset.isSelf === '1';

                    if (isSelf) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Action not allowed',
                            text: 'You cannot delete your own account.'
                        });
                        return;
                    }

                    if (isAdmin) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Action not allowed',
                            text: 'Admin accounts cannot be deleted.'
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Delete staff member?',
                        text: 'This action cannot be undone.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#dc2626',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
</x-layouts.staff>
