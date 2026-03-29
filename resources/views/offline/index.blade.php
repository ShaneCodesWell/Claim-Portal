<x-layouts.offline>
    <style>
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-card {
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px -5px rgba(0, 0, 0, 0.12);
        }

        .policy-row {
            transition: all 0.2s ease;
        }

        .policy-row:hover {
            background-color: #f8fafc;
        }

        .search-input:focus {
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        @keyframes pulse-slow {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.6;
            }
        }

        .animate-pulse-slow {
            animation: pulse-slow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.2s ease;
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: white;
            border-radius: 1rem;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>

    {{-- Header Section --}}
    <div class="max-w-7xl mx-auto px-6 pb-12 space-y-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Claim Dashboard</h1>
                <p class="text-gray-500 text-sm mt-1">
                    Manage your insurance policies and submit new claim requests.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-3 px-4 py-2 bg-blue-50 border border-blue-100 rounded-full">
                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm text-blue-600">
                        <span class="font-bold text-sm" id="active-count">0</span>
                    </div>
                    <div class="text-sm font-medium text-blue-700">Active Policies</div>
                </div>
                <div class="flex items-center gap-3 px-4 py-2 bg-red-50 border border-red-100 rounded-full">
                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm text-red-600">
                        <span class="font-bold text-sm" id="expired-count">0</span>
                    </div>
                    <div class="text-sm font-medium text-red-700">Expired</div>
                </div>
            </div>
        </div>
        {{-- Search and Filter Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                {{-- Search Input --}}
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" id="search-policies"
                            placeholder="Search by policy number, product, or vehicle..."
                            class="search-input pl-11 pr-4 py-2.5 border-2 border-gray-200 rounded-lg w-full focus:outline-none focus:border-blue-500 transition-colors text-sm" />
                    </div>
                </div>

                {{-- Filters --}}
                <div class="flex flex-col sm:flex-row gap-3">
                    <select id="policy-type"
                        class="px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-blue-500 transition-colors font-medium text-gray-700 text-sm">
                        <option value="">All Types</option>
                        @if (isset($businessClasses) && count($businessClasses) > 0)
                            @foreach ($businessClasses as $classId => $className)
                                <option value="{{ $className }}">{{ $className }}</option>
                            @endforeach
                        @endif
                    </select>

                    <select id="policy-status"
                        class="px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-blue-500 transition-colors font-medium text-gray-700 text-sm">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="expired">Expired</option>
                    </select>

                    <button id="clear-filters"
                        class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all font-medium flex items-center gap-2 text-sm">
                        <i class="fas fa-times-circle"></i>
                        Clear
                    </button>
                </div>
            </div>
        </div>

        {{-- Policies Table Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-file-contract text-blue-600"></i>
                            Your Insurance Policies
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">Click on any policy to view details or file a claim</p>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Policy Details
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Policy Number
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Product
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Renewal Date
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        <!-- MOTOR POLICY -->
                        <tr class="policy-row">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-11 w-11 bg-blue-50 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-car text-blue-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-gray-900">Motor Policy</div>
                                        <div class="text-xs text-gray-500">GR 0896-25</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-sm">P-001-MOTOR</td>
                            <td class="px-6 py-4 text-sm">Comprehensive</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-50 text-green-600">
                                    Active
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">24-04-26</td>
                            <td class="px-6 py-4 text-right">
                                <button onclick="openModal('Motor Policy')" class="text-blue-600 font-medium">
                                    View
                                </button>
                            </td>
                        </tr>

                        <!-- FIRE POLICY -->
                        <tr class="policy-row">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-11 w-11 bg-red-50 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-fire text-red-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-gray-900">Fire Policy</div>
                                        <div class="text-xs text-gray-500">Warehouse Cover</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-sm">P-002-FIRE</td>
                            <td class="px-6 py-4 text-sm">Fire & Allied Perils</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-50 text-green-600">
                                    Active
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">12-06-26</td>
                            <td class="px-6 py-4 text-right">
                                <button onclick="openModal('Fire Policy')" class="text-blue-600 font-medium">
                                    View
                                </button>
                            </td>
                        </tr>

                        <!-- TRAVEL POLICY -->
                        <tr class="policy-row">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-11 w-11 bg-purple-50 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-plane text-purple-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-gray-900">Travel Policy</div>
                                        <div class="text-xs text-gray-500">UK Trip</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-sm">P-003-TRAVEL</td>
                            <td class="px-6 py-4 text-sm">Travel Insurance</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-50 text-red-600">
                                    Expired
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">01-01-25</td>
                            <td class="px-6 py-4 text-right">
                                <button onclick="openModal('Travel Policy')" class="text-blue-600 font-medium">
                                    View
                                </button>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            {{-- Pagination Container --}}
            <div id="pagination-container" class="hidden bg-gray-50">
                {{-- Pagination will be rendered here by JavaScript --}}
            </div>

        </div>
    </div>

    {{-- Policy Details Modal --}}
    <x-policy-details-modal />

    <script>
        function openModal(policyName) {
            const modal = document.getElementById('policyModal');

            // Example: set a title dynamically
            document.getElementById('modal-policy-number').textContent = policyName;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            const modal = document.getElementById('policyModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        // Close on outside click
        document.getElementById('policyModal')?.addEventListener('click', function(e) {
            if (e.target.id === 'policyModal') {
                closeModal();
            }
        });

        // ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</x-layouts.app>
