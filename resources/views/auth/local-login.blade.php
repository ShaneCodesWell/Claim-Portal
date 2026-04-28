<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <title>Local Login | Vanguard Assurance</title>
    <!-- Tailwind CSS v3 + Font Awesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    <!-- Dark overlay (same as other pages) -->
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-[2px]"></div>

    <!-- Top navigation bar (consistent with staff login) -->
    <nav class="absolute top-0 left-0 w-full p-6 z-20 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="bg-white/10 p-2 rounded-lg border border-white/20 backdrop-blur-sm">
                <img src="{{ asset('images/Vanguard.png') }}" alt="Vanguard Assurance Logo" class="w-40 h-12 object-contain" />
            </div>
            <span class="text-white font-bold text-xl tracking-tight drop-shadow-md">Vanguard Assurance</span>
        </div>
        <div class="flex gap-4">
            <a href="#" class="text-white/80 hover:text-white text-sm font-medium transition-colors">Contact
                Support</a>
        </div>
    </nav>

    <div class="max-w-md w-full relative z-10 mt-16 mb-4">
        <!-- Local Login Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden login-card">
            <!-- Card header -->
            <div class="bg-brand-900 px-6 py-4 text-center">
                <div class="flex items-center justify-center gap-3 mb-1">
                    <div
                        class="bg-white/10 w-10 h-10 rounded-full flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <i class="fas fa-lock text-white text-sm"></i>
                    </div>
                    <h2 class="text-lg font-semibold text-white">Local Login</h2>
                </div>
                <p class="text-blue-200 text-xs">Vanguard Assurance - Password authentication</p>
            </div>

            <!-- Form content -->
            <div class="p-6">
                <!-- Fallback reason banner (if redirected from failed API) -->
                @if ($fallbackReason ?? false)
                    <div class="mb-4 flex items-start gap-2 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                        <i class="fas fa-exclamation-triangle text-amber-500 text-sm mt-0.5"></i>
                        <p class="text-xs text-amber-700">{{ $fallbackReason }}</p>
                    </div>
                @else
                    <div class="mb-4 text-center">
                        <p class="text-gray-600 text-sm">Enter your registered phone number and local password.</p>
                    </div>
                @endif

                <!-- Validation errors -->
                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('login.local.submit') }}" method="POST" class="space-y-5" id="localLoginForm">
                    @csrf

                    <!-- Phone Number field -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <div class="relative">
                            <i
                                class="fas fa-phone-alt absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" name="phone" id="phone"
                                value="{{ $prefillPhone ?? old('phone') }}" placeholder="Your registered phone number"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition bg-white @error('phone') border-red-400 bg-red-50 @enderror"
                                required />
                        </div>
                        @error('phone')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password field with toggle -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="password" name="password" id="password" required
                                placeholder="Your local password"
                                class="w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition bg-white @error('password') border-red-400 bg-red-50 @enderror" />
                            <button type="button" id="togglePassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit button -->
                    <button type="submit" id="loginBtn"
                        class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl shadow-sm transition duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500">
                        <i class="fas fa-sign-in-alt text-sm"></i>
                        <span>Sign In</span>
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="px-2 bg-white text-gray-400">or</span>
                    </div>
                </div>

                <!-- Back to standard login link -->
                <div class="text-center">
                    <a href="{{ route('login') }}" class="text-sm text-brand-600 hover:text-brand-700 font-medium">
                        <i class="fas fa-arrow-left mr-1"></i> Back to standard login
                    </a>
                </div>
            </div>

            <!-- Footer -->
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
            <p>© 2026 NissiTechnologies. Secure local authentication.</p>
        </div>
    </div>

    <script>
        // Password visibility toggle
        const toggleBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        if (toggleBtn && passwordInput) {
            toggleBtn.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                const icon = toggleBtn.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                }
            });
        }

        // Loading state on submit
        const form = document.getElementById('localLoginForm');
        const loginBtn = document.getElementById('loginBtn');
        if (form && loginBtn) {
            form.addEventListener('submit', function() {
                loginBtn.disabled = true;
                loginBtn.innerHTML = `
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Signing in...</span>
                `;
            });
        }
    </script>
</body>

</html>