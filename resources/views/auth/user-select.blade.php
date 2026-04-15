<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <title>Choose Your Role | Vanguard Assurance</title>
    <!-- Tailwind CSS v3 + Font Awesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        body {
            background-image: url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .role-card {
            transition: all 0.25s ease;
        }

        .role-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 30px -12px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>

<body class="font-sans antialiased min-h-screen flex items-center justify-center p-5 relative">
    <!-- Dark overlay for background image -->
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-[2px]"></div>

    <!-- Top Navigation Bar (with Staff Login button) -->
    <nav class="absolute top-0 left-0 w-full p-6 z-20 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="bg-white/10 p-2.5 rounded-xl border border-white/20 backdrop-blur-sm">
                <img src="{{ asset('images/Vanguard.png') }}" alt="Vanguard Assurance Logo"
                    class="h-10 w-auto object-contain" />
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

    <div class="max-w-5xl w-full mx-auto relative z-10">
        <!-- Header / Brand -->
        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-4xl font-bold text-white drop-shadow-md">
                Welcome to the Claims Portal
            </h1>
            <p class="text-gray-200 mt-2 max-w-md mx-auto">
                Please select your account type to continue
            </p>
        </div>

        <!-- Role Selection Cards (unchanged) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Customer Card -->
            <a href="{{ route('login') }}">
                <div
                    class="role-card bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden cursor-pointer group">
                    <div class="p-8 text-center">
                        <div
                            class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-5 group-hover:bg-indigo-100 transition">
                            <i class="fas fa-user-circle text-indigo-600 text-4xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Customer</h2>
                        <p class="text-gray-500 text-sm mb-6">
                            Access your policies, file claims, track status, and manage your insurance portfolio.
                        </p>
                        <div
                            class="inline-flex items-center gap-2 text-indigo-600 font-medium text-sm border border-indigo-200 px-4 py-2 rounded-full group-hover:bg-indigo-50 transition">
                            <span>Continue as Customer</span>
                            <i class="fas fa-arrow-right text-xs"></i>
                        </div>
                    </div>
                    <div
                        class="bg-gray-50 px-6 py-3 border-t border-gray-100 text-xs text-gray-500 flex justify-center gap-4">
                        <span><i class="fas fa-file-alt mr-1"></i> File claims</span>
                        <span><i class="fas fa-chart-line mr-1"></i> Track coverage</span>
                    </div>
                </div>
            </a>


            <!-- Intermediary Card -->
            <a href="{{ route('agent.login') }}">
                <div
                    class="role-card bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden cursor-pointer group">
                    <div class="p-8 text-center">
                        <div
                            class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-5 group-hover:bg-emerald-100 transition">
                            <i class="fas fa-handshake text-emerald-600 text-4xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Intermediary</h2>
                        <p class="text-gray-500 text-sm mb-6">
                            Broker, agent, or partner – manage client policies, submit claims on behalf of customers,
                            and
                            access reports.
                        </p>
                        <div
                            class="inline-flex items-center gap-2 text-emerald-600 font-medium text-sm border border-emerald-200 px-4 py-2 rounded-full group-hover:bg-emerald-50 transition">
                            <span>Continue as Intermediary</span>
                            <i class="fas fa-arrow-right text-xs"></i>
                        </div>
                    </div>
                    <div
                        class="bg-gray-50 px-6 py-3 border-t border-gray-100 text-xs text-gray-500 flex justify-center gap-4">
                        <span><i class="fas fa-users mr-1"></i> Client management</span>
                        <span><i class="fas fa-chart-simple mr-1"></i> Analytics</span>
                    </div>
                </div>
            </a>
        </div>

        <!-- Footer note -->
        <div class="text-center mt-12 text-gray-300 text-xs">
            <p>© 2026 NissiTechnologies. Secure role-based access.</p>
        </div>
    </div>
</body>

</html>
