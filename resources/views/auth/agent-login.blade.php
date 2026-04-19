<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Vanguard Assurance</title>
    <!-- Tailwind CSS + Font Awesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
    <!-- Dark overlay for better text contrast -->
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-[2px]"></div>

    <!-- Top Navigation Bar (with Staff Login button) -->
    <nav class="absolute top-0 left-0 w-full p-6 z-20 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="bg-white/10 p-2 rounded-lg border border-white/20 backdrop-blur-sm">
                <img src="images/Vanguard.png" alt="Vanguard Assurance Logo" class="w-40 h-12 object-contain" />
            </div>
            {{-- <span class="text-white font-bold text-xl tracking-tight drop-shadow-md">Vanguard Assurance</span> --}}
        </div>
        <div class="flex gap-4">
            <a href="#" class="text-white/80 hover:text-white text-sm font-medium transition-colors">Contact
                Support</a>
            <a href="{{ route('staff.login') }}"
                class="text-white/80 hover:text-white text-sm font-medium transition-colors border-l border-white/30 pl-4 ml-2">
                <i class="fas fa-user-lock mr-1"></i>Staff Login
            </a>
        </div>
    </nav>

    <div class="max-w-md w-full relative z-10 mt-16 mb-4">
        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden login-card">
            <!-- Card header with brand -->
            <div class="bg-brand-900 px-6 py-4 text-center">
                <div class="flex items-center justify-center gap-3 mb-1">
                    <div
                        class="bg-white/10 w-10 h-10 rounded-full flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <i class="fas fa-lock text-white text-sm"></i>
                    </div>
                    <h2 class="text-lg font-semibold text-white">Agent Login portal</h2>
                </div>
                <p class="text-blue-200 text-xs">Vanguard Assurance Claims Portal</p>
            </div>

            <!-- Form content -->
            <div class="p-6">
                <div class="mb-4 text-center">
                    <p class="text-gray-600 text-sm">Enter your credentials to access your account</p>
                    @if ($errors->any())
                        <div class="mt-2 p-2 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                            {{ $errors->first() }}
                        </div>
                    @endif
                </div>

                <form action="{{ route('login.submit') }}" method="POST" class="space-y-5" id="verificationForm">
                    @csrf

                    <!-- Login type dropdown -->
                    <div>
                        <label for="login_type" class="block text-sm font-medium text-gray-700 mb-1">Login with</label>
                        <div class="relative">
                            <i
                                class=" absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                            <select name="login_type" id="login_type"
                                class="w-full pl-4 pr-8 py-2.5 border border-gray-300 rounded-xl text-sm bg-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                                <option value="mobile_no">Phone Number</option>
                                <option value="policy_number">Policy Number</option>
                                <option value="vehicle_number">Vehicle Number</option>
                            </select>
                        </div>
                    </div>

                    <!-- Username field -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Mobile Number / Email
                            / Customer Code</label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" name="username" id="username" required
                                placeholder="e.g., 0244123456 or policy number"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition bg-white">
                        </div>
                    </div>

                    <!-- Submit button -->
                    <button type="submit" id="sendOtpBtn"
                        class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl shadow-sm transition duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500">
                        <i class="fas fa-paper-plane text-sm"></i>
                        <span>Send OTP</span>
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

                <!-- Password login link -->
                <div class="text-center">
                    <a href="{{ route('login.local') }}"
                        class="text-sm text-brand-600 hover:text-brand-700 font-medium">
                        <i class="fas fa-lock mr-1"></i> Login with Password
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
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('verificationForm');
            const submitBtn = document.getElementById('sendOtpBtn');

            form.addEventListener('submit', function(e) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Sending...</span>
                `;
            });
        });
    </script>
</body>

</html>
