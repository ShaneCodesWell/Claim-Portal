<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login | Vanguard Assurance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=DM+Serif+Display:ital@0;1&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['"DM Serif Display"', 'serif'],
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

        /* ── AUTH MODAL ─────────────────────────────────────── */
        #authModal {
            display: none;
            opacity: 0;
            transition: opacity 0.25s ease;
        }

        #authModal.visible {
            opacity: 1;
        }

        #authModalCard {
            transform: translateY(24px) scale(0.97);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.25s ease;
            opacity: 0;
        }

        #authModal.visible #authModalCard {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        /* ── STAGES: only one visible at a time ─────────────── */
        .modal-stage {
            display: none;
        }

        .modal-stage.active {
            display: block;
        }

        /* ── ANIMATED LOADER DOTS ───────────────────────────── */
        .loader-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #2563eb;
            animation: loaderPulse 1.4s ease-in-out infinite;
        }

        .loader-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .loader-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes loaderPulse {

            0%,
            80%,
            100% {
                transform: scale(0.6);
                opacity: 0.4;
            }

            40% {
                transform: scale(1.1);
                opacity: 1;
            }
        }

        /* ── ORBITING RING ──────────────────────────────────── */
        @keyframes orbit {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .orbit-ring {
            animation: orbit 2s linear infinite;
            transform-origin: center;
        }

        .orbit-ring-slow {
            animation: orbit 3s linear infinite reverse;
            transform-origin: center;
        }

        /* ── SUCCESS CHECKMARK ──────────────────────────────── */
        @keyframes checkDraw {
            from {
                stroke-dashoffset: 50;
            }

            to {
                stroke-dashoffset: 0;
            }
        }

        .check-path {
            stroke-dasharray: 50;
            stroke-dashoffset: 50;
            animation: checkDraw 0.5s ease forwards 0.2s;
        }

        /* ── GLIMS BADGE PULSE ──────────────────────────────── */
        @keyframes badgePulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(234, 179, 8, 0.4);
            }

            50% {
                box-shadow: 0 0 0 8px rgba(234, 179, 8, 0);
            }
        }

        .glims-badge {
            animation: badgePulse 2s ease infinite;
        }

        /* ── PASSWORD STRENGTH BAR ──────────────────────────── */
        .strength-bar-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.3s ease, background-color 0.3s ease;
        }

        /* ── SUBTLE SLIDE-IN FOR EACH STAGE ─────────────────── */
        @keyframes stageFadeIn {
            from {
                opacity: 0;
                transform: translateX(12px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .modal-stage.active {
            animation: stageFadeIn 0.25s ease forwards;
        }

        /* ── MESSAGE CYCLE TRANSITION ───────────────────────── */
        #loaderMessage {
            transition: opacity 0.4s ease;
        }

        #loaderMessage.fading {
            opacity: 0;
        }
    </style>
</head>

<body class="font-sans antialiased min-h-screen flex items-center justify-center p-4 relative">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-[2px]"></div>

    <!-- Top Nav -->
    <nav class="absolute top-0 left-0 w-full p-6 z-20 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="bg-white/10 p-2 rounded-lg border border-white/20 backdrop-blur-sm">
                <img src="images/Vanguard.png" alt="Vanguard Assurance Logo" class="w-40 h-12 object-contain" />
            </div>
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

    <!-- Login Card -->
    <div class="max-w-md w-full relative z-10 mt-16 mb-4">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-brand-900 px-6 py-4 text-center">
                <div class="flex items-center justify-center gap-3 mb-1">
                    <div
                        class="bg-white/10 w-10 h-10 rounded-full flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <i class="fas fa-lock text-white text-sm"></i>
                    </div>
                    <h2 class="text-lg font-semibold text-white">Customer Login Portal</h2>
                </div>
                <p class="text-blue-200 text-xs">Vanguard Assurance Claims Portal</p>
            </div>

            <div class="p-6">
                <div class="mb-4 text-center">
                    <p class="text-gray-600 text-sm">Enter your credentials to access your account</p>
                    @if ($errors->any())
                        <div class="mt-2 p-2 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                            {{ $errors->first() }}
                        </div>
                    @endif
                </div>

                {{--
                    NOTE: The form no longer has action/method for a full page POST.
                    It is intercepted by JS and submitted via fetch() to /login/ajax.
                    The original route('/login') fallback still works for no-JS browsers
                    because we leave action on the form — JS prevents default when available.
                --}}
                <form action="{{ route('login.submit') }}" method="POST" class="space-y-5" id="verificationForm">
                    @csrf

                    <div>
                        <label for="login_type" class="block text-sm font-medium text-gray-700 mb-1">Login with</label>
                        <div class="relative">
                            <select name="login_type" id="login_type"
                                class="w-full pl-4 pr-8 py-2.5 border border-gray-300 rounded-xl text-sm bg-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                                <option value="mobile_no">Phone Number</option>
                                <option value="policy_number">Policy Number</option>
                                <option value="vehicle_number">Vehicle Number</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Mobile Number /
                            Policy / Vehicle Number</label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" name="username" id="username" required
                                placeholder="e.g., 0244123456 or policy number"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition bg-white">
                        </div>
                    </div>

                    <button type="submit" id="sendOtpBtn"
                        class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl shadow-sm transition duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500">
                        <i class="fas fa-paper-plane text-sm"></i>
                        <span>Request Access</span>
                    </button>
                </form>

                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="px-2 bg-white text-gray-400">or</span>
                    </div>
                </div>

                <div class="text-center">
                    <a href="{{ route('login.local') }}"
                        class="text-sm text-brand-600 hover:text-brand-700 font-medium">
                        <i class="fas fa-lock mr-1"></i> Login with Password
                    </a>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100 flex justify-between text-xs text-gray-400">
                <span><i class="fas fa-shield-alt mr-1"></i> 256-bit SSL</span>
                <div class="flex gap-3">
                    <a href="#" class="hover:text-gray-600">Privacy</a>
                    <a href="#" class="hover:text-gray-600">Terms</a>
                </div>
            </div>
        </div>
    </div>


    {{-- ═══════════════════════════════════════════════════════
         AUTH MODAL — full-screen overlay
         Stages: loading | success_genova | success_glims |
                 local_password | setup_password | error
    ═══════════════════════════════════════════════════════ --}}
    <div id="authModal" class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(6px);">

        <div id="authModalCard"
            class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden border border-gray-100">

            {{-- ── STAGE: LOADING ───────────────────────────── --}}
            <div id="stage-loading" class="modal-stage p-8 text-center">
                <!-- Animated orb -->
                <div class="flex items-center justify-center mb-6">
                    <div class="relative w-20 h-20">
                        <!-- Outer ring -->
                        <svg class="orbit-ring absolute inset-0 w-full h-full" viewBox="0 0 80 80">
                            <circle cx="40" cy="40" r="36" fill="none" stroke="#dbeafe"
                                stroke-width="2" />
                            <circle cx="40" cy="4" r="4" fill="#2563eb" />
                        </svg>
                        <!-- Inner ring -->
                        <svg class="orbit-ring-slow absolute inset-0 w-full h-full" viewBox="0 0 80 80">
                            <circle cx="40" cy="40" r="26" fill="none" stroke="#eff6ff"
                                stroke-width="1.5" />
                            <circle cx="40" cy="14" r="3" fill="#93c5fd" />
                        </svg>
                        <!-- Core icon -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div
                                class="w-10 h-10 bg-brand-900 rounded-full flex items-center justify-center shadow-lg">
                                <i class="fas fa-shield-alt text-white text-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <h3 class="font-display text-xl text-gray-900 mb-2">Verifying your identity</h3>
                <p id="loaderMessage" class="text-sm text-gray-500 min-h-5 transition-opacity duration-300">
                    Connecting to Vanguard Assurance...
                </p>

                <!-- Animated dots -->
                <div class="flex items-center justify-center gap-2 mt-5">
                    <div class="loader-dot"></div>
                    <div class="loader-dot"></div>
                    <div class="loader-dot"></div>
                </div>

                <p class="text-xs text-gray-400 mt-5">Please don't close this window</p>
            </div>

            {{-- ── STAGE: SUCCESS (Genova) ──────────────────── --}}
            <div id="stage-success-genova" class="modal-stage">
                <div class="bg-brand-900 px-6 py-5 text-center">
                    <!-- Success check -->
                    <div class="flex items-center justify-center mb-3">
                        <div
                            class="w-14 h-14 bg-white/10 rounded-full flex items-center justify-center border border-white/20">
                            <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
                                <path class="check-path" d="M6 14l6 6 10-12" stroke="white" stroke-width="2.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="font-display text-xl text-white mb-1">Welcome back!</h3>
                    <p id="genova-name" class="text-blue-200 text-sm"></p>
                </div>
                <div class="p-6 text-center">
                    <p class="text-sm text-gray-600 mb-5">Your identity has been verified. We've sent an OTP to your
                        registered contact.</p>
                    <div
                        class="flex items-center gap-2 bg-brand-50 border border-brand-100 rounded-xl p-3 text-left mb-4">
                        <i class="fas fa-info-circle text-brand-600 text-sm shrink-0"></i>
                        <p class="text-xs text-brand-800">You'll be redirected to enter your OTP shortly.</p>
                    </div>
                    <div class="flex justify-center">
                        <div class="loader-dot"></div>
                        <div class="loader-dot mx-1"></div>
                        <div class="loader-dot"></div>
                    </div>
                </div>
            </div>

            {{-- ── STAGE: SUCCESS (GLIMS fallback) ─────────── --}}
            <div id="stage-success-glims" class="modal-stage">
                <div class="bg-amber-600 px-6 py-5 text-center">
                    <div class="flex items-center justify-center mb-3">
                        <div
                            class="w-14 h-14 bg-white/10 rounded-full flex items-center justify-center border border-white/20 glims-badge">
                            <i class="fas fa-database text-white text-lg"></i>
                        </div>
                    </div>
                    <h3 class="font-display text-xl text-white mb-1">Account found</h3>
                    <p id="glims-name" class="text-amber-100 text-sm"></p>
                </div>
                <div class="p-6">
                    <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl p-3 mb-5">
                        <i class="fas fa-exclamation-triangle text-amber-500 text-sm mt-0.5 shrink-0"></i>
                        <p class="text-xs text-amber-800 leading-relaxed">
                            We found your account through our records database. To ensure you always have access to your
                            policies — even when our primary service is down — please set up a local password.
                        </p>
                    </div>
                    <button id="glimsSetupPasswordBtn"
                        class="w-full py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-xl transition">
                        <i class="fas fa-key mr-2"></i>Set Up My Password
                    </button>
                    <button id="glimsSkipBtn" data-skip-count="0"
                        class="w-full py-2 mt-2 text-xs text-gray-400 hover:text-gray-600 transition">
                        Remind me later
                    </button>
                </div>
            </div>

            {{-- ── STAGE: LOCAL PASSWORD (both APIs down) ───── --}}
            <div id="stage-local-password" class="modal-stage">
                <div class="bg-slate-800 px-6 py-5 text-center">
                    <div class="flex items-center justify-center mb-3">
                        <div
                            class="w-14 h-14 bg-white/10 rounded-full flex items-center justify-center border border-white/20">
                            <i class="fas fa-wifi text-white text-lg opacity-40"></i>
                            <i class="fas fa-slash text-white text-lg absolute"></i>
                        </div>
                    </div>
                    <h3 class="font-display text-xl text-white mb-1">Service unavailable</h3>
                    <p class="text-slate-400 text-xs">Use your local password to continue</p>
                </div>
                <div class="p-6">
                    <div class="flex items-start gap-3 bg-slate-50 border border-slate-200 rounded-xl p-3 mb-5">
                        <i class="fas fa-info-circle text-slate-500 text-sm mt-0.5 shrink-0"></i>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Our verification service is temporarily unavailable. Since you've previously set up a local
                            password, you can still access your account.
                        </p>
                    </div>
                    <form id="localPasswordForm" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <div class="relative">
                                <i
                                    class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="password" id="localPasswordInput" name="password" required
                                    placeholder="Enter your password"
                                    class="w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                                <button type="button"
                                    class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                                    data-target="localPasswordInput">
                                    <i class="fas fa-eye text-sm"></i>
                                </button>
                            </div>
                        </div>
                        <div id="localPasswordError"
                            class="hidden text-xs text-red-600 bg-red-50 border border-red-200 rounded-lg p-2"></div>
                        <button type="submit"
                            class="w-full py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-xl transition">
                            <i class="fas fa-sign-in-alt mr-2"></i>Log In
                        </button>
                    </form>
                </div>
            </div>

            {{-- ── STAGE: SET UP PASSWORD ───────────────────── --}}
            <div id="stage-setup-password" class="modal-stage">
                <div class="bg-brand-900 px-6 py-4 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <i class="fas fa-key text-white"></i>
                        <h3 class="font-display text-lg text-white">Secure your account</h3>
                    </div>
                    <p class="text-blue-200 text-xs mt-1">One-time setup — takes 30 seconds</p>
                </div>
                <div class="p-6">
                    <!-- Progress steps -->
                    <div class="flex items-center justify-center gap-2 mb-5">
                        <div class="flex items-center gap-1.5">
                            <div class="w-5 h-5 rounded-full bg-green-500 flex items-center justify-center">
                                <i class="fas fa-check text-white" style="font-size:8px"></i>
                            </div>
                            <span class="text-xs text-gray-500">Verified</span>
                        </div>
                        <div class="w-8 h-px bg-gray-300"></div>
                        <div class="flex items-center gap-1.5">
                            <div class="w-5 h-5 rounded-full bg-brand-600 flex items-center justify-center">
                                <span class="text-white font-bold" style="font-size:9px">2</span>
                            </div>
                            <span class="text-xs text-brand-600 font-medium">Set Password</span>
                        </div>
                        <div class="w-8 h-px bg-gray-300"></div>
                        <div class="flex items-center gap-1.5">
                            <div class="w-5 h-5 rounded-full bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-400 font-bold" style="font-size:9px">3</span>
                            </div>
                            <span class="text-xs text-gray-400">Dashboard</span>
                        </div>
                    </div>

                    <p class="text-xs text-gray-500 text-center mb-5 leading-relaxed">
                        Set a local password so you can always access your claims portal — even when our external
                        services are unavailable.
                    </p>

                    <form id="setupPasswordForm" class="space-y-4">
                        <!-- Password -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                            <div class="relative">
                                <i
                                    class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="password" id="newPassword" name="password" required
                                    placeholder="Min 8 chars, 1 letter + 1 number"
                                    class="w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                                <button type="button"
                                    class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                                    data-target="newPassword">
                                    <i class="fas fa-eye text-sm"></i>
                                </button>
                            </div>
                            <!-- Strength bar -->
                            <div class="mt-2">
                                <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                    <div id="strengthBar" class="strength-bar-fill w-0 bg-red-400"></div>
                                </div>
                                <p id="strengthLabel" class="text-xs text-gray-400 mt-1"></p>
                            </div>
                        </div>

                        <!-- Confirm -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                            <div class="relative">
                                <i
                                    class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="password" id="confirmPassword" name="password_confirmation" required
                                    placeholder="Repeat your password"
                                    class="w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                                <button type="button"
                                    class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                                    data-target="confirmPassword">
                                    <i class="fas fa-eye text-sm"></i>
                                </button>
                            </div>
                        </div>

                        <div id="setupPasswordError"
                            class="hidden text-xs text-red-600 bg-red-50 border border-red-200 rounded-lg p-2"></div>

                        <button type="submit" id="setupPasswordBtn"
                            class="w-full py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl transition">
                            <i class="fas fa-shield-alt mr-2"></i>Save Password & Continue
                        </button>

                        <button type="button" id="skipSetupBtn"
                            class="w-full py-2 text-xs text-gray-400 hover:text-gray-600 transition hidden"
                            data-skip-count="0">
                            Skip for now (not recommended)
                        </button>
                    </form>
                </div>
            </div>

            {{-- ── STAGE: ERROR ─────────────────────────────── --}}
            <div id="stage-error" class="modal-stage p-8 text-center">
                <div class="flex items-center justify-center mb-5">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                    </div>
                </div>
                <h3 class="font-display text-xl text-gray-900 mb-2">Unable to verify</h3>
                <p id="errorMessage" class="text-sm text-gray-500 mb-6 leading-relaxed"></p>
                <button id="errorRetryBtn"
                    class="w-full py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl transition mb-3">
                    <i class="fas fa-redo mr-2"></i>Try Again
                </button>
                <a href="{{ route('login.local') }}"
                    class="block text-sm text-gray-500 hover:text-brand-600 transition">
                    <i class="fas fa-lock mr-1"></i>Use local password instead
                </a>
            </div>

        </div>{{-- /#authModalCard --}}
    </div>{{-- /#authModal --}}


    <script>
        (() => {
            // ── CONFIG ──────────────────────────────────────────────
            const AJAX_URL = '{{ route('login.ajax') }}';
            const LOCAL_LOGIN_URL = '{{ route('login.local.submit') }}';
            const SETUP_PW_URL = '{{ route('setup.password.ajax') }}';
            const CSRF = document.querySelector('meta[name="csrf-token"]').content;

            // Messages cycled during the loading stage.
            // We keep them generic and reassuring — no technical jargon.
            const LOAD_MESSAGES = [
                'Connecting to Vanguard Assurance...',
                'Verifying your identity...',
                'Gathering your policies...',
                'Checking our records...',
                'Almost there...',
            ];

            // Max times a user can defer the password setup before it becomes required.
            const MAX_SKIPS = 1;

            // ── STATE ────────────────────────────────────────────────
            let currentStage = null;
            let messageInterval = null;
            let messageIndex = 0;
            let skipCount = 0;

            // ── DOM REFS ─────────────────────────────────────────────
            const modal = document.getElementById('authModal');
            const form = document.getElementById('verificationForm');
            const loaderMessage = document.getElementById('loaderMessage');
            const errorMessage = document.getElementById('errorMessage');
            const genovaName = document.getElementById('genova-name');
            const glimsName = document.getElementById('glims-name');

            // ── MODAL OPEN / CLOSE ───────────────────────────────────
            function openModal() {
                modal.style.display = 'flex';
                // Force reflow before adding class so transition fires
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => modal.classList.add('visible'));
                });
            }

            function closeModal() {
                modal.classList.remove('visible');
                setTimeout(() => {
                    modal.style.display = 'none';
                    stopMessageCycle();
                }, 300);
            }

            // ── STAGE SWITCHER ────────────────────────────────────────
            function showStage(name) {
                document.querySelectorAll('.modal-stage').forEach(el => el.classList.remove('active'));
                const stage = document.getElementById('stage-' + name);
                if (stage) stage.classList.add('active');
                currentStage = name;
            }

            // ── ANIMATED MESSAGE CYCLE ───────────────────────────────
            function startMessageCycle() {
                messageIndex = 0;
                loaderMessage.textContent = LOAD_MESSAGES[0];

                messageInterval = setInterval(() => {
                    // Fade out
                    loaderMessage.classList.add('fading');
                    setTimeout(() => {
                        messageIndex = (messageIndex + 1) % LOAD_MESSAGES.length;
                        loaderMessage.textContent = LOAD_MESSAGES[messageIndex];
                        loaderMessage.classList.remove('fading');
                    }, 400);
                }, 2200);
            }

            function stopMessageCycle() {
                if (messageInterval) {
                    clearInterval(messageInterval);
                    messageInterval = null;
                }
            }

            // ── PASSWORD STRENGTH ─────────────────────────────────────
            const strengthBar = document.getElementById('strengthBar');
            const strengthLabel = document.getElementById('strengthLabel');

            function checkStrength(pw) {
                let score = 0;
                if (pw.length >= 8) score++;
                if (/[A-Z]/.test(pw)) score++;
                if (/[0-9]/.test(pw)) score++;
                if (/[^A-Za-z0-9]/.test(pw)) score++;

                const levels = [{
                        width: '25%',
                        color: 'bg-red-400',
                        label: 'Weak'
                    },
                    {
                        width: '50%',
                        color: 'bg-orange-400',
                        label: 'Fair'
                    },
                    {
                        width: '75%',
                        color: 'bg-yellow-400',
                        label: 'Good'
                    },
                    {
                        width: '100%',
                        color: 'bg-green-500',
                        label: 'Strong'
                    },
                ];

                const level = levels[Math.max(0, score - 1)] ?? levels[0];

                strengthBar.style.width = pw.length ? level.width : '0';
                strengthBar.className = 'strength-bar-fill ' + (pw.length ? level.color : '');
                strengthLabel.textContent = pw.length ? level.label : '';
            }

            document.getElementById('newPassword')?.addEventListener('input', e => {
                checkStrength(e.target.value);
            });

            // ── TOGGLE PASSWORD VISIBILITY ────────────────────────────
            document.querySelectorAll('.toggle-password').forEach(btn => {
                btn.addEventListener('click', () => {
                    const target = document.getElementById(btn.dataset.target);
                    const icon = btn.querySelector('i');
                    if (target.type === 'password') {
                        target.type = 'text';
                        icon.classList.replace('fa-eye', 'fa-eye-slash');
                    } else {
                        target.type = 'password';
                        icon.classList.replace('fa-eye-slash', 'fa-eye');
                    }
                });
            });

            // ── FORM SUBMIT → AJAX LOGIN ──────────────────────────────
            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                openModal();
                showStage('loading');
                startMessageCycle();

                const formData = new FormData(form);

                try {
                    const res = await fetch(AJAX_URL, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': CSRF,
                            'Accept': 'application/json'
                        },
                        body: formData,
                    });

                    stopMessageCycle();
                    const data = await res.json();

                    handleAuthResponse(data, formData.get('username'));

                } catch (err) {
                    stopMessageCycle();
                    showError('A network error occurred. Please check your connection and try again.');
                }
            });

            // ── HANDLE JSON RESPONSE FROM /login/ajax ─────────────────
            function handleAuthResponse(data, identifier) {
                switch (data.status) {
                    case 'success':
                        if (data.source === 'glims') {
                            glimsName.textContent = data.name ?? '';

                            if (data.needs_password_setup) {
                                // No password yet — show the setup prompt
                                showStage('success-glims');
                            } else {
                                // Already has a password — redirect straight to dashboard
                                setTimeout(() => {
                                    window.location.href = data.redirect;
                                }, 800);
                            }
                        } else {
                            // Genova success
                            genovaName.textContent = data.name ?? '';
                            showStage('success-genova');

                            if (data.needs_password_setup) {
                                // After a brief celebratory moment, slide into password setup
                                setTimeout(() => showStage('setup-password'), 2200);
                            } else {
                                // All done — redirect
                                setTimeout(() => {
                                    window.location.href = data.redirect;
                                }, 2000);
                            }
                        }
                        break;

                    case 'local_password_available':
                        // Both APIs down but customer has a local password
                        showStage('local-password');
                        break;

                    case 'error':
                    default:
                        showError(data.message ?? 'Something went wrong. Please try again.');
                        break;
                }
            }

            // ── GLIMS STAGE BUTTONS ───────────────────────────────────
            document.getElementById('glimsSetupPasswordBtn')?.addEventListener('click', () => {
                showStage('setup-password');
            });

            document.getElementById('glimsSkipBtn')?.addEventListener('click', () => {
                skipCount++;
                if (skipCount >= MAX_SKIPS) {
                    // Used their last skip — force setup
                    const btn = document.getElementById('glimsSkipBtn');
                    btn.textContent = "You'll need to set a password to continue.";
                    btn.disabled = true;
                    setTimeout(() => showStage('setup-password'), 1200);
                } else {
                    // Let them in — they'll see the dashboard nudge
                    // For GLIMS users, we need to proceed to dashboard via a silent redirect
                    // The session is already set from loginAjax, so direct navigation works
                    window.location.href = '{{ route('dashboard') }}';
                }
            });

            // ── LOCAL PASSWORD FORM (APIs down) ───────────────────────
            document.getElementById('localPasswordForm')?.addEventListener('submit', async (e) => {
                e.preventDefault();

                const errorEl = document.getElementById('localPasswordError');
                const submitBtn = e.target.querySelector('button[type="submit"]');
                const phone = document.getElementById('username')?.value ??
                    document.querySelector('[name="username"]')?.value;

                errorEl.classList.add('hidden');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Logging in...';

                const formData = new FormData();
                formData.append('phone', phone);
                formData.append('password', document.getElementById('localPasswordInput').value);

                try {
                    const res = await fetch(LOCAL_LOGIN_URL, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': CSRF,
                            'Accept': 'application/json'
                        },
                        body: formData,
                    });

                    if (res.redirected) {
                        // Laravel redirected → success, follow it
                        window.location.href = res.url;
                        return;
                    }

                    const data = await res.json();

                    if (res.ok) {
                        window.location.href = '{{ route('dashboard') }}';
                    } else {
                        const msg = data.errors?.phone?.[0] ??
                            data.errors?.password?.[0] ??
                            data.message ??
                            'Incorrect password. Please try again.';

                        errorEl.textContent = msg;
                        errorEl.classList.remove('hidden');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-sign-in-alt mr-2"></i>Log In';
                    }

                } catch (err) {
                    errorEl.textContent = 'Network error. Please try again.';
                    errorEl.classList.remove('hidden');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-sign-in-alt mr-2"></i>Log In';
                }
            });

            // ── SETUP PASSWORD FORM ───────────────────────────────────
            document.getElementById('setupPasswordForm')?.addEventListener('submit', async (e) => {
                e.preventDefault();

                const errorEl = document.getElementById('setupPasswordError');
                const submitBtn = document.getElementById('setupPasswordBtn');
                const pw = document.getElementById('newPassword').value;
                const pwConfirm = document.getElementById('confirmPassword').value;

                errorEl.classList.add('hidden');

                // Client-side check before hitting the server
                if (pw !== pwConfirm) {
                    errorEl.textContent = 'Passwords do not match.';
                    errorEl.classList.remove('hidden');
                    return;
                }

                if (pw.length < 8 || !/[a-zA-Z]/.test(pw) || !/[0-9]/.test(pw)) {
                    errorEl.textContent =
                        'Password must be at least 8 characters with a letter and a number.';
                    errorEl.classList.remove('hidden');
                    return;
                }

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';

                const formData = new FormData();
                formData.append('password', pw);
                formData.append('password_confirmation', pwConfirm);

                try {
                    const res = await fetch(SETUP_PW_URL, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': CSRF,
                            'Accept': 'application/json'
                        },
                        body: formData,
                    });

                    const data = await res.json();

                    if (data.status === 'success') {
                        submitBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Saved!';
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 800);
                    } else {
                        const firstError = data.errors?.password?.[0] ?? data.message ??
                            'Could not save password.';
                        errorEl.textContent = firstError;
                        errorEl.classList.remove('hidden');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML =
                            '<i class="fas fa-shield-alt mr-2"></i>Save Password & Continue';
                    }

                } catch (err) {
                    errorEl.textContent = 'Network error. Please try again.';
                    errorEl.classList.remove('hidden');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-shield-alt mr-2"></i>Save Password & Continue';
                }
            });

            // ── SKIP SETUP (Genova path — shown if MAX_SKIPS > 1) ────
            const skipSetupBtn = document.getElementById('skipSetupBtn');
            if (skipSetupBtn) {
                // Only show skip for Genova users (GLIMS skip is handled separately above)
                // Unhide it after the setup stage renders
                skipSetupBtn.classList.remove('hidden');

                skipSetupBtn.addEventListener('click', () => {
                    skipCount++;
                    if (skipCount >= MAX_SKIPS) {
                        skipSetupBtn.textContent = 'Password setup is required to continue.';
                        skipSetupBtn.disabled = true;
                    } else {
                        // Get redirect from the last response — stored on window
                        window.location.href = window._authRedirect ?? '{{ route('dashboard') }}';
                    }
                });
            }

            // ── ERROR STAGE ───────────────────────────────────────────
            function showError(msg) {
                errorMessage.textContent = msg;
                showStage('error');
            }

            document.getElementById('errorRetryBtn')?.addEventListener('click', () => {
                closeModal();
            });

        })();
    </script>
</body>

</html>
