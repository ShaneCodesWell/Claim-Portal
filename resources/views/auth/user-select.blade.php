<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <title>Choose Your Role | Vanguard Assurance</title>
    <!-- Tailwind CSS v3 + Font Awesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="icon" type="image/png" href="{{ asset('images/favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}" />
    <link rel="manifest" href="{{ asset('images/site.webmanifest') }}" />
    <style>
        /* ── Fixed Background (stays put while content scrolls) ── */
        .bg-fixed-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 0;
            background-image: url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .bg-overlay {
            position: absolute;
            inset: 0;
            background-color: rgba(15, 23, 42, 0.60);
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
        }

        /* ── Body: scrollable content sits above fixed bg ── */
        body {
            min-height: 100vh;
            min-height: 100dvh;
            /* dynamic viewport height for mobile */
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            position: relative;
            z-index: 1;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        }

        /* ── Role Cards ── */
        .role-card {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .role-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 30px -12px rgba(0, 0, 0, 0.20);
        }

        /* ── Responsive tweaks ── */

        /* Small phones (320px - 480px) */
        @media (max-width: 480px) {
            body {
                padding: 0.75rem;
            }

            .nav-container {
                padding: 1rem 0.75rem !important;
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .nav-brand img {
                height: 32px !important;
            }

            .nav-links {
                flex-wrap: wrap;
                justify-content: flex-end;
                gap: 0.25rem 0.75rem;
                font-size: 0.75rem;
            }

            .nav-links a {
                padding: 0.25rem 0;
            }

            .nav-links .divider {
                display: none;
            }

            .hero-title {
                font-size: 1.5rem !important;
                line-height: 1.3;
            }

            .hero-sub {
                font-size: 0.875rem;
                padding: 0 0.5rem;
            }

            .role-card .p-8 {
                padding: 1.5rem 1rem !important;
            }

            .role-card .w-20 {
                width: 56px !important;
                height: 56px !important;
            }

            .role-card .w-20 i {
                font-size: 1.75rem !important;
            }

            .role-card h2 {
                font-size: 1.25rem !important;
            }

            .role-card p.text-sm {
                font-size: 0.8rem !important;
            }

            .role-card .inline-flex {
                font-size: 0.75rem !important;
                padding: 0.4rem 1rem !important;
            }

            .role-footer {
                padding: 0.6rem 0.75rem !important;
                font-size: 0.65rem !important;
                flex-wrap: wrap;
                gap: 0.25rem 0.75rem;
            }

            .footer-note {
                font-size: 0.65rem !important;
                margin-top: 2rem !important;
                padding: 0 0.5rem;
            }

            .grid-cards {
                gap: 1.25rem !important;
            }
        }

        /* Tablets (481px - 1024px) */
        @media (min-width: 481px) and (max-width: 1024px) {
            body {
                padding: 1.5rem;
            }

            .nav-container {
                padding: 1.25rem 1.5rem !important;
            }

            .hero-title {
                font-size: 2.25rem !important;
            }

            .grid-cards {
                gap: 2rem !important;
            }

            .role-card .p-8 {
                padding: 2rem 1.75rem !important;
            }
        }

        /* Large desktop (1200px+) */
        @media (min-width: 1200px) {
            .grid-cards {
                gap: 2.5rem !important;
            }
        }

        /* ── Utilities ── */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Ensure content wrapper takes full height on short screens */
        .content-wrapper {
            width: 100%;
            max-width: 80rem;
            margin: 0 auto;
            padding: 5rem 0 2rem;
            /* top space for fixed nav */
        }

        @media (max-width: 640px) {
            .content-wrapper {
                padding: 4.5rem 0 1.5rem;
            }
        }
    </style>
</head>

<body>

    <!-- ═══ FIXED BACKGROUND (stays put) ═══ -->
    <div class="bg-fixed-container" aria-hidden="true">
        <div class="bg-overlay"></div>
    </div>

    <!-- ═══ TOP NAVIGATION ═══ -->
    <nav class="fixed top-0 left-0 w-full z-20 nav-container" style="padding: 1.25rem 2rem;">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-3">

            <!-- Brand -->
            <a href="/">
                <div class="flex items-center gap-3 shrink-0">
                    <div class="bg-white/10 p-2 rounded-xl border border-white/20 backdrop-blur-sm nav-brand">
                        <img src="{{ asset('images/Vanguard.png') }}" alt="Vanguard Assurance Logo"
                            class="h-10 w-auto object-contain" style="height: 40px;" />
                    </div>
                </div>
            </a>

            <!-- Nav Links -->
            <div class="nav-links flex items-center gap-2 sm:gap-4 text-sm font-medium">
                <a href="#"
                    class="text-white/80 hover:text-white transition-colors whitespace-nowrap text-xs sm:text-sm">
                    <i class="fas fa-headset mr-1"></i>Support
                </a>
                <span class="text-white/20 hidden xs:inline">|</span>
                <a href="{{ route('staff.login') }}"
                    class="text-white/80 hover:text-white transition-colors whitespace-nowrap text-xs sm:text-sm">
                    <i class="fas fa-user-lock mr-1"></i>Staff Login
                </a>
            </div>
        </div>
    </nav>

    <!-- ═══ MAIN CONTENT ═══ -->
    <div class="content-wrapper relative z-10 px-3 sm:px-5">

        <!-- Header -->
        <div class="text-center mb-8 sm:mb-10">
            <h1 class="hero-title text-2xl sm:text-3xl md:text-4xl font-bold text-white drop-shadow-md">
                Welcome to the Claims Portal
            </h1>
            <p class="hero-sub text-gray-200 mt-2 max-w-md mx-auto text-sm sm:text-base">
                Please select your account type to continue
            </p>
        </div>

        <!-- Role Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-6 md:gap-8 max-w-5xl mx-auto grid-cards">

            <!-- ─── Customer Card ─── -->
            <a href="{{ route('login') }}" class="block no-underline">
                <div
                    class="role-card bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden h-full flex flex-col">
                    <div class="p-6 sm:p-7 md:p-8 text-center flex-1 flex flex-col items-center">
                        <div
                            class="w-16 sm:w-20 h-16 sm:h-20 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-5 group-hover:bg-indigo-100 transition">
                            <i class="fas fa-user-circle text-indigo-600 text-3xl sm:text-4xl"></i>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-1.5 sm:mb-2">Customer</h2>
                        <p class="text-gray-500 text-xs sm:text-sm mb-4 sm:mb-6 flex-1">
                            Access your policies, file claims, track status, and manage your insurance portfolio.
                        </p>
                        <div
                            class="inline-flex items-center gap-2 text-indigo-600 font-medium text-xs sm:text-sm border border-indigo-200 px-3 sm:px-4 py-1.5 sm:py-2 rounded-full group-hover:bg-indigo-50 transition">
                            <span>Continue as Customer</span>
                            <i class="fas fa-arrow-right text-[10px] sm:text-xs"></i>
                        </div>
                    </div>
                    <div
                        class="role-footer bg-gray-50 px-4 sm:px-6 py-2.5 sm:py-3 border-t border-gray-100 text-[10px] sm:text-xs text-gray-500 flex justify-center gap-3 sm:gap-4 flex-wrap">
                        <span><i class="fas fa-file-alt mr-1"></i> File claims</span>
                        {{-- <span><i class="fas fa-shield-alt mr-1"></i> Policy access</span> --}}
                    </div>
                </div>
            </a>

            <!-- ─── Intermediary Card ─── -->
            <a href="{{ route('agent.login') }}" class="block no-underline">
                <div
                    class="role-card bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden h-full flex flex-col">
                    <div class="p-6 sm:p-7 md:p-8 text-center flex-1 flex flex-col items-center">
                        <div
                            class="w-16 sm:w-20 h-16 sm:h-20 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-5 group-hover:bg-emerald-100 transition">
                            <i class="fas fa-handshake text-emerald-600 text-3xl sm:text-4xl"></i>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-1.5 sm:mb-2">Intermediary</h2>
                        <p class="text-gray-500 text-xs sm:text-sm mb-4 sm:mb-6 flex-1">
                            Broker, agent, or partner — manage client policies, submit claims on behalf of customers.
                        </p>
                        <div
                            class="inline-flex items-center gap-2 text-emerald-600 font-medium text-xs sm:text-sm border border-emerald-200 px-3 sm:px-4 py-1.5 sm:py-2 rounded-full group-hover:bg-emerald-50 transition">
                            <span>Continue as Intermediary</span>
                            <i class="fas fa-arrow-right text-[10px] sm:text-xs"></i>
                        </div>
                    </div>
                    <div
                        class="role-footer bg-gray-50 px-4 sm:px-6 py-2.5 sm:py-3 border-t border-gray-100 text-[10px] sm:text-xs text-gray-500 flex justify-center gap-3 sm:gap-4 flex-wrap">
                        <span><i class="fas fa-users mr-1"></i> Client management</span>
                        {{-- <span><i class="fas fa-chart-simple mr-1"></i> Portfolio view</span>/ --}}
                    </div>
                </div>
            </a>
        </div>

        <!-- Footer -->
        <div class="footer-note text-center mt-8 sm:mt-10 md:mt-12 text-gray-300 text-[10px] sm:text-xs">
            <p>© 2026 {{ $company->name ?? 'Vanguard Assurance' }}. Secure role-based access.</p>
        </div>
    </div>
</body>

</html>
