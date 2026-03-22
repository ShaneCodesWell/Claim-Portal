<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login with Password | Vanguard Assurance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ["Inter", "sans-serif"] },
                    colors: {
                        brand: {
                            50: "#eff6ff",
                            100: "#dbeafe",
                            500: "#3b82f6",
                            600: "#2563eb",
                            800: "#1e40af",
                            900: "#1e3a8a",
                        },
                    },
                },
            },
        };
    </script>
</head>

<body class="bg-[url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070&auto=format&fit=crop')] bg-cover bg-center bg-no-repeat bg-fixed min-h-screen flex items-center justify-center p-4 relative">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-[2px]"></div>

    <nav class="absolute top-0 left-0 w-full p-6 z-20 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="bg-white/10 p-2 rounded-lg border border-white/20 backdrop-blur-sm">
                <img src="/images/Vanguard.png" alt="Logo" class="w-40 h-12" />
            </div>
            <span class="text-white font-bold text-xl tracking-tight drop-shadow-md">Claims Portal</span>
        </div>
        <a href="#" class="text-white/80 hover:text-white text-sm font-medium transition-colors">Contact Support</a>
    </nav>

    <div class="w-full max-w-sm bg-white rounded-xl shadow-lg overflow-hidden border border-slate-100 relative z-10 mt-16 mb-4">

        {{-- Header --}}
        <div class="bg-brand-900 p-3 sm:p-4 text-center relative">
            <div class="absolute top-0 left-0 w-full h-1 bg-linear-to-r from-blue-400 to-brand-600"></div>
            <div class="flex items-center justify-center mb-2 sm:mb-3">
                <div class="bg-white/10 w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center backdrop-blur-sm border border-white/20 mr-2 sm:mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <div class="text-left">
                    <h1 class="text-lg sm:text-xl font-bold text-white leading-tight">Local Login</h1>
                    <p class="text-blue-200 text-xs sm:text-sm leading-tight">Vanguard Assurance</p>
                </div>
            </div>
        </div>

        <div class="p-4 sm:p-5">

            {{-- Fallback reason banner (shown when redirected from failed API) --}}
            @if ($fallbackReason)
                <div class="mb-4 flex items-start gap-2 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                    <svg class="h-4 w-4 text-amber-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                    </svg>
                    <p class="text-xs text-amber-700">{{ $fallbackReason }}</p>
                </div>
            @else
                <div class="mb-4 text-center">
                    <p class="text-slate-600 text-sm">Enter your registered phone number and local password.</p>
                </div>
            @endif

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="text-red-600 text-sm mb-4 p-3 bg-red-50 rounded-lg border border-red-100">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.local.submit') }}" method="POST" class="space-y-4" id="localLoginForm">
                @csrf

                {{-- Phone Number --}}
                <div>
                    <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">Phone Number</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                            </svg>
                        </div>
                        <input
                            type="text"
                            name="phone"
                            id="phone"
                            value="{{ $prefillPhone ?? old('phone') }}"
                            placeholder="Your registered phone number"
                            class="block w-full pl-9 pr-3 py-2 text-sm border border-slate-300 rounded-lg bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-1 focus:ring-brand-500 focus:border-brand-500 transition duration-150 ease-in-out @error('phone') border-red-400 bg-red-50 @enderror"
                            required
                        />
                    </div>
                    @error('phone')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="Your local password"
                            class="block w-full pl-9 pr-10 py-2 text-sm border border-slate-300 rounded-lg bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-1 focus:ring-brand-500 focus:border-brand-500 transition duration-150 ease-in-out @error('password') border-red-400 bg-red-50 @enderror"
                            required
                        />
                        {{-- Show/hide toggle --}}
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                            <svg id="eyeIcon" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <button type="submit" id="loginBtn"
                    class="w-full flex items-center justify-center py-2.5 px-4 border border-transparent rounded-lg text-sm font-semibold text-white bg-brand-600 hover:bg-brand-800 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-brand-500 transition-colors duration-200 mt-2">
                    <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Sign In
                </button>
            </form>

            {{-- Back to standard login --}}
            <div class="mt-4 text-center">
                <a href="{{ route('login') }}" class="text-xs text-brand-600 hover:text-brand-800 font-medium transition-colors">
                    ← Back to standard login
                </a>
            </div>
        </div>

        {{-- Footer --}}
        <div class="bg-slate-50 px-4 py-3 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 space-y-1 sm:space-y-0">
            <span class="text-center sm:text-left">Secure 256-bit SSL</span>
            <div class="flex space-x-2">
                <span>Privacy</span>
                <span>•</span>
                <span>Terms</span>
            </div>
        </div>
    </div>

    <script>
        // Show/hide password toggle
        const toggleBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        toggleBtn.addEventListener('click', function () {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
        });

        // Loading state on submit
        const form = document.getElementById('localLoginForm');
        const loginBtn = document.getElementById('loginBtn');

        form.addEventListener('submit', function () {
            loginBtn.disabled = true;
            loginBtn.innerHTML = `
                <svg class="animate-spin h-4 w-4 text-white mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Signing in...
            `;
        });
    </script>
</body>
</html>