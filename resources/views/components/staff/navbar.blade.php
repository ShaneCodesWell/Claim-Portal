<!-- TOP NAVIGATION BAR with hamburger + user dropdown -->
<header class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-20">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4 md:py-5">
            <div class="flex items-center gap-3">
                <!-- Mobile menu button -->
                <button id="mobileMenuBtn" class="md:hidden text-gray-600 hover:text-blue-600 focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div class="flex items-center gap-2">
                    <div class="bg-blue-100 rounded-lg p-1.5 shadow-sm md:hidden">
                        <i class="fas fa-shield-alt text-blue-600 text-sm"></i>
                    </div>
                    <h1 class="text-xl md:text-xl font-bold text-gray-800 tracking-tight">
                        E-Claim
                        <span class="font-medium text-blue-600">Dashboard</span>
                    </h1>
                </div>
            </div>

            <!-- User dropdown (click toggles) -->
            <div class="relative">
                <button id="userMenuBtn"
                    class="flex items-center gap-2 bg-white border border-gray-200 rounded-full pl-3 pr-2 py-1 shadow-sm hover:shadow-md transition focus:outline-none">
                    <i class="fas fa-user-circle text-gray-500 text-xl"></i>
                    <span class="text-sm font-medium text-gray-700 hidden sm:inline">{{ Auth::user()->name }}</span>
                    <i id="dropdownIcon"
                        class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-200"></i>
                </button>
                <!-- Dropdown menu -->
                <div id="userDropdown"
                    class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50 hidden transition-all duration-150 origin-top-right">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-sm font-semibold text-gray-800">
                            {{ Auth::user()->name }}
                        </p>
                        <p class="text-xs text-gray-500 truncate">
                            {{ Auth::user()->email }}
                        </p>
                    </div>
                    <a href="{{ route('my-claims') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-user-circle w-4 text-gray-400"></i> My
                        Claims
                    </a>
                    {{-- <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-bell w-4 text-gray-400"></i> Notifications
                        <span class="ml-auto bg-red-100 text-red-600 text-xs px-1.5 rounded-full">3</span>
                    </a> --}}
                    <a href="{{ route('settings') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-cog w-4 text-gray-400"></i> Settings
                    </a>
                    <hr class="my-1 border-gray-200" />
                    <form action="{{ route('logout') }}" method="POST" class="flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                        @csrf
                        <button type="submit" >
                            <i class="fas fa-sign-out-alt w-4"></i> Sign out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
