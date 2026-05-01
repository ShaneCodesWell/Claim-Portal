<aside id="sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen bg-white border-r border-gray-200 shadow-xl transform -translate-x-full md:translate-x-0 sidebar-transition transition-transform duration-300 ease-in-out">
    <div class="h-full flex flex-col">
        <!-- Sidebar header with logo -->
        <div class="px-5 py-3 flex items-center gap-3 border-b border-gray-100">
            <div class="flex items-center justify-center">
                <img src="/images/Vanguard.png" alt="Logo" class="w-40 h-12">
            </div>
            {{-- <span class="text-xs bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-full ml-auto">{{ UserRole::labels()[Auth::user()->role] ?? 'Unknown Role' }}</span> --}}
            <span
                class="text-xs bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-full ml-auto">{{ Auth::user()->role }}</span>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-3 py-6 space-y-1.5">
            {{-- <a href="#"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-indigo-50 text-indigo-700 font-medium">
                <i class="fas fa-tachometer-alt w-5"></i>
                <span>Dashboard</span>
            </a> --}}
            <a href="{{ route('staff.claims.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                <i class="fas fa-clipboard-list w-5"></i>
                <span>Customer Claims</span>
                {{-- <span
                    class="ml-auto bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-full">{{ $stats['total_claims'] }}</span> --}}
            </a>
            <a href="{{ route('staff.claims.my-queue') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                <i class="fas fa-user-plus w-5"></i>
                <span>My Queue</span>
                <span class="ml-auto bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-full">{{ $stats['my_claims'] }}</span>
            </a>
            <a href="{{ route('claim-form') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                <i class="fas fa-file-alt w-5"></i>
                <span>Claim Forms</span>
            </a>
            <a href="{{ route('claim-documents') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                <i class="fas fa-folder-open w-5"></i>
                <span>Documents</span>
            </a>
            <a href="{{ route('customers') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                <i class="fas fa-users w-5"></i>
                <span>Customer List</span>
            </a>
            <div class="pt-4 mt-4 border-t border-gray-100">
                @if (Auth::user()->isAdmin())
                    <a href="{{ route('organization') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                        <i class="fas fa-building w-5"></i>
                        <span>Organization</span>
                    </a>
                    <a href="{{ route('settings') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                        <i class="fas fa-cog w-5"></i>
                        <span>Settings</span>
                    </a>
                @endif
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
    class="fixed inset-0 bg-black bg-opacity-40 z-30 hidden md:hidden transition-opacity duration-300"></div>
