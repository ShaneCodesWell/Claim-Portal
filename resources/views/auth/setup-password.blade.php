<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Set Up Password | Vanguard Assurance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div class="text-left">
                    <h1 class="text-lg sm:text-xl font-bold text-white leading-tight">Set Up Password</h1>
                    <p class="text-blue-200 text-xs sm:text-sm leading-tight">Vanguard Assurance</p>
                </div>
            </div>
        </div>

        <div class="p-4 sm:p-5">

            {{-- Info box --}}
            <div class="mb-4 flex items-start gap-2 p-3 bg-blue-50 border border-blue-100 rounded-lg">
                <svg class="h-4 w-4 text-brand-600 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-xs text-blue-700">
                    This password lets you log in to the portal even when the verification service is unavailable.
                </p>
            </div>

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="text-red-600 text-sm mb-4 p-3 bg-red-50 rounded-lg border border-red-100">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('password.setup.submit') }}" method="POST" id="setupForm" class="space-y-4">
                @csrf

                {{-- New Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Choose a password</label>
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
                            placeholder="Min. 8 characters"
                            autocomplete="new-password"
                            class="block w-full pl-9 pr-10 py-2 text-sm border border-slate-300 rounded-lg bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-1 focus:ring-brand-500 focus:border-brand-500 transition duration-150 ease-in-out @error('password') border-red-400 bg-red-50 @enderror"
                            oninput="checkStrength(this.value)"
                            required
                        />
                        <button type="button" onclick="toggleVisibility('password')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    {{-- Strength bar --}}
                    <div class="mt-2 h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                        <div id="strengthBar" class="h-full rounded-full transition-all duration-300" style="width:0%"></div>
                    </div>
                    <p id="strengthLabel" class="mt-1 text-xs text-slate-400">Enter a password</p>
                </div>

                {{-- Password rules --}}
                <ul class="space-y-1 text-xs">
                    <li id="rule-length" class="flex items-center gap-1.5 text-slate-400 transition-colors duration-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-current shrink-0"></span> At least 8 characters
                    </li>
                    <li id="rule-letter" class="flex items-center gap-1.5 text-slate-400 transition-colors duration-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-current shrink-0"></span> Contains a letter
                    </li>
                    <li id="rule-number" class="flex items-center gap-1.5 text-slate-400 transition-colors duration-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-current shrink-0"></span> Contains a number
                    </li>
                </ul>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Confirm password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            placeholder="Re-enter your password"
                            autocomplete="new-password"
                            class="block w-full pl-9 pr-10 py-2 text-sm border border-slate-300 rounded-lg bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-1 focus:ring-brand-500 focus:border-brand-500 transition duration-150 ease-in-out"
                            required
                        />
                        <button type="button" onclick="toggleVisibility('password_confirmation')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit" id="submitBtn" disabled
                    class="w-full flex items-center justify-center py-2.5 px-4 border border-transparent rounded-lg text-sm font-semibold text-white bg-brand-600 hover:bg-brand-800 disabled:bg-slate-300 disabled:cursor-not-allowed focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-brand-500 transition-colors duration-200 mt-2">
                    <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Save Password
                </button>
            </form>

            {{-- Skip link --}}
            <div class="mt-4 text-center">
                <a href="{{ route('dashboard') }}" class="text-xs text-slate-400 hover:text-slate-600 transition-colors">
                    Skip for now — remind me later
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
        function toggleVisibility(fieldId) {
            const input = document.getElementById(fieldId);
            input.type = input.type === 'password' ? 'text' : 'password';
        }

        function checkStrength(value) {
            const hasLength = value.length >= 8;
            const hasLetter = /[a-zA-Z]/.test(value);
            const hasNumber = /[0-9]/.test(value);

            setRule('rule-length', hasLength);
            setRule('rule-letter', hasLetter);
            setRule('rule-number', hasNumber);

            const score = [hasLength, hasLetter, hasNumber].filter(Boolean).length;

            const bar   = document.getElementById('strengthBar');
            const label = document.getElementById('strengthLabel');
            const btn   = document.getElementById('submitBtn');

            const levels = [
                { pct: '0%',   color: '',          text: 'Enter a password' },
                { pct: '33%',  color: '#ef4444',   text: 'Weak' },
                { pct: '66%',  color: '#f59e0b',   text: 'Almost there…' },
                { pct: '100%', color: '#22c55e',   text: 'Strong' },
            ];

            bar.style.width      = levels[score].pct;
            bar.style.background = levels[score].color;
            label.textContent    = levels[score].text;
            label.style.color    = score === 3 ? '#16a34a' : score === 2 ? '#d97706' : score === 1 ? '#ef4444' : '#94a3b8';
            btn.disabled         = score < 3;
        }

        function setRule(id, pass) {
            const el = document.getElementById(id);
            el.style.color = pass ? '#16a34a' : '#94a3b8';
        }

        // Loading state on submit
        document.getElementById('setupForm').addEventListener('submit', function () {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = `
                <svg class="animate-spin h-4 w-4 text-white mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Saving...
            `;
        });
    </script>
</body>
</html>