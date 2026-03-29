<!-- Navbar -->
<nav class="bg-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-1">
        <div class="flex items-center justify-between">

            <!-- LEFT -->
            <div class="flex items-center space-x-4">
                <div class="flex items-center justify-center">
                    <!-- Replace with relative path if needed -->
                    <img src="/images/Vanguard.png" alt="Logo" class="w-40 h-12">
                </div>

                <div class="h-10 w-px bg-gray-300"></div>

                <div>
                    <h1 class="text-xl font-bold text-gray-900">Insurance Claims</h1>
                    <p class="text-xs text-gray-500">Claim Portal</p>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="flex items-center space-x-4">

                <!-- Static User -->
                <div class="relative">
                    <button onclick="toggleUserDropdown()" id="user-dropdown-button"
                        class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors">

                        <!-- Avatar -->
                        <div class="w-9 h-9 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                            MA
                        </div>

                        <!-- User Info -->
                        <div class="text-left hidden md:block">
                            <p class="text-sm font-semibold text-gray-900">Moses Adonoo</p>
                            <p class="text-xs text-gray-500">+233 24 000 0000</p>
                        </div>

                        <!-- Arrow -->
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown -->
                    <div id="user-dropdown"
                        class="hidden absolute right-0 mt-2 w-56 rounded-lg shadow-lg bg-white ring-1 ring-gray-300">

                        <div class="py-1">

                            <!-- Mobile Info -->
                            <div class="px-4 py-3 border-b border-gray-200 md:hidden">
                                <p class="text-sm font-semibold text-gray-900">Moses Adonoo</p>
                                <p class="text-xs text-gray-500">+233 24 000 0000</p>
                            </div>

                            <!-- Menu -->
                            <button class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-th-large mr-3 text-gray-400"></i>
                                Dashboard
                            </button>

                            <button class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-file-alt mr-3 text-gray-400"></i>
                                My Claims
                            </button>

                            <button class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-question-circle mr-3 text-gray-400"></i>
                                Help & Support
                            </button>

                            <div class="border-t border-gray-200"></div>

                            <button
                                class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt mr-3"></i>
                                Logout
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</nav>

<script>
    function toggleUserDropdown() {
        const dropdown = document.getElementById('user-dropdown');
        dropdown.classList.toggle('hidden');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', (event) => {
        const button = document.getElementById('user-dropdown-button');
        const dropdown = document.getElementById('user-dropdown');

        if (button && dropdown && !button.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });
</script>