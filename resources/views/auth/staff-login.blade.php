<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <title>Staff Login | Vanguard Assurance</title>
    <!-- Tailwind CSS v3 + Font Awesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                    },
                },
            },
        }
    </script>
    <style>
        body {
            background-image: url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .login-card {
            transition: all 0.2s ease;
        }
    </style>
</head>

<body class="font-sans antialiased min-h-screen flex items-center justify-center p-4 relative">
    <!-- Dark overlay (same as role selection) -->
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-[2px]"></div>

    <!-- Top navigation bar (consistent with role selection) -->
    <nav class="absolute top-0 left-0 w-full p-6 z-20 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="bg-white/10 p-2 rounded-lg border border-white/20 backdrop-blur-sm">
                <img src="{{ asset('images/Vanguard.png') }}" alt="Vanguard Assurance Logo"
                    class="h-10 w-auto object-contain" />
                {{-- <i class="fas fa-shield-alt text-white text-xl"></i> --}}
            </div>
            {{-- <span class="text-white font-bold text-xl tracking-tight drop-shadow-md">Vanguard Assurance</span> --}}
        </div>
        <div class="flex gap-4">
            <a href="/" class="text-white/80 hover:text-white text-sm font-medium transition-colors">
                <i class="fas fa-arrow-left mr-1"></i>Back to Portal
            </a>
            <a href="#"
                class="text-white/80 hover:text-white text-sm font-medium transition-colors border-l border-white/30 pl-4 ml-2">
                Contact Support
            </a>
        </div>
    </nav>

    <div class="max-w-md w-full relative z-10 mt-16 mb-4">
        <!-- Staff Login Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden login-card">
            <!-- Card header (brand color) -->
            <div class="bg-brand-900 px-6 py-4 text-center">
                <div class="flex items-center justify-center gap-3 mb-1">
                    <div
                        class="bg-white/10 w-10 h-10 rounded-full flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <i class="fas fa-user-lock text-white text-sm"></i>
                    </div>
                    <h2 class="text-lg font-semibold text-white">Staff Login</h2>
                </div>
                <p class="text-blue-200 text-xs">Secure access for claims team & administrators</p>
            </div>

            {{-- Errors --}}
            @if ($errors->any())
                <div
                    class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Form content -->
            <div class="p-6">
                <div class="mb-4 text-center">
                    <p class="text-gray-600 text-sm">Enter your staff credentials to continue</p>
                </div>

                <form action="{{ route('staff.login.submit') }}" method="POST" class="space-y-5" id="staffLoginForm">
                    @csrf

                    <!-- Email / Username field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address /
                            Username</label>
                        <div class="relative">
                            <i
                                class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" name="email" id="email" required
                                placeholder="e.g., john@vanguard.com or staffID"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition bg-white">
                        </div>
                    </div>

                    <!-- Password field -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="password" name="password" id="password" required placeholder="••••••••"
                                class="w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition bg-white">
                            <button type="button" id="togglePassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember me + Forgot password row -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="remember"
                                class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                            <span class="text-sm text-gray-600">Remember me</span>
                        </label>
                        <a href="#" class="text-sm text-brand-600 hover:text-brand-700 font-medium">Forgot
                            password?</a>
                    </div>

                    <!-- Login button -->
                    <button type="submit" id="loginBtn"
                        class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl shadow-sm transition duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500">
                        <i class="fas fa-sign-in-alt text-sm"></i>
                        <span>Login to Staff Portal</span>
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="px-2 bg-white text-gray-400">Secure Access Only</span>
                    </div>
                </div>

                <!-- Notice about MFA (optional) -->
                <div class="text-center text-xs text-gray-500">
                    <i class="fas fa-shield-alt mr-1"></i> This portal uses multi‑factor authentication for all staff
                    accounts.
                </div>
            </div>

            <!-- Footer (matching role selection style) -->
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100 flex justify-between text-xs text-gray-400">
                <span><i class="fas fa-shield-alt mr-1"></i> 256-bit SSL</span>
                <div class="flex gap-3">
                    <a href="#" class="hover:text-gray-600">Privacy</a>
                    <a href="#" class="hover:text-gray-600">Terms</a>
                </div>
            </div>
        </div>

        <!-- Footer note -->
        <div class="text-center mt-6 text-gray-300 text-xs">
            <p>© 2026 NissiTechnologies. Authorized staff only.</p>
        </div>
    </div>

    <script>
        // Password visibility toggle
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        }

        // Form submission with loading state (demo only)
        const form = document.getElementById('staffLoginForm');
        const loginBtn = document.getElementById('loginBtn');

        if (form) {
            form.addEventListener('submit', function(e) {
                loginBtn.disabled = true;
                loginBtn.innerHTML = `
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Authenticating...</span>
                `;
                // If validation fails (page reloads), button will reset automatically.
            });
        }
    </script>
</body>

</html>
