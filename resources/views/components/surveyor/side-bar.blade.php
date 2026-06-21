<aside id="sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen bg-white border-r border-gray-200 shadow-xl transform -translate-x-full md:translate-x-0 sidebar-transition transition-transform duration-300 ease-in-out">
    <div class="h-full flex flex-col">
        <!-- Sidebar header with logo -->
        <div class="px-5 py-3 flex items-center gap-3 border-b border-gray-100">
            <div class="flex items-center justify-center">
                <img src="/images/Vanguard.png" alt="Logo" class="w-40 h-12">
            </div>
            <span class="text-xs bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-full ml-auto">Surveyor</span>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-3 py-6 space-y-1.5">
            <a href="{{ route('surveyor.claims.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
            {{ request()->routeIs('surveyor.claims.index')
                ? 'bg-cyan-50 text-cyan-700 font-medium'
                : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-clipboard-list w-5"></i>
                <span>All Survey Claims</span>
                @if ($stats['all_survey_count'] > 0)
                    <span class="ml-auto bg-cyan-100 text-cyan-700 text-xs px-2 py-0.5 rounded-full">
                        {{ $stats['all_survey_count'] }}
                    </span>
                @endif
            </a>

            <a href="{{ route('surveyor.claims.my-queue') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
            {{ request()->routeIs('surveyor.claims.my-queue')
                ? 'bg-cyan-50 text-cyan-700 font-medium'
                : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-user-check w-5"></i>
                <span>My Assigned</span>
                @if ($stats['my_queue_count'] > 0)
                    <span class="ml-auto bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-full">
                        {{ $stats['my_queue_count'] }}
                    </span>
                @endif
            </a>

            <div class="pt-4 mt-4 border-t border-gray-100">
                <form action="{{ route('logout') }}" method="POST"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-red-500 hover:bg-red-50 transition">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 w-full text-left">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </nav>
        <div class="p-4 text-center text-xs text-gray-400 border-t border-gray-100">
            <i class="far fa-clock"></i> Last sync: just now
        </div>
    </div>
</aside>
<!-- OVERLAY for mobile sidebar (hidden by default) -->
<div id="sidebarOverlay"
    class="fixed inset-0 bg-black/40 z-30 hidden md:hidden transition-opacity duration-300 opacity-0"></div>
