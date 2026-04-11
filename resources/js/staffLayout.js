(function () {
    // Sidebar elements
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebarOverlay");
    const mobileMenuBtn = document.getElementById("mobileMenuBtn");

    // User dropdown elements
    const userMenuBtn = document.getElementById("userMenuBtn");
    const userDropdown = document.getElementById("userDropdown");
    const dropdownIcon = document.getElementById("dropdownIcon");

    // Toggle sidebar on mobile
    function openSidebar() {
        sidebar.classList.remove("-translate-x-full");
        sidebar.classList.add("translate-x-0");
        overlay.classList.remove("hidden");
        document.body.classList.add("no-scroll");
    }

    function closeSidebar() {
        sidebar.classList.add("-translate-x-full");
        sidebar.classList.remove("translate-x-0");
        overlay.classList.add("hidden");
        document.body.classList.remove("no-scroll");
    }

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            if (sidebar.classList.contains("-translate-x-full")) {
                openSidebar();
            } else {
                closeSidebar();
            }
        });
    }

    if (overlay) {
        overlay.addEventListener("click", closeSidebar);
    }

    // Close sidebar on window resize if open and becomes desktop (optional: but not needed)
    window.addEventListener("resize", function () {
        if (window.innerWidth >= 768) {
            // On desktop, ensure sidebar is visible and overlay hidden
            sidebar.classList.remove("-translate-x-full");
            sidebar.classList.add("translate-x-0");
            overlay.classList.add("hidden");
            document.body.classList.remove("no-scroll");
        } else {
            // On mobile, if sidebar is open due to desktop? keep state? but if user resizes, we reset to closed for better UX
            if (
                !sidebar.classList.contains("-translate-x-full") &&
                window.innerWidth < 768
            ) {
                // if open on mobile after resize, keep open? Actually we reset to closed for consistency
                // but we won't force close because it might be intentional. Let's only adjust if it was forced by desktop
            }
        }
    });

    // Initialize sidebar state: on desktop it's visible, on mobile hidden
    if (window.innerWidth < 768) {
        sidebar.classList.add("-translate-x-full");
        sidebar.classList.remove("translate-x-0");
    } else {
        sidebar.classList.remove("-translate-x-full");
        sidebar.classList.add("translate-x-0");
        overlay.classList.add("hidden");
    }

    // USER DROPDOWN toggle
    if (userMenuBtn && userDropdown) {
        userMenuBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            const isHidden = userDropdown.classList.contains("hidden");
            if (isHidden) {
                userDropdown.classList.remove("hidden");
                dropdownIcon.style.transform = "rotate(180deg)";
            } else {
                userDropdown.classList.add("hidden");
                dropdownIcon.style.transform = "rotate(0deg)";
            }
        });

        // Close dropdown if clicking outside
        document.addEventListener("click", function (event) {
            if (
                !userMenuBtn.contains(event.target) &&
                !userDropdown.contains(event.target)
            ) {
                userDropdown.classList.add("hidden");
                dropdownIcon.style.transform = "rotate(0deg)";
            }
        });
    }

    // Optional: close dropdown on escape key
    document.addEventListener("keydown", function (e) {
        if (
            e.key === "Escape" &&
            userDropdown &&
            !userDropdown.classList.contains("hidden")
        ) {
            userDropdown.classList.add("hidden");
            dropdownIcon.style.transform = "rotate(0deg)";
        }
        if (
            e.key === "Escape" &&
            sidebar &&
            !sidebar.classList.contains("-translate-x-full") &&
            window.innerWidth < 768
        ) {
            closeSidebar();
        }
    });
})();
