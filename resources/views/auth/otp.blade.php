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
        <div class="bg-brand-900 p-2 sm:p-4 text-center relative">
            <div class="absolute top-0 left-0 w-full h-1 bg-linear-to-r from-blue-400 to-brand-600"></div>
            <div class="flex items-center justify-center sm:mb-3">
                <div
                    class="bg-white/10 w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center backdrop-blur-sm border border-white/20 mr-2 sm:mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
                <div class="text-left">
                    <h1 class="text-lg sm:text-xl font-bold text-white leading-tight">OTP Verification</h1>
                    <p class="text-blue-200 text-xs sm:text-sm leading-tight">Vanguard Assurance</p>
                </div>
            </div>
        </div>

        <div class="p-4 sm:p-5">

            <!-- Compact instruction text -->
            <div class="mb-4 text-center">
                <p class="text-slate-600 text-sm mb-1">Enter the 6-digit verification code.</p>
                @if ($errors->any())
                    <div class="text-red-600 text-xs p-2 bg-red-50 rounded mt-2">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="text-green-600 text-xs p-2 bg-green-50 rounded mt-2">
                        {{ session('success') }}
                    </div>
                @endif
            </div>

            <!-- OTP Verification Form -->
            <form action="{{ route('otp.verify') }}" method="POST" class="space-y-4" id="verificationForm">
                @csrf

                <!-- OTP Input Field -->
                <div>
                    <label for="otp" class="block text-sm font-medium text-slate-700 mb-1">Verification
                        Code</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" id="otp" name="otp" placeholder="Enter 6-digit code"
                            maxlength="6"
                            class="block w-full pl-9 pr-3 py-2 text-sm border border-slate-300 rounded-lg bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-1 focus:ring-brand-500 focus:border-brand-500 transition duration-150 ease-in-out"
                            required />
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        Enter the code sent to your registered phone number
                    </p>
                </div>

                <!-- Verify Button -->
                <button type="submit"
                    class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-brand-500 transition-colors duration-200 mt-2">
                    <svg class="-ml-0.5 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                    Verify & Continue
                </button>
            </form>

            <!-- Help Section -->
            <div class="mt-5 pt-2 border-t border-slate-100">
                <div class="text-xs text-slate-500">
                    <p class="flex items-start">
                        <svg class="h-3 w-3 mr-1.5 mt-0.5 text-slate-400 shrink-0" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                        Didn't receive the code? Check your SMS messages or wait 60 seconds to resend.
                    </p>
                </div>
            </div>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sendOtpBtn = document.getElementById('sendOtpBtn');
            const otpTimer = document.getElementById('otpTimer');
            const timerText = document.getElementById('timerText');
            const countdownElement = document.getElementById('countdown');
            const otpForm = document.getElementById('verificationForm');

            let countdown = 60;
            let timer = null;

            // Function to start countdown
            function startCountdown() {
                sendOtpBtn.disabled = true;
                otpTimer.classList.remove('hidden');
                countdown = 60;
                countdownElement.textContent = countdown;

                timer = setInterval(() => {
                    countdown--;
                    countdownElement.textContent = countdown;

                    if (countdown <= 0) {
                        clearInterval(timer);
                        sendOtpBtn.disabled = false;
                        otpTimer.classList.add('hidden');
                        timerText.innerHTML = '<span class="text-green-600">Ready to resend OTP</span>';
                    }
                }, 1000);
            }

            // Handle OTP form submission
            if (sendOtpBtn) {
                sendOtpBtn.addEventListener('click', function(e) {
                    // Only start countdown if form submission succeeds
                    // In a real app, you'd want to start countdown after successful API response
                    // For now, we'll start it immediately for demo purposes
                    setTimeout(() => {
                        startCountdown();
                        timerText.innerHTML = 'Resend available in <span id="countdown">60</span>s';
                    }, 500);
                });
            }

            // Auto-focus OTP input and format
            const otpInput = document.getElementById('otp');
            if (otpInput) {
                otpInput.focus();

                // Format OTP input (only numbers, max 6 digits)
                otpInput.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
                });
            }
        });
    </script>
</body>

</html>
