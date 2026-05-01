<x-layouts.staff>
    <!-- Header with stats and filters -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-clipboard-list text-blue-500 text-2xl"></i>
                Incoming Claims
            </h2>
            <p class="text-gray-500 text-sm mt-1">
                All customer-initiated claims · ready for team review
            </p>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="searchInput" placeholder="Search client, policy..."
                    class="pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-blue-300 w-64 bg-white" />
            </div>
            <button id="filterResetBtn"
                class="bg-white border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 transition shadow-sm flex items-center gap-2">
                <i class="fas fa-refresh text-gray-500"></i> Reset
            </button>
            {{-- <button
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-sm transition flex items-center gap-2">
                <i class="fas fa-download"></i> Export
            </button> --}}
        </div>
    </div>

    <!-- Claim Amount Filter Tabs -->
    <div class="flex flex-wrap gap-2 mb-6 border-b border-gray-200 pb-2">
        <button data-filter="all"
            class="amount-filter-tab px-4 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600">
            All Claims
        </button>
        <button data-filter="low"
            class="amount-filter-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
            Same Day (≤ 30k)
        </button>
        <button data-filter="medium"
            class="amount-filter-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
            Medium (30k - 100k)
        </button>
        <button data-filter="high"
            class="amount-filter-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
            High (> 100k)
        </button>
    </div>

    <!-- Claims Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto custom-scroll">
            <table class="min-w-[1200px] md:min-w-full w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Client</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Policy Number</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Policy Period</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Product</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Claim Amount</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Assigned To</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Status</th>
                        <th class="px-4 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody id="claimsTableBody" class="divide-y divide-gray-200">
                    {{-- @forelse ( as )
                        
                    @empty
                        
                    @endforelse --}}
                    <!-- Row 1: John Davis - Low (25,000) -->
                    <tr class="hover:bg-gray-50 transition" data-amount="25000">
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-9 w-9 rounded-xl bg-gray-100 text-gray-700 flex items-center justify-center text-sm font-semibold">
                                    JD</div>
                                <span class="text-sm font-medium text-gray-900">John Davis</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 font-mono text-sm text-gray-700">
                            <div>P-1001-101-2026-000020</div>
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[0.7rem] font-medium bg-green-100 text-green-700">GLIMS</span>
                        </td>
                        <td class="px-4 py-4 text-xs text-gray-700">
                            <div>15-01-2024</div>
                            <span class="text-xs text-gray-400">to</span> 14-01-2025</span>
                        </td>
                        <td class="px-4 py-4 text-xs font-medium text-gray-900">Comprehensive</td>
                        <td class="px-4 py-4 text-sm font-medium text-gray-900">GHS 25,000.00</td>
                        <td class="px-4 py-4 text-sm text-gray-700">Jessica Arthur</td>
                        <td class="px-4 py-4">
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100">Pending</span>
                        </td>
                        <td class="px-4 py-4 text-right relative" x-data="{ open: false }" style="overflow: visible;">
                            <button @click="open = !open"
                                class="h-9 w-9 rounded-lg hover:bg-gray-100 text-gray-500 transition inline-flex items-center justify-center">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div x-show="open" @click.outside="open = false" x-transition
                                class="absolute right-4 top-12 z-50 w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-2">
                                <a href="{{ route('process-claim-motor') }}"
                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                    <i class="fas fa-check-circle text-xs text-emerald-500"></i> Process Claim
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <button onclick="assignClaim(1)"
                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                    <i class="fas fa-user-check text-xs text-emerald-500"></i> Assign Claim
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 2: Sarah Boateng - Medium (65,000) -->
                    <tr class="hover:bg-gray-50 transition" data-amount="65000">
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-9 w-9 rounded-xl bg-gray-100 text-gray-700 flex items-center justify-center text-sm font-semibold">
                                    SB</div>
                                <span class="text-sm font-medium text-gray-900">Sarah Boateng</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 font-mono text-sm text-gray-700">
                            <div>P-1001-102-2026-000095</div>
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[0.7rem] font-medium bg-green-100 text-green-700">GLIMS</span>
                        </td>
                        <td class="px-4 py-4 text-xs text-gray-700">
                            <div>10-09-2023</div>
                            <span class="text-xs text-gray-400">to</span> 09-09-2024</span>
                        </td>
                        <td class="px-4 py-4 text-xs font-medium text-gray-900">Happy Home</td>
                        <td class="px-4 py-4 text-sm font-medium text-gray-900">GHS 65,000.00</td>
                        <td class="px-4 py-4 text-sm text-gray-700">Afia Kyei</td>
                        <td class="px-4 py-4">
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">Assigned</span>
                        </td>
                        <td class="px-4 py-4 text-right relative" x-data="{ open: false }" style="overflow: visible;">
                            <button @click="open = !open"
                                class="h-9 w-9 rounded-lg hover:bg-gray-100 text-gray-500 transition inline-flex items-center justify-center">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div x-show="open" @click.outside="open = false" x-transition
                                class="absolute right-4 top-12 z-50 w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-2">
                                <a href="{{ route('process-claim-fire') }}"
                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                    <i class="fas fa-check-circle text-xs text-emerald-500"></i> Process Claim
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <button onclick="assignClaim(1)"
                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                    <i class="fas fa-user-check text-xs text-emerald-500"></i> Assign Claim
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 3: Olivia Addo - High (150,000) -->
                    <tr class="hover:bg-gray-50 transition" data-amount="150000">
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-9 w-9 rounded-xl bg-gray-100 text-gray-700 flex items-center justify-center text-sm font-semibold">
                                    OA</div>
                                <span class="text-sm font-medium text-gray-900">Olivia Addo</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 font-mono text-sm text-gray-700">
                            <div>P-1003-310-2026-000150</div>
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[0.7rem] font-medium bg-blue-100 text-blue-700">Genova</span>
                        </td>
                        <td class="px-4 py-4 text-xs text-gray-700">
                            <div>01-03-20243</div>
                            <span class="text-xs text-gray-400">to</span> 01-09-2024</span>
                        </td>
                        <td class="px-4 py-4 text-xs font-medium text-gray-900">Vanguard Safe Travel</td>
                        <td class="px-4 py-4 text-sm font-medium text-gray-900">GHS 150,000.00</td>
                        <td class="px-4 py-4 text-sm text-gray-400">Unassigned</td>
                        <td class="px-4 py-4">
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">New</span>
                        </td>
                        <td class="px-4 py-4 text-right relative" x-data="{ open: false }" style="overflow: visible;">
                            <button @click="open = !open"
                                class="h-9 w-9 rounded-lg hover:bg-gray-100 text-gray-500 transition inline-flex items-center justify-center">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div x-show="open" @click.outside="open = false" x-transition
                                class="absolute right-4 top-12 z-50 w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-2">
                                <a href="{{ route('process-claim-general-accident') }}"
                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                    <i class="fas fa-check-circle text-xs text-emerald-500"></i> Process Claim
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <button onclick="assignClaim(1)"
                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                    <i class="fas fa-user-check text-xs text-emerald-500"></i> Assign Claim
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer with pagination info -->
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200 flex justify-between items-center flex-wrap gap-3">
            <div class="text-sm text-gray-500">
                <i class="fas fa-clipboard-list mr-1"></i>
                Showing <span id="visibleCount">3</span> of <span id="totalCount">3</span> registered claims
            </div>
            <div class="flex gap-2">
                <button class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm bg-white hover:bg-gray-50"><i
                        class="fas fa-chevron-left"></i> Previous</button>
                <button class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-sm">1</button>
                <button class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm bg-white hover:bg-gray-50">Next <i
                        class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </div>

    <div
        class="mt-6 bg-blue-50/40 rounded-xl border border-blue-100 p-4 flex flex-wrap justify-between items-center gap-3">
        <div class="flex items-center gap-3 text-sm text-blue-800">
            <i class="fas fa-info-circle text-blue-500 text-lg"></i>
            <span><strong>Claims team overview:</strong> Use the amount filters to triage claims by value.</span>
        </div>
        <div class="flex gap-2">
            <span class="bg-white px-3 py-1 rounded-full text-xs shadow-sm"><i class="far fa-file-alt"></i> Modal
                preview</span>
            <span class="bg-white px-3 py-1 rounded-full text-xs shadow-sm"><i class="far fa-folder-open"></i> File
                list modal</span>
        </div>
    </div>

    <script>
        // Amount filter logic
        const filterTabs = document.querySelectorAll('.amount-filter-tab');
        const tableRows = document.querySelectorAll('#claimsTableBody tr');
        const totalCountSpan = document.getElementById('totalCount');
        const visibleCountSpan = document.getElementById('visibleCount');
        const searchInput = document.getElementById('searchInput');
        const resetBtn = document.getElementById('filterResetBtn');

        let currentAmountFilter = 'all';
        let currentSearchTerm = '';

        function filterTable() {
            let visible = 0;
            tableRows.forEach(row => {
                const amount = parseInt(row.getAttribute('data-amount'));
                let matchesAmount = true;
                if (currentAmountFilter === 'low') matchesAmount = amount <= 30000;
                else if (currentAmountFilter === 'medium') matchesAmount = amount > 30000 && amount <= 100000;
                else if (currentAmountFilter === 'high') matchesAmount = amount > 100000;

                // Search filter (client name or policy number)
                const clientName = row.querySelector('td:first-child .text-sm.font-medium')?.innerText
                    .toLowerCase() || '';
                const policyNumber = row.querySelector('td:nth-child(2)')?.innerText.toLowerCase() || '';
                const matchesSearch = currentSearchTerm === '' || clientName.includes(currentSearchTerm) ||
                    policyNumber.includes(currentSearchTerm);

                if (matchesAmount && matchesSearch) {
                    row.style.display = '';
                    visible++;
                } else {
                    row.style.display = 'none';
                }
            });
            visibleCountSpan.innerText = visible;
        }

        // Tab click handlers
        filterTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                currentAmountFilter = tab.getAttribute('data-filter');
                filterTabs.forEach(t => {
                    t.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
                    t.classList.add('text-gray-500');
                });
                tab.classList.remove('text-gray-500');
                tab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
                filterTable();
            });
        });

        // Search input
        searchInput.addEventListener('input', (e) => {
            currentSearchTerm = e.target.value.toLowerCase().trim();
            filterTable();
        });

        // Reset button: clear search and set filter to All
        resetBtn.addEventListener('click', () => {
            searchInput.value = '';
            currentSearchTerm = '';
            // Reset amount filter to All
            currentAmountFilter = 'all';
            filterTabs.forEach(t => {
                t.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
                t.classList.add('text-gray-500');
            });
            const allTab = document.querySelector('.amount-filter-tab[data-filter="all"]');
            if (allTab) {
                allTab.classList.remove('text-gray-500');
                allTab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
            }
            filterTable();
        });

        // Initialize counts
        totalCountSpan.innerText = tableRows.length;
        filterTable();
    </script>
</x-layouts.staff>
