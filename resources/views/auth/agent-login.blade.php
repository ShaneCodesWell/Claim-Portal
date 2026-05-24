<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Login | Vanguard Assurance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
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
    </style>
</head>

<body class="font-sans antialiased min-h-screen flex items-center justify-center p-4 relative">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-[2px]"></div>

    <nav class="absolute top-0 left-0 w-full p-6 z-20 flex justify-between items-center">
        <div class="bg-white/10 p-2 rounded-lg border border-white/20 backdrop-blur-sm">
            <img src="{{ asset('images/Vanguard.png') }}" alt="Vanguard Assurance" class="w-40 h-12 object-contain">
        </div>
        <div class="flex gap-4">
            <a href="#" class="text-white/80 hover:text-white text-sm font-medium transition-colors">Contact
                Support</a>
            <a href="{{ route('staff.login') }}"
                class="text-white/80 hover:text-white text-sm font-medium transition-colors border-l border-white/30 pl-4 ml-2">
                <i class="fas fa-user-lock mr-1"></i> Staff Login
            </a>
        </div>
    </nav>

    <div class="max-w-md w-full relative z-10 mt-16 mb-4">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">

            {{-- Header --}}
            <div class="bg-brand-900 px-6 py-5 text-center">
                <div class="flex items-center justify-center gap-3 mb-1">
                    <div
                        class="bg-white/10 w-10 h-10 rounded-full flex items-center justify-center border border-white/20">
                        <i class="fas fa-user-tie text-white text-sm"></i>
                    </div>
                    <h2 class="text-lg font-semibold text-white">Agent Login</h2>
                </div>
                <p class="text-blue-200 text-xs">Vanguard Assurance — Intermediary Portal</p>
            </div>

            {{-- Form --}}
            <div class="p-6">
                <p class="text-gray-500 text-sm text-center mb-5">
                    Sign in with your registered phone number and password.
                </p>

                @if ($errors->any())
                    <div
                        class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm flex items-center gap-2">
                        <i class="fas fa-exclamation-circle shrink-0"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('agent.login.submit') }}" method="POST" class="space-y-4" id="agentLoginForm">
                    @csrf

                    {{-- Phone --}}
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                            Phone Number
                        </label>
                        <div class="relative">
                            <i class="fas fa-phone absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required
                                placeholder="e.g. 0244123456"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm
                                       focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition bg-white
                                       @error('phone') border-red-400 @enderror">
                        </div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            Password
                        </label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="password" name="password" id="password" required
                                placeholder="Enter your password"
                                class="w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-xl text-sm
                                       focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition bg-white">
                            <button type="button" id="togglePassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" id="submitBtn"
                        class="w-full flex items-center justify-center gap-2 py-2.5 px-4
                               bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold
                               rounded-xl shadow-sm transition duration-200
                               focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500">
                        <i class="fas fa-sign-in-alt text-sm"></i>
                        <span>Sign In</span>
                    </button>
                </form>

                <p class="text-center text-xs text-gray-400 mt-5">
                    Having trouble? <a href="#" class="text-brand-600 hover:underline">Contact support</a>
                </p>
            </div>

            {{-- Footer --}}
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100 flex justify-between text-xs text-gray-400">
                <span><i class="fas fa-shield-alt mr-1"></i> 256-bit SSL</span>
                <div class="flex gap-3">
                    <a href="#" class="hover:text-gray-600">Privacy</a>
                    <a href="#" class="hover:text-gray-600">Terms</a>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password toggle
            document.getElementById('togglePassword').addEventListener('click', function() {
                const input = document.getElementById('password');
                const icon = this.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('fa-eye', 'fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.replace('fa-eye-slash', 'fa-eye');
                }
            });

            // Loading state on submit
            document.getElementById('agentLoginForm').addEventListener('submit', function() {
                const btn = document.getElementById('submitBtn');
                btn.disabled = true;
                btn.innerHTML = `
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span>Signing in...</span>`;
            });
        });
    </script>
</body>

</html>
