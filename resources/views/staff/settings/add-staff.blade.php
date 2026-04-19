<x-layouts.staff>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-users text-blue-500 text-2xl"></i>
                Staff Management
            </h2>
            <p class="text-gray-500 text-sm mt-1">
                Add new staff members individually or upload multiple via Excel file.
            </p>
        </div>
    </div>

    <!-- Two column layout: left form, right bulk upload -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Single Staff Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-user-plus text-blue-500"></i> Add Single Staff
            </h3>
            <form action="{{ route('staff.store') }}" id="singleStaffForm" class="space-y-4">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                        <input type="text" name="name" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                        <input type="email" name="email" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select name="role" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="Claims Adjuster">Claims Adjuster</option>
                            <option value="Reviewer">Reviewer</option>
                            <option value="Admin">Admin</option>
                            <option value="Viewer">Viewer</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                        <input type="text" name="department" placeholder="e.g., Claims, Surveyer"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                        <input type="password" name="password" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password *</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    </div>
                </div>
                <div class="flex justify-end pt-2">
                    <button type="submit"
                        class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition shadow-sm">
                        <i class="fas fa-save mr-2"></i>Add Staff
                    </button>
                </div>
            </form>
        </div>

        <!-- Bulk Upload via Excel -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-file-excel text-green-600"></i> Bulk Upload (Excel)
            </h3>
            <div
                class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-300 transition">
                <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-3"></i>
                <p class="text-sm text-gray-600 mb-2">Upload an Excel (.xlsx, .xls) or CSV file</p>
                <p class="text-xs text-gray-400 mb-4">File must contain columns: name, email, role, department,
                    password</p>
                <label
                    class="cursor-pointer bg-blue-50 hover:bg-blue-100 text-blue-700 px-4 py-2 rounded-lg text-sm font-medium inline-flex items-center gap-2 transition">
                    <i class="fas fa-upload"></i> Choose File
                    <input type="file" id="bulkFile" accept=".xlsx, .xls, .csv" class="hidden">
                </label>
                <p id="fileNameDisplay" class="text-xs text-gray-500 mt-2 hidden"></p>
                <button id="uploadBulkBtn"
                    class="mt-4 bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition disabled:opacity-50"
                    disabled>
                    <i class="fas fa-upload mr-2"></i>Upload & Process
                </button>
            </div>
            <div class="mt-4 text-xs text-gray-500">
                <i class="fas fa-download mr-1"></i>
                <a href="#" class="text-blue-600 hover:underline">Download sample Excel template</a>
            </div>
        </div>
    </div>

    <!-- Existing Staff List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50 flex justify-between items-center">
            <h3 class="font-semibold text-gray-800"><i class="fas fa-list-ul text-blue-500 mr-2"></i>Current Staff
                Members</h3>
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="staffSearch" placeholder="Search staff..."
                    class="pl-9 pr-4 py-1.5 border border-gray-300 rounded-lg text-sm w-64">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Department
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody id="staffTableBody" class="divide-y divide-gray-100">
                    @forelse ($staffMembers as $staff)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div
                                        class="h-9 w-9 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-sm font-semibold">
                                        {{ strtoupper(substr($staff->name, 0, 1)) }}{{ strtoupper(substr(strrchr($staff->name, ' '), 1, 1)) }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="font-medium text-gray-800">{{ $staff->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $staff->email }}</td>
                            <td class="px-6 py-4 text-sm">{{ $staff->role }}</td>
                            <td class="px-6 py-4 text-sm">{{ $staff->department }}</td>
                            <td class="px-6 py-4"><span
                                    class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Active</span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2"><button class="text-blue-600"><i
                                        class="fas fa-edit"></i></button><button class="text-red-500"><i
                                        class="fas fa-trash-alt"></i></button></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                No staff members found. Please add staff using the form above.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-300 flex justify-between items-center flex-wrap gap-3">
            <div class="text-sm text-gray-500">
                @if ($staffMembers->firstItem())
                    Showing {{ $staffMembers->lastItem() }} of {{ $staffMembers->total() }}
                    staff members
                @else
                    No staff members found
                @endif
            </div>
            <div class="flex gap-2">
                {{ $staffMembers->links() }}
            </div>
        </div>
    </div>

    <script>
        // Handle single staff form submission (demo)
        document.getElementById('singleStaffForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i>Adding Staff...`;

            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch('{{ route('staff.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'Staff Added!',
                        text: `${result.staff.name} has been added successfully.`,
                        confirmButtonColor: '#4f46e5',
                        confirmButtonText: 'Great!',
                    });

                    this.reset();

                    // Optionally append new staff to the table
                    appendStaffToTable(result.staff);

                } else {
                    const errorMessage = result.errors ?
                        Object.values(result.errors).flat().join('\n') :
                        result.message ?? 'Something went wrong.';

                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to Add Staff',
                        text: errorMessage,
                        confirmButtonColor: '#4f46e5',
                    });
                }

            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Unexpected Error',
                    text: 'An error occurred. Please try again.',
                    confirmButtonColor: '#4f46e5',
                });
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = `<i class="fas fa-save mr-2"></i>Add Staff`;
            }
        });

        function appendStaffToTable(staff) {
            const tableBody = document.getElementById('staffTableBody');
            const initials = staff.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();

            const colors = ['bg-blue-100 text-blue-700', 'bg-emerald-100 text-emerald-700',
                'bg-amber-100 text-amber-700', 'bg-rose-100 text-rose-700'
            ];
            const colorClass = colors[Math.floor(Math.random() * colors.length)];

            const row = document.createElement('tr');
            row.className = 'divide-y divide-gray-100';
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="h-8 w-8 rounded-full ${colorClass} flex items-center justify-center font-semibold text-sm">
                            ${initials}
                        </div>
                        <div class="ml-3">
                            <p class="font-medium text-gray-800">${staff.name}</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">${staff.email}</td>
                <td class="px-6 py-4 text-sm">${staff.role}</td>
                <td class="px-6 py-4 text-sm">${staff.department ?? 'N/A'}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Active</span>
                </td>
                <td class="px-6 py-4 text-right space-x-2">
                    <button class="text-blue-600"><i class="fas fa-edit"></i></button>
                    <button class="text-red-500"><i class="fas fa-trash-alt"></i></button>
                </td>
            `;

            tableBody.appendChild(row);

            // Update the showing count
            const totalCount = document.getElementById('totalCount');
            const showingCount = document.getElementById('showingCount');
            const newCount = parseInt(totalCount.textContent) + 1;
            totalCount.textContent = newCount;
            showingCount.textContent = newCount;
        }

        // Bulk upload file selection preview
        const fileInput = document.getElementById('bulkFile');
        const fileNameDisplay = document.getElementById('fileNameDisplay');
        const uploadBtn = document.getElementById('uploadBulkBtn');

        fileInput.addEventListener('change', function(e) {
            if (this.files.length > 0) {
                fileNameDisplay.textContent = `Selected: ${this.files[0].name}`;
                fileNameDisplay.classList.remove('hidden');
                uploadBtn.disabled = false;
            } else {
                fileNameDisplay.classList.add('hidden');
                uploadBtn.disabled = true;
            }
        });

        uploadBtn.addEventListener('click', function() {
            if (!fileInput.files.length) return;
            alert(`Uploading ${fileInput.files[0].name}... (demo - process on server)`);
            // In real implementation, use FormData and AJAX
        });

        // Sample Excel download (just a placeholder)
        document.querySelector('a[href="#"]').addEventListener('click', function(e) {
            e.preventDefault();
            alert('Sample Excel template download would start here.');
        });

        // Staff search (simple filter)
        const staffSearch = document.getElementById('staffSearch');
        const tableRows = document.querySelectorAll('#staffTableBody tr');
        staffSearch.addEventListener('input', function() {
            const term = this.value.toLowerCase();
            let visible = 0;
            tableRows.forEach(row => {
                const text = row.innerText.toLowerCase();
                if (text.includes(term)) {
                    row.style.display = '';
                    visible++;
                } else {
                    row.style.display = 'none';
                }
            });
            document.getElementById('showingCount').innerText = visible;
        });
    </script>
</x-layouts.staff>
