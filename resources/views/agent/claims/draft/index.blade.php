<x-layouts.app>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">
                    My Drafts
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Pick up where you left off on a claim you haven't submitted yet.
                </p>
            </div>
        </div>
    </div>

    <!-- Drafts Table / List -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Toolbar -->
        <div
            class="px-5 py-4 border-b border-gray-200 bg-gray-50 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">
                    Unsubmitted Claim Drafts
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    These claims are saved but haven't been sent for review yet
                </p>
            </div>

            <div class="flex items-center gap-3">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" placeholder="Search by policy number..."
                        class="pl-8 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-gray-300 w-64 bg-white" />
                </div>
                <button
                    class="bg-white border border-gray-300 hover:bg-gray-50 px-3 py-2 rounded-xl text-sm font-medium text-gray-700 transition flex items-center gap-2">
                    <i class="fas fa-filter text-xs"></i>
                    Filter
                </button>
            </div>
        </div>

        <!-- Table (Responsive) -->
        <div class="overflow-x-auto custom-scroll">
            <table class="min-w-225 md:min-w-full w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Product</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Policy</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Claim Type</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Last Saved</th>
                        <th class="px-4 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($drafts as $draft)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $draft->policy->product_name }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $draft->policy->policy_number }}</div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-600 capitalize">{{ $draft->claim_type }}</td>
                            <td class="px-4 py-4 text-sm text-gray-600">{{ $draft->updated_at->diffForHumans() }}</td>
                            <td class="px-4 py-4 text-right relative" x-data="{ open: false }"
                                style="overflow: visible;">
                                <button @click="open = !open"
                                    class="px-3 py-2 border border-gray-300 rounded-xl text-sm text-gray-700 hover:bg-gray-50 inline-flex items-center">
                                    Details
                                    <i class="fas fa-chevron-down text-xs ml-1"></i>
                                </button>
                                <div x-show="open" @click.outside="open = false" x-transition
                                    x-anchor.bottom-end="$el.previousElementSibling"
                                    class="fixed w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-9999">
                                    <a href="{{ route('claims.draft.continue', $draft->id) }}"
                                        class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                        <i class="fas fa-pen text-xs text-blue-500"></i>
                                        Continue Draft
                                    </a>
                                    <form method="POST" action="{{ route('claims.draft.destroyById', $draft->id) }}"
                                        class="delete-draft-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                            Delete Draft
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <i class="fas fa-inbox text-4xl text-gray-300"></i>
                                    <p class="text-gray-600">You don't have any saved drafts yet.</p>
                                    <a href="{{ route('dashboard') }}"
                                        class="mt-2 inline-flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl shadow-sm transition">
                                        <i class="fas fa-plus-circle"></i> Start a Claim
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-300 flex justify-between items-center flex-wrap gap-3">
            <div class="text-sm text-gray-500">
                <i class="fas fa-file mr-1"></i>
                @if ($drafts->firstItem())
                    Showing {{ $drafts->lastItem() }} of {{ $drafts->total() }} drafts
                @else
                    No drafts found
                @endif
            </div>
            <div class="flex gap-2">
                {{ $drafts->links() }}
            </div>
        </div>
    </div>

    <!-- Helpful Tip / Support Card -->
    <div
        class="mt-6 bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-gray-800">
                <i class="fas fa-life-ring text-blue-500 mr-2"></i> Need help completing a claim?
            </p>
            <p class="text-sm text-gray-500 mt-1">
                Contact our claims support team for assistance with any draft or in-progress claim.
            </p>
        </div>
        <a href="tel:+233302666485"
            class="bg-blue-50 hover:bg-blue-100 text-blue-700 px-4 py-2 rounded-xl text-sm font-medium transition flex items-center gap-2 w-full sm:w-auto justify-center">
            <i class="fas fa-headset"></i> Contact Support
        </a>
    </div>
    {{-- Flash Messages --}}
    @if (session('success') || session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                @if (session('success'))
                    Toast.fire({
                        icon: 'success',
                        title: @json(session('success'))
                    });
                @endif

                @if (session('error'))
                    Toast.fire({
                        icon: 'error',
                        title: @json(session('error'))
                    });
                @endif
            });
        </script>
    @endif
    <script>
        document.querySelectorAll('.delete-draft-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Delete this draft?',
                    text: 'This will permanently remove your saved progress on this claim. This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete draft',
                    cancelButtonText: 'Go back',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
</x-layouts.app>
