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
                <!-- <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6 text-white"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2"
          > -->
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <span class="text-white font-bold text-xl tracking-tight drop-shadow-md">Claims Portal</span>
        </div>

        <a href="#" class="text-white/80 hover:text-white text-sm font-medium transition-colors">Contact
            Support</a>
    </nav>

    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-100 relative z-10">
        <div class="bg-brand-900 p-8 text-center relative">
            <div class="absolute top-0 left-0 w-full h-1 bg-linear-to-r from-blue-400 to-brand-600"></div>

            <div
                class="mx-auto bg-white/10 w-16 h-16 rounded-full flex items-center justify-center mb-4 backdrop-blur-sm border border-white/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-white mb-1">
                Identity Verification
            </h1>
            <p class="text-blue-200 text-sm">SecureGuard Claim Processing</p>
        </div>

        <div class="p-8">
            <div class="mb-6 text-center">
                <p class="text-slate-600">
                    To process your claim securely, please verify the phone number
                    associated with your policy.
                </p>
            </div>

            <form id="verificationForm" class="space-y-6">
                <div>
                    <label for="phone" class="block text-sm font-medium text-slate-700 mb-2">Mobile Number</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path
                                    d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                            </svg>
                        </div>
                        <input type="tel" id="phone" name="phone" placeholder="(233) 000-0000"
                            class="block w-full pl-10 pr-3 py-3 border border-slate-300 rounded-lg leading-5 bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition duration-150 ease-in-out sm:text-sm"
                            required />
                    </div>
                </div>

                <button type="submit"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-brand-600 hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-colors duration-200">
                    Verify & Process Claim
                </button>
            </form>

            <div class="mt-6 flex items-center justify-center">
                <div class="text-sm">
                    <a href="#" class="font-medium text-brand-600 hover:text-brand-500">
                        Need help accessing your account?
                    </a>
                </div>
            </div>
        </div>

        <div
            class="bg-slate-50 px-8 py-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
            <span>Encrypted 256-bit SSL</span>
            <div class="flex space-x-2">
                <span>Privacy</span>
                <span>•</span>
                <span>Terms</span>
            </div>
        </div>
    </div>

    <script>
        document
            .getElementById("verificationForm")
            .addEventListener("submit", function(e) {
                e.preventDefault(); // Stop page reload

                const phoneNumber = document.getElementById("phone").value;

                // Simple validation check
                if (phoneNumber.length < 5) {
                    Swal.fire({
                        icon: "error",
                        title: "Invalid Number",
                        text: "Please enter a valid phone number.",
                        confirmButtonColor: "#1e3a8a",
                    });
                    return;
                }

                // Simulate processing delay for realism
                let timerInterval;
                Swal.fire({
                    title: "Verifying Identity",
                    html: "Checking policy records...",
                    timer: 1500,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                }).then((result) => {
                    // Show Success Alert after "loading" finishes
                    Swal.fire({
                        icon: "success",
                        title: "Verification Successful",
                        text: "Your phone number has been verified. We are now processing your claim.",
                        confirmButtonText: "Continue to Dashboard",
                        confirmButtonColor: "#1e3a8a", // Matches the brand-900 color
                        background: "#fff",
                        iconColor: "#10b981", // Emerald green for success
                        showClass: {
                            popup: "animate__animated animate__fadeInDown",
                        },
                        hideClass: {
                            popup: "animate__animated animate__fadeOutUp",
                        },
                    }).then(() => {
                        window.location.href = "{{ route('dashboard') }}";
                    });
                });
            });
    </script>
</body>

</html>
