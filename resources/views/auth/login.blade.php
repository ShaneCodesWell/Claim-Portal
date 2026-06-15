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
    <link rel="icon" type="image/png" href="{{ asset('images/favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}" />
    <link rel="manifest" href="{{ asset('images/site.webmanifest') }}" />
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

        .modal-stage {
            display: none;
        }

        .modal-stage.active {
            display: block;
        }

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

        #loaderMessage {
            transition: opacity 0.4s ease;
        }

        #loaderMessage.fading {
            opacity: 0;
        }

        /* OTP digit inputs */
        .otp-digit:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .otp-digit.filled {
            border-color: #2563eb;
            background: #eff6ff;
        }
    </style>
</head>

<body class="font-sans antialiased min-h-screen flex items-center justify-center p-4 relative">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-[2px]"></div>

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
                    <p class="text-gray-600 text-sm">Enter your details to receive a verification code</p>
                    @if ($errors->any())
                        <div class="mt-2 p-2 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                            {{ $errors->first() }}
                        </div>
                    @endif
                </div>

                <form action="{{ route('login.submit') }}" method="POST" class="space-y-5" id="verificationForm">
                    @csrf
                    <div>
                        <label for="login_type" class="block text-sm font-medium text-gray-700 mb-1">Login with</label>
                        <select name="login_type" id="login_type"
                            class="w-full pl-4 pr-8 py-2.5 border border-gray-300 rounded-xl text-sm bg-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                            <option value="mobile_no">Phone Number</option>
                            <option value="policy_number">Policy Number</option>
                            <option value="vehicle_number">Vehicle Number</option>
                        </select>
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
                        class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl shadow-sm transition duration-200">
                        <i class="fas fa-paper-plane text-sm"></i>
                        <span>Send Verification Code</span>
                    </button>
                </form>
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
         AUTH MODAL
         Stages: loading | success-genova | profile-picker |
                 otp-entry | no-profile | error
    ═══════════════════════════════════════════════════════ --}}
    <div id="authModal" class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(6px);">

        <div id="authModalCard"
            class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden border border-gray-100">

            <button id="modalCloseBtn"
                class="absolute top-3 right-3 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 transition z-10"
                aria-label="Close">
                <i class="fas fa-times text-sm"></i>
            </button>

            {{-- ── STAGE: LOADING ───────────────────────────── --}}
            <div id="stage-loading" class="modal-stage p-8 text-center">
                <div class="flex items-center justify-center mb-6">
                    <div class="relative w-20 h-20">
                        <svg class="orbit-ring absolute inset-0 w-full h-full" viewBox="0 0 80 80">
                            <circle cx="40" cy="40" r="36" fill="none" stroke="#dbeafe"
                                stroke-width="2" />
                            <circle cx="40" cy="4" r="4" fill="#2563eb" />
                        </svg>
                        <svg class="orbit-ring-slow absolute inset-0 w-full h-full" viewBox="0 0 80 80">
                            <circle cx="40" cy="40" r="26" fill="none" stroke="#eff6ff"
                                stroke-width="1.5" />
                            <circle cx="40" cy="14" r="3" fill="#93c5fd" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div
                                class="w-10 h-10 bg-brand-900 rounded-full flex items-center justify-center shadow-lg">
                                <i class="fas fa-shield-alt text-white text-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <h3 class="font-display text-xl text-gray-900 mb-2">Verifying your identity</h3>
                <p id="loaderMessage" class="text-sm text-gray-500 min-h-5">Connecting to Vanguard Assurance...</p>
                <div class="flex items-center justify-center gap-2 mt-5">
                    <div class="loader-dot"></div>
                    <div class="loader-dot"></div>
                    <div class="loader-dot"></div>
                </div>
                <p class="text-xs text-gray-400 mt-5">Please don't close this window</p>
            </div>

            {{-- ── STAGE: OTP SENT (brief interstitial) ────── --}}
            <div id="stage-success-genova" class="modal-stage">
                <div class="bg-brand-900 px-6 py-5 text-center">
                    <div class="flex items-center justify-center mb-3">
                        <div
                            class="w-14 h-14 bg-white/10 rounded-full flex items-center justify-center border border-white/20">
                            <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
                                <path class="check-path" d="M6 14l6 6 10-12" stroke="white" stroke-width="2.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="font-display text-xl text-white mb-1">Code sent!</h3>
                    <p id="genova-name" class="text-blue-200 text-sm"></p>
                </div>
                <div class="p-6 text-center">
                    <p class="text-sm text-gray-600 mb-5">We've sent a 6-digit verification code to your registered
                        phone number.</p>
                    <div
                        class="flex items-center gap-2 bg-brand-50 border border-brand-100 rounded-xl p-3 text-left mb-4">
                        <i class="fas fa-info-circle text-brand-600 text-sm shrink-0"></i>
                        <p class="text-xs text-brand-800">Taking you to the verification screen now...</p>
                    </div>
                    <div class="flex justify-center gap-2">
                        <div class="loader-dot"></div>
                        <div class="loader-dot mx-1"></div>
                        <div class="loader-dot"></div>
                    </div>
                </div>
            </div>

            {{-- ── STAGE: PROFILE PICKER ─────────────────────── --}}
            <div id="stage-profile-picker" class="modal-stage">
                <div class="bg-brand-900 px-6 py-5 text-center">
                    <div class="flex items-center justify-center mb-3">
                        <div
                            class="w-14 h-14 bg-white/10 rounded-full flex items-center justify-center border border-white/20">
                            <i class="fas fa-users text-white text-lg"></i>
                        </div>
                    </div>
                    <h3 class="font-display text-xl text-white mb-1">Select your profile</h3>
                    <p class="text-blue-200 text-xs">Multiple accounts found for this number</p>
                </div>
                <div class="p-6">
                    <div id="profile-list" class="space-y-3 max-h-72 overflow-y-auto"></div>
                </div>
            </div>

            {{-- ── STAGE: OTP ENTRY ──────────────────────────── --}}
            <div id="stage-otp-entry" class="modal-stage">
                <div class="bg-brand-900 px-6 py-5 text-center">
                    <div class="flex items-center justify-center mb-3">
                        <div
                            class="w-14 h-14 bg-white/10 rounded-full flex items-center justify-center border border-white/20">
                            <i class="fas fa-mobile-alt text-white text-xl"></i>
                        </div>
                    </div>
                    <h3 class="font-display text-xl text-white mb-1">Enter your code</h3>
                    <p id="otp-phone-display" class="text-blue-200 text-xs">Code sent to your registered number</p>
                </div>
                <div class="p-6">
                    <p id="otp-name-display" class="text-sm text-center text-gray-500 mb-5"></p>

                    <form id="otpForm" class="space-y-5">
                        <div>
                            <label
                                class="block text-xs font-medium text-gray-500 mb-3 text-center uppercase tracking-wide">6-digit
                                verification code</label>
                            <div class="flex justify-center gap-2">
                                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                                    class="otp-digit w-11 h-13 py-3 text-center text-xl font-bold border-2 border-gray-200 rounded-xl transition outline-none" />
                                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                                    class="otp-digit w-11 h-13 py-3 text-center text-xl font-bold border-2 border-gray-200 rounded-xl transition outline-none" />
                                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                                    class="otp-digit w-11 h-13 py-3 text-center text-xl font-bold border-2 border-gray-200 rounded-xl transition outline-none" />
                                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                                    class="otp-digit w-11 h-13 py-3 text-center text-xl font-bold border-2 border-gray-200 rounded-xl transition outline-none" />
                                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                                    class="otp-digit w-11 h-13 py-3 text-center text-xl font-bold border-2 border-gray-200 rounded-xl transition outline-none" />
                                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                                    class="otp-digit w-11 h-13 py-3 text-center text-xl font-bold border-2 border-gray-200 rounded-xl transition outline-none" />
                            </div>
                            <input type="hidden" id="otpValue" name="otp">
                        </div>

                        <div id="otpError"
                            class="hidden text-xs text-red-600 bg-red-50 border border-red-200 rounded-lg p-2 text-center">
                        </div>

                        <button type="submit" id="verifyOtpBtn"
                            class="w-full py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl transition">
                            <i class="fas fa-check-circle mr-2"></i>Verify Code
                        </button>
                    </form>

                    <div class="text-center mt-4 space-y-1">
                        <p class="text-xs text-gray-400">Didn't receive the code?</p>
                        <button id="resendOtpBtn" disabled
                            class="text-sm font-medium text-gray-400 transition disabled:cursor-not-allowed">
                            Resend code (<span id="resendCountdown">60</span>s)
                        </button>
                    </div>

                    <button type="button" id="backToLoginBtn"
                        class="w-full mt-3 py-2 text-xs text-gray-400 hover:text-gray-600 transition">
                        <i class="fas fa-arrow-left mr-1"></i>Use a different number
                    </button>
                </div>
            </div>

            {{-- ── STAGE: NO PROFILE ─────────────────────────── --}}
            <div id="stage-no-profile" class="modal-stage p-8 text-center">
                <div class="flex items-center justify-center mb-5">
                    <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-slash text-amber-500 text-2xl"></i>
                    </div>
                </div>
                <h3 class="font-display text-xl text-gray-900 mb-2">Account not found</h3>
                <p class="text-sm text-gray-500 mb-6 leading-relaxed">
                    Your details were verified but no account profile was found. Please contact support.
                </p>
                <button id="noProfileRetryBtn"
                    class="w-full py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl transition">
                    <i class="fas fa-redo mr-2"></i>Try Again
                </button>
            </div>

            {{-- ── STAGE: ERROR ──────────────────────────────── --}}
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
            </div>

        </div>
    </div>

    <script>
        (() => {
            const AJAX_URL = '{{ route('login.ajax') }}';
            const SELECT_URL = '{{ route('login.select.profile') }}';
            const VERIFY_OTP_URL = '{{ route('login.verify.otp') }}';
            const RESEND_OTP_URL = '{{ route('login.resend.otp') }}';
            const CSRF = document.querySelector('meta[name="csrf-token"]').content;

            const LOAD_MESSAGES = [
                'Connecting to Vanguard Assurance...',
                'Verifying your identity...',
                'Gathering your policies...',
                'Checking our records...',
                'Almost there...',
            ];

            let messageInterval = null;
            let messageIndex = 0;
            let resendTimer = null;
            const modal = document.getElementById('authModal');
            const form = document.getElementById('verificationForm');
            const loaderMessage = document.getElementById('loaderMessage');
            const errorMessage = document.getElementById('errorMessage');

            // ── Modal helpers ──────────────────────────────────────────────
            function openModal() {
                modal.style.display = 'flex';
                requestAnimationFrame(() => requestAnimationFrame(() => modal.classList.add('visible')));
            }

            function closeModal() {
                modal.classList.remove('visible');
                setTimeout(() => {
                    modal.style.display = 'none';
                    stopMessageCycle();
                }, 300);
            }

            function showStage(name) {
                document.querySelectorAll('.modal-stage').forEach(el => el.classList.remove('active'));
                document.getElementById('stage-' + name)?.classList.add('active');
            }

            function startMessageCycle() {
                messageIndex = 0;
                loaderMessage.textContent = LOAD_MESSAGES[0];
                messageInterval = setInterval(() => {
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

            function showError(msg) {
                errorMessage.textContent = msg;
                showStage('error');
            }

            // ── OTP stage ─────────────────────────────────────────────────
            function showOtpStage(name, phoneMasked) {
                // Brief "sent" confirmation (1.5s) then slide into OTP entry
                document.getElementById('genova-name').textContent = name;
                showStage('success-genova');

                setTimeout(() => {
                    document.getElementById('otp-name-display').textContent = `Welcome, ${name}`;
                    document.getElementById('otp-phone-display').textContent = `Code sent to ${phoneMasked}`;
                    clearOtpInputs();
                    showStage('otp-entry');
                    startResendCountdown();
                    otpDigits[0]?.focus();
                }, 1500);
            }

            // ── OTP digit inputs ──────────────────────────────────────────
            const otpDigits = document.querySelectorAll('.otp-digit');

            otpDigits.forEach((input, index) => {
                input.addEventListener('input', e => {
                    const val = e.target.value.replace(/\D/g, '');
                    e.target.value = val;
                    e.target.classList.toggle('filled', !!val);
                    if (val && index < otpDigits.length - 1) otpDigits[index + 1].focus();
                    updateOtpValue();
                });

                input.addEventListener('keydown', e => {
                    if (e.key === 'Backspace' && !e.target.value && index > 0) {
                        otpDigits[index - 1].value = '';
                        otpDigits[index - 1].classList.remove('filled');
                        otpDigits[index - 1].focus();
                        updateOtpValue();
                    }
                });

                input.addEventListener('paste', e => {
                    e.preventDefault();
                    const pasted = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
                    pasted.split('').forEach((char, i) => {
                        if (otpDigits[i]) {
                            otpDigits[i].value = char;
                            otpDigits[i].classList.add('filled');
                        }
                    });
                    updateOtpValue();
                    otpDigits[Math.min(pasted.length, 5)]?.focus();
                });
            });

            function updateOtpValue() {
                document.getElementById('otpValue').value =
                    Array.from(otpDigits).map(i => i.value).join('');
            }

            function clearOtpInputs() {
                otpDigits.forEach(i => {
                    i.value = '';
                    i.classList.remove('filled');
                });
                document.getElementById('otpValue').value = '';
                document.getElementById('otpError').classList.add('hidden');
            }

            // ── Resend countdown ──────────────────────────────────────────
            function startResendCountdown(seconds = 60) {
                const btn = document.getElementById('resendOtpBtn');
                if (resendTimer) clearInterval(resendTimer);

                let remaining = seconds;
                btn.disabled = true;
                btn.classList.remove('text-brand-600', 'hover:text-brand-700');
                btn.classList.add('text-gray-400');

                const updateBtn = () => {
                    btn.innerHTML = `Resend code (<span id="resendCountdown">${remaining}</span>s)`;
                };
                updateBtn();

                resendTimer = setInterval(() => {
                    remaining--;
                    updateBtn();
                    if (remaining <= 0) {
                        clearInterval(resendTimer);
                        btn.disabled = false;
                        btn.innerHTML = 'Resend code';
                        btn.classList.remove('text-gray-400');
                        btn.classList.add('text-brand-600', 'hover:text-brand-700');
                    }
                }, 1000);
            }

            // ── Profile picker ────────────────────────────────────────────
            function renderProfiles(profiles) {
                const list = document.getElementById('profile-list');
                list.innerHTML = '';
                profiles.forEach(profile => {
                    const card = document.createElement('button');
                    card.type = 'button';
                    card.className =
                        'w-full text-left p-4 border border-gray-200 rounded-xl hover:border-blue-400 hover:bg-blue-50 transition flex items-center gap-3';

                    const initials = profile.name.split(' ').slice(0, 2).map(w => w[0] ?? '').join('')
                        .toUpperCase();
                    const sourceBadge = profile.source === 'glims' || profile.source === 'glims_local' ?
                        '<span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full ml-1">GLIMS</span>' :
                        '<span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full ml-1">Genova</span>';
                    const policyText = profile.policy_count !== null ?
                        `${profile.policy_count} polic${profile.policy_count === 1 ? 'y' : 'ies'}` :
                        'Policies loading...';

                    card.innerHTML = `
                    <div class="w-10 h-10 rounded-full bg-brand-900 text-white flex items-center justify-center text-sm font-semibold shrink-0">${initials}</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center flex-wrap gap-1">
                            <p class="text-sm font-medium text-gray-900 truncate">${profile.name}</p>
                            ${sourceBadge}
                            ${profile.is_match ? '<span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Suggested</span>' : ''}
                        </div>
                        <p class="text-xs text-gray-500">${profile.code} · ${policyText}</p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 text-xs shrink-0"></i>
                `;
                    card.addEventListener('click', () => selectProfile(profile));
                    list.appendChild(card);
                });
            }

            async function selectProfile(profile) {
                showStage('loading');
                startMessageCycle();

                try {
                    const res = await fetch(SELECT_URL, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': CSRF,
                            'Accept': 'application/json'
                        },
                        body: new URLSearchParams({
                            customer_code: profile.code
                        }),
                    });
                    stopMessageCycle();
                    const data = await res.json();

                    if (data.status === 'otp_sent') {
                        showOtpStage(data.name, data.phone_masked);
                    } else {
                        showError(data.message ?? 'Something went wrong.');
                    }
                } catch {
                    stopMessageCycle();
                    showError('A network error occurred. Please try again.');
                }
            }

            // ── Main form submit ──────────────────────────────────────────
            form.addEventListener('submit', async e => {
                e.preventDefault();
                openModal();
                showStage('loading');
                startMessageCycle();

                try {
                    const res = await fetch(AJAX_URL, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': CSRF,
                            'Accept': 'application/json'
                        },
                        body: new FormData(form),
                    });
                    stopMessageCycle();
                    handleAuthResponse(await res.json());
                } catch {
                    stopMessageCycle();
                    showError('A network error occurred. Please check your connection and try again.');
                }
            });

            function handleAuthResponse(data) {
                switch (data.status) {
                    case 'otp_sent':
                        showOtpStage(data.name, data.phone_masked);
                        break;
                    case 'profile_selection':
                        renderProfiles(data.profiles);
                        showStage('profile-picker');
                        break;
                    case 'no_profile':
                        showStage('no-profile');
                        break;
                    case 'error':
                    default:
                        showError(data.message ?? 'Something went wrong. Please try again.');
                        break;
                }
            }

            // ── OTP form submit ───────────────────────────────────────────
            document.getElementById('otpForm')?.addEventListener('submit', async e => {
                e.preventDefault();
                const errorEl = document.getElementById('otpError');
                const submitBtn = document.getElementById('verifyOtpBtn');
                const otp = document.getElementById('otpValue').value;

                errorEl.classList.add('hidden');

                if (otp.length !== 6) {
                    errorEl.textContent = 'Please enter the complete 6-digit code.';
                    errorEl.classList.remove('hidden');
                    return;
                }

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Verifying...';

                try {
                    const res = await fetch(VERIFY_OTP_URL, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': CSRF,
                            'Accept': 'application/json'
                        },
                        body: new URLSearchParams({
                            otp
                        }),
                    });
                    const data = await res.json();

                    if (data.status === 'success') {
                        submitBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Verified!';
                        setTimeout(() => window.location.href = data.redirect, 800);
                    } else {
                        errorEl.textContent = data.message ?? 'Invalid code. Please try again.';
                        errorEl.classList.remove('hidden');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Verify Code';
                        clearOtpInputs();
                        otpDigits[0]?.focus();
                    }
                } catch {
                    errorEl.textContent = 'Network error. Please try again.';
                    errorEl.classList.remove('hidden');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Verify Code';
                }
            });

            // ── Resend button ─────────────────────────────────────────────
            document.getElementById('resendOtpBtn')?.addEventListener('click', async () => {
                const btn = document.getElementById('resendOtpBtn');
                const errorEl = document.getElementById('otpError');
                btn.disabled = true;

                try {
                    const res = await fetch(RESEND_OTP_URL, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': CSRF,
                            'Accept': 'application/json'
                        },
                    });
                    const data = await res.json();

                    if (data.status === 'success') {
                        clearOtpInputs();
                        otpDigits[0]?.focus();
                        startResendCountdown();
                    } else {
                        errorEl.textContent = data.message ?? 'Could not resend code.';
                        errorEl.classList.remove('hidden');
                        btn.disabled = false;
                    }
                } catch {
                    btn.disabled = false;
                }
            });

            // ── Utility buttons ───────────────────────────────────────────
            const UNCLOSABLE = ['loading', 'success-genova'];

            function canClose() {
                const active = document.querySelector('.modal-stage.active');
                return !active || !UNCLOSABLE.some(s => active.id === 'stage-' + s);
            }

            document.getElementById('modalCloseBtn')?.addEventListener('click', () => {
                if (canClose()) closeModal();
            });
            modal.addEventListener('click', e => {
                if (e.target === modal && canClose()) closeModal();
            });
            document.getElementById('errorRetryBtn')?.addEventListener('click', closeModal);
            document.getElementById('noProfileRetryBtn')?.addEventListener('click', closeModal);
            document.getElementById('backToLoginBtn')?.addEventListener('click', closeModal);

        })();
    </script>
</body>

</html>
