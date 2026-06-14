<div id="staffPolicyModal" style="display:none"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">

        {{-- Header --}}
        <div class="flex items-start justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <div class="mb-1">
                    <span id="staff-modal-status" class="text-xs font-semibold px-2.5 py-1 rounded-full"></span>
                </div>
                <h2 class="text-lg font-bold text-gray-900" id="staff-modal-policy-number">—</h2>
                <p class="text-sm text-gray-500 mt-0.5" id="staff-modal-business-class">—</p>
            </div>
            <button onclick="staffCloseModal()" class="text-gray-400 hover:text-gray-600 transition p-1 mt-1">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        {{-- Policy meta --}}
        <div class="px-6 py-4 grid grid-cols-2 md:grid-cols-3 gap-4 border-b border-gray-100 bg-gray-50/50">
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Product</p>
                <p class="text-sm font-semibold text-gray-800" id="staff-modal-product">—</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Start Date</p>
                <p class="text-sm font-semibold text-gray-800" id="staff-modal-start-date">—</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">End Date</p>
                <p class="text-sm font-semibold text-gray-800" id="staff-modal-end-date">—</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Renewal Date</p>
                <p class="text-sm font-semibold text-gray-800" id="staff-modal-renewal-date">—</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Customer</p>
                <p class="text-sm font-semibold text-gray-800" id="staff-modal-customer-name">—</p>
            </div>
        </div>

        {{-- Risks list --}}
        <div class="flex-1 overflow-y-auto px-6 py-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <i class="fas fa-car text-blue-500 text-xs"></i>
                    Risks / Vehicles
                    <span id="staff-modal-risk-count"
                        class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">0</span>
                </h3>
                <div class="relative">
                    <i class="fas fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" id="staff-modal-risk-search" placeholder="Search risks..."
                        oninput="staffFilterRisks(this.value)"
                        class="pl-7 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg w-44
                               focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
            </div>
            <div id="staff-modal-risks-list" class="space-y-2"></div>
            <p id="staff-modal-risk-empty" class="hidden text-sm text-gray-400 text-center py-6">
                No matching risks found.
            </p>
        </div>

        {{-- Footer --}}
        <div
            class="px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-2xl flex items-center justify-between gap-3">
            <p class="text-xs text-gray-400 flex items-center gap-1.5">
                <i class="fas fa-user-shield text-amber-500"></i>
                Acting on behalf of
                <span class="font-medium text-gray-600" id="staff-modal-footer-name">the customer</span>
            </p>
            <div class="flex items-center gap-2">
                <button onclick="staffCloseModal()"
                    class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Close
                </button>
                {{-- Hidden for fleet — each risk card has its own button in that case --}}
                <button id="staff-modal-claim-btn"
                    class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition flex items-center gap-2 shadow-sm">
                    <i class="fas fa-file-invoice"></i> Process Claim
                </button>
            </div>
        </div>
    </div>
</div>
