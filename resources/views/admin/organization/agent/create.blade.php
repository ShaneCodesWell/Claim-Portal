<x-layouts.staff>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-users text-blue-500 text-2xl"></i>
                Intermediary Management
            </h2>
            <p class="text-gray-500 text-sm mt-1">
                Add new agents individually or upload multiple via Excel file.
            </p>
        </div>

        {{-- Right-side actions --}}
        <div class="sm:ml-auto flex items-center gap-2">
            <a href="{{ route('organization') }}?tab=tab-agents"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50">
                <i class="fas fa-arrow-left"></i>
                Back
            </a>
        </div>
    </div>

    <!-- Two column layout: left form, right bulk upload -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Single Staff Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-user-plus text-blue-500"></i> Add an Intermediary
            </h3>
            <form action="{{ route('agents.store') }}" id="singleStaffForm" class="space-y-4" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Fullname --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                        <input type="text" name="name" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                        <input type="email" name="email"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    {{-- Phone Number --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="text" name="phone"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    {{-- Gender --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                        <select name="gender" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>

                        </select>
                    </div>
                    {{-- Date of Birth --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Date of Birth
                        </label>

                        <input type="date" name="date_of_birth"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    {{-- League --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">League</label>
                        <select name="league" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="">Select League</option>
                            <option value="champions">Champions League</option>
                            <option value="regional">Regional</option>
                            <option value="auto_house">Auto House</option>

                        </select>
                    </div>
                    {{-- GLIMS Agent Code --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            GLIMS Agent Code
                        </label>
                        <input type="text" name="glims_agent_code" placeholder="e.g. 30004"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    {{-- Genova Agent Code --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Genova Agent Code
                        </label>
                        <input type="text" name="genova_agent_code" placeholder="e.g. AG-0507"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    {{-- Department --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Category
                        </label>

                        <select name="user_category" id="categorySelect"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="">Select Category</option>

                            <option value="Service Providers">Service Providers</option>
                            <option value="Reinsurance">Reinsurance</option>
                            <option value="Agent">Agent</option>
                            <option value="Broker">Broker</option>
                            <option value="Bancassurance">Bancassurance</option>
                            <option value="Protocol">Protocol</option>
                        </select>
                    </div>
                    {{-- Sub Category --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Sub Category
                        </label>

                        <select name="sub_user_category" id="subCategorySelect"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="">Select Sub Category</option>
                        </select>
                    </div>
                    {{-- Branch --}}
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
                </div>
                <div class="flex justify-end pt-2 gap-3">
                    <a href="{{ route('organization') }}?tab=tab-agents"
                        class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white hover:bg-gray-50 transition">
                        <i class="fas fa-arrow-left text-xs"></i>
                        Back
                    </a>
                    <button type="submit"
                        class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition shadow-sm">
                        <i class="fas fa-save mr-2"></i>Add Agent
                    </button>
                </div>
            </form>
        </div>

        <!-- Bulk Upload via Excel -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-file-excel text-green-600"></i> Bulk Upload (Excel)
            </h3>
            <form action="{{ route('agents.bulk-upload') }}" method="POST" enctype="multipart/form-data"
                id="bulkUploadForm">
                @csrf
                <div
                    class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-300 transition">
                    <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-3"></i>
                    <p class="text-sm text-gray-600 mb-2">Upload an Excel (.xlsx, .xls) or CSV file</p>
                    <p class="text-xs text-gray-400 mb-4">Columns: name, email, phone, gender, date_of_birth, league,
                        glims_agent_code, genova_agent_code, branch, user_category, sub_user_category</p>
                    <label
                        class="cursor-pointer bg-blue-50 hover:bg-blue-100 text-blue-700 px-4 py-2 rounded-lg text-sm font-medium inline-flex items-center gap-2 transition">
                        <i class="fas fa-upload"></i> Choose File
                        <input type="file" name="file" id="bulkFile" accept=".xlsx,.xls,.csv" class="hidden"
                            required>
                    </label>
                    <p id="fileNameDisplay" class="text-xs text-gray-500 mt-2 hidden"></p>
                    <button type="submit" id="uploadBulkBtn"
                        class="mt-4 bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition disabled:opacity-50"
                        disabled>
                        <i class="fas fa-upload mr-2"></i>Upload & Process
                    </button>
                </div>
            </form>
            <div class="mt-4 text-xs text-gray-500">
                <i class="fas fa-download mr-1"></i>
                <a href="{{ route('agents.template') }}" class="text-blue-600 hover:underline">Download sample Excel
                    template</a>
            </div>
        </div>
    </div>

    <script>
        // Bulk upload file selection preview
        const fileInput = document.getElementById('bulkFile');
        const fileNameDisplay = document.getElementById('fileNameDisplay');
        const uploadBtn = document.getElementById('uploadBulkBtn');
        const bulkUploadForm = document.getElementById('bulkUploadForm');

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

        // Show a spinner and lock the button once the upload is submitted
        bulkUploadForm.addEventListener('submit', function(e) {
            if (!fileInput.files.length) {
                e.preventDefault();
                return;
            }

            uploadBtn.disabled = true;
            uploadBtn.innerHTML = `
            <svg class="animate-spin h-4 w-4 text-white inline-block mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            Processing... this may take a moment
        `;
            // Form submits normally after this — full page navigation happens on redirect
        });

        // Staff search (simple filter)
        const staffSearch = document.getElementById('staffSearch');
        if (staffSearch) {
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
                const countEl = document.getElementById('showingCount');
                if (countEl) countEl.innerText = visible;
            });
        }
    </script>
    <script src="{{ asset('js/agent-category.js') }}"></script>
</x-layouts.staff>
