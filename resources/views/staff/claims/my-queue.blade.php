<x-layouts.staff>
    <!-- Minimal Workspace Header -->
    <div class="mb-6">
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">
                        Welcome back, <span class="font-bold text-blue-500">{{ Auth::user()->name }}</span>
                    </p>
                    <h2 class="text-xl font-semibold text-gray-900">
                        Your Assigned Claims
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Review active claims, access supporting documents, and keep progress moving.
                    </p>
                </div>
                <button onclick="window.location.reload()"
                    class="bg-white border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-refresh text-gray-500"></i> Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Compact Neutral Stat Pills -->
    <div class="flex flex-wrap gap-3 mb-6">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 shadow-sm">
            <span class="h-2 w-2 rounded-full bg-indigo-400"></span>
            <span class="text-sm text-gray-600">Total</span>
            <span class="text-sm font-semibold text-gray-900">{{ $stats['total_claims'] }}</span>
        </div>

        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 shadow-sm">
            <span class="h-2 w-2 rounded-full bg-amber-400"></span>
            <span class="text-sm text-gray-600">Under Review</span>
            <span class="text-sm font-semibold text-gray-900">{{ $stats['under_review'] }}</span>
        </div>

        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 shadow-sm">
            <span class="h-2 w-2 rounded-full bg-orange-400"></span>
            <span class="text-sm text-gray-600">Awaiting Finalization</span>
            <span class="text-sm font-semibold text-gray-900">{{ $stats['awaiting_finalization'] ?? 0 }}</span>
        </div>

        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 shadow-sm">
            <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
            <span class="text-sm text-gray-600">Completed</span>
            <span class="text-sm font-semibold text-gray-900">{{ $stats['closed_claims'] }}</span>
        </div>
    </div>

    <!-- Premium Claims Desk -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Embedded Toolbar -->
        <div
            class="px-5 py-4 border-b border-gray-200 bg-gray-50 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">
                    Claims Assigned to You
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    Use actions to review claim forms, attached files, or update status
                </p>
            </div>

            <div class="flex items-center gap-3">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="search" id="queue-search" name="search" value="{{ request('search') }}"
                        placeholder="Search client, policy or claim no..." autocomplete="off"
                        class="pl-8 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-gray-300 w-64 bg-white" />
                </div>

                {{-- Filter: hidden until the team asks for it
                    <button class="...">Filter</button> --}}
            </div>
        </div>

        <!-- Table -->
        <div id="queue-results" class="transition-opacity">
            @include('staff.claims.partials.my-queue-results')
        </div>

    </div>

    <!-- Workspace Tip -->
    {{-- <div class="mt-6 bg-blue-50/40 rounded-xl border border-blue-100 shadow-xs p-4">
        <p class="text-sm font-medium text-blue-800"><i class="fas fa-info-circle text-blue-500 text-lg"></i>
            Workspace tip
        </p>
        <p class="text-sm text-blue-800 mt-1">
            Keep pending claims at the top of your workflow. Resolve them first before moving to in-progress items.
        </p>
    </div> --}}
    <script>
        (function() {
            const input = document.getElementById('queue-search');
            const results = document.getElementById('queue-results');
            const baseUrl = @json(url()->current());

            let timer = null;
            let controller = null;

            async function load(url) {
                controller?.abort(); // drop any stale in-flight request
                const current = controller = new AbortController();
                results.classList.add('opacity-50');

                try {
                    const res = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html'
                        },
                        signal: current.signal,
                    });

                    if (res.status === 401 || res.status === 419) { // session expired
                        window.location.reload();
                        return;
                    }
                    if (!res.ok) throw new Error('Request failed: ' + res.status);

                    results.innerHTML = await res.text();
                    history.replaceState(null, '', url); // keep the URL shareable / refresh-safe
                } catch (e) {
                    if (e.name !== 'AbortError') {
                        results.innerHTML = '<div class="px-6 py-12 text-center text-sm text-red-500">' +
                            'Could not load claims. Please try again.</div>';
                    }
                } finally {
                    if (controller === current) results.classList.remove('opacity-50');
                }
            }

            input.addEventListener('input', () => {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    const term = input.value.trim();
                    const url = new URL(baseUrl);
                    if (term) url.searchParams.set('search',
                    term); // new search always starts on page 1
                    load(url.toString());
                }, 300);
            });

            // Pagination links load via AJAX too, keeping the search term
            results.addEventListener('click', (e) => {
                const link = e.target.closest('nav a[href]');
                if (!link) return;
                e.preventDefault();
                load(link.href);
            });
        })();
    </script>
</x-layouts.staff>
