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
            <form action="{{ route('staff.store') }}" id="singleStaffForm" class="space-y-4" method="POST">
                @csrf
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="text" name="phone" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select name="role" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            @foreach ($roles as $role)
                                <option value="{{ $role }}">
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
                                <option value="{{ $department->id }}">
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
                                <option value="{{ $branch->id }}">
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
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
                <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Administrator Access
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_admin" value="1" class="sr-only peer">
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

    <script>
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
