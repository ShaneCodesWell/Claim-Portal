<div id="policyModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm hidden items-center justify-center p-4 z-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        {{-- Modal Header --}}
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
            <div class="flex items-center gap-3">
                {{-- Logo --}}
                <div class="flex">
                    <img src="{{ asset('images/Vanguard.png') }}" alt="Logo" class="h-8">
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Policy Details</h3>
                    <span class="text-sm text-gray-500" id="modal-policy-number"></span>
                </div>
            </div>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg p-2 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        {{-- Modal Body --}}
        <div class="p-6">
            {{-- Policy Information --}}
            <div class="space-y-4">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Policy Information</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Business Class</p>
                        <p class="text-sm font-semibold text-gray-900" id="modal-business-class"></p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Product</p>
                        <p class="text-sm font-semibold text-gray-900" id="modal-product"></p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Vehicle/Asset</p>
                        <p class="text-sm font-semibold text-gray-900" id="modal-vehicle"></p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Status</p>
                        <span class="text-sm font-bold" id="modal-status"></span>
                    </div>
                </div>
            </div>

            {{-- Coverage Period --}}
            <div class="space-y-4 mt-6">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Coverage Period</h4>
                <div class="grid grid-cols-3 gap-4 bg-gray-50 p-4 rounded-lg">
                    <div>
                        <p class="text-xs text-gray-600 mb-1">Start Date</p>
                        <p class="text-sm font-semibold text-gray-900" id="modal-start-date"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 mb-1">End Date</p>
                        <p class="text-sm font-semibold text-gray-900" id="modal-end-date"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 mb-1">Renewal Date</p>
                        <p class="text-sm font-semibold text-gray-900" id="modal-renewal-date"></p>
                    </div>
                </div>
            </div>

            {{-- Customer Information --}}
            <div class="space-y-4 mt-6">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Customer Information</h4>
                <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg">
                    <div>
                        <p class="text-xs text-gray-600 mb-1">Name</p>
                        <p class="text-sm font-semibold text-gray-900" id="modal-customer-name"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 mb-1">Customer Code</p>
                        <p class="text-sm font-semibold text-gray-900" id="modal-customer-code"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 mb-1">Phone</p>
                        <p class="text-sm font-semibold text-gray-900" id="modal-customer-phone"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 mb-1">Email</p>
                        <p class="text-sm font-semibold text-gray-900" id="modal-customer-email"></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div class="px-6 py-4 bg-gray-50 rounded-b-xl border-t border-gray-100 flex justify-end gap-3 sticky bottom-0">
            <button onclick="closeModal()" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                Close
            </button>
            <button onclick="processClaimFromModal()" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2 shadow-sm hover:shadow">
                <i class="fas fa-file-invoice"></i>
                File Claim
            </button>
        </div>
    </div>
</div>