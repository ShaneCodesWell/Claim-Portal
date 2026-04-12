<x-layouts.staff>
    <!-- Settings Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-sliders-h text-indigo-500 text-2xl"></i>
            Preferences & Configuration
        </h2>
        <p class="text-gray-500 text-sm mt-1">
            Manage your account, team permissions, notification rules, and
            security settings.
        </p>
    </div>

    <!-- Settings Tabs (FUNCTIONAL) -->
    <div class="flex flex-wrap gap-1 border-b border-gray-200 mb-6">
        <button data-tab="profile"
            class="settings-tab px-5 py-2.5 text-sm font-medium text-indigo-600 border-b-2 border-indigo-600">
            Profile
        </button>
        <button data-tab="notifications"
            class="settings-tab px-5 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-700">
            Notifications
        </button>
        <button data-tab="security"
            class="settings-tab px-5 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-700">
            Security
        </button>
        <button data-tab="team" class="settings-tab px-5 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-700">
            Team & Roles
        </button>
        <button data-tab="integrations"
            class="settings-tab px-5 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-700">
            Integrations
        </button>
    </div>

    <!-- Profile Settings Section -->
    <div id="section-profile" class="settings-section">
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="font-semibold text-gray-800">
                    <i class="fas fa-user-circle text-indigo-500 mr-2"></i>
                    Administrator Profile
                </h3>
            </div>
            <div class="p-6 space-y-5">
                <div class="flex flex-col sm:flex-row gap-6">
                    <div class="shrink-0 text-center">
                        <div class="w-24 h-24 bg-indigo-100 rounded-full flex items-center justify-center mx-auto">
                            <i class="fas fa-user-shield text-indigo-600 text-4xl"></i>
                        </div>
                        <button class="mt-2 text-xs text-indigo-600 hover:underline">
                            Change avatar
                        </button>
                    </div>
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label><input
                                type="text" value="Moses Vanguard"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-800" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label><input
                                type="email" value="moses.vanguard@claimpulse.com"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label><input
                                type="text" value="Claims Administrator (Super Admin)"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Department</label><input
                                type="text" value="Claims Management"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" />
                        </div>
                    </div>
                </div>
                <div class="pt-4 flex justify-end gap-3">
                    <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Preferences -->
    <div id="section-notifications" class="settings-section hidden">
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="font-semibold text-gray-800">
                    <i class="fas fa-bell text-indigo-500 mr-2"></i> Notification
                    Settings
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <div>
                        <p class="font-medium text-gray-800">
                            New claim submission alerts
                        </p>
                        <p class="text-xs text-gray-500">
                            Get notified when a customer files a new claim
                        </p>
                    </div>
                    <label class="toggle-switch"><input type="checkbox" checked /><span class="slider"></span></label>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <div>
                        <p class="font-medium text-gray-800">
                            Document upload notifications
                        </p>
                        <p class="text-xs text-gray-500">
                            Alert when additional documents are attached to claims
                        </p>
                    </div>
                    <label class="toggle-switch"><input type="checkbox" checked /><span class="slider"></span></label>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <div>
                        <p class="font-medium text-gray-800">
                            Daily claims summary email
                        </p>
                        <p class="text-xs text-gray-500">
                            Receive a digest of all claims activity each morning
                        </p>
                    </div>
                    <label class="toggle-switch"><input type="checkbox" /><span class="slider"></span></label>
                </div>
                <div class="flex items-center justify-between py-2">
                    <div>
                        <p class="font-medium text-gray-800">
                            System & security alerts
                        </p>
                        <p class="text-xs text-gray-500">
                            Critical platform and access notifications
                        </p>
                    </div>
                    <label class="toggle-switch"><input type="checkbox" checked /><span class="slider"></span></label>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t flex justify-end">
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">
                    Update Preferences
                </button>
            </div>
        </div>
    </div>

    <!-- Security & Access -->
    <div id="section-security" class="settings-section hidden">
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="font-semibold text-gray-800">
                    <i class="fas fa-lock text-indigo-500 mr-2"></i> Security &
                    Authentication
                </h3>
            </div>
            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label><input
                            type="password" placeholder="••••••••"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label><input
                            type="password" placeholder="••••••••"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label><input
                            type="password" placeholder="••••••••"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg" />
                    </div>
                </div>
                <div class="flex items-center justify-between py-3 border-t border-gray-100 mt-2">
                    <div>
                        <p class="font-medium text-gray-800">
                            Two-Factor Authentication (2FA)
                        </p>
                        <p class="text-xs text-gray-500">
                            Add an extra layer of security to your account
                        </p>
                    </div>
                    <button
                        class="px-3 py-1.5 border border-indigo-300 text-indigo-600 rounded-lg text-sm hover:bg-indigo-50">
                        Enable 2FA
                    </button>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-800">Session Management</p>
                        <p class="text-xs text-gray-500">
                            Active sessions: 2 (Current + Mobile)
                        </p>
                    </div>
                    <button class="text-red-500 text-sm hover:underline">
                        Revoke all other sessions
                    </button>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t flex justify-end gap-3">
                <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
                    Cancel
                </button>
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">
                    Update Password
                </button>
            </div>
        </div>
    </div>

    <!-- Team & Roles (Admin permissions) -->
    <div id="section-team" class="settings-section hidden">
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">
                    <i class="fas fa-users-cog text-indigo-500 mr-2"></i> Team
                    Members & Roles
                </h3>
                <button class="text-indigo-600 text-sm flex items-center gap-1">
                    <i class="fas fa-plus-circle"></i> Invite Member
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs text-gray-500">
                                Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs text-gray-500">
                                Role
                            </th>
                            <th class="px-6 py-3 text-left text-xs text-gray-500">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs text-gray-500">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr>
                            <td class="px-6 py-3 text-sm">Moses Vanguard</td>
                            <td class="px-6 py-3 text-sm">Super Admin</td>
                            <td class="px-6 py-3">
                                <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">Active</span>
                            </td>
                            <td class="px-6 py-3">
                                <button class="text-gray-400">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 text-sm">Shane Miller</td>
                            <td class="px-6 py-3 text-sm">Claims Adjuster</td>
                            <td class="px-6 py-3">
                                <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">Active</span>
                            </td>
                            <td class="px-6 py-3">
                                <button class="text-gray-400">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 text-sm">Lisa Crawford</td>
                            <td class="px-6 py-3 text-sm">Reviewer</td>
                            <td class="px-6 py-3">
                                <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-1 rounded-full">Pending
                                    invite</span>
                            </td>
                            <td class="px-6 py-3">
                                <button class="text-gray-400">
                                    <i class="fas fa-envelope"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t text-right">
                <button class="text-indigo-600 text-sm">
                    Manage roles & permissions →
                </button>
            </div>
        </div>
    </div>

    <!-- Integrations -->
    <div id="section-integrations" class="settings-section hidden">
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="font-semibold text-gray-800">
                    <i class="fas fa-plug text-indigo-500 mr-2"></i> Connected
                    Services
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fab fa-slack text-blue-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="font-medium">Slack Notifications</p>
                            <p class="text-xs text-gray-500">
                                Send claim alerts to #claims channel
                            </p>
                        </div>
                    </div>
                    <button class="px-3 py-1 border border-gray-300 rounded-lg text-sm">
                        Connect
                    </button>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-cloud-upload-alt text-green-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="font-medium">AWS S3 Backup</p>
                            <p class="text-xs text-gray-500">
                                Automated document backup
                            </p>
                        </div>
                    </div>
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">Configured</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-envelope-open-text text-purple-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="font-medium">Email Digest</p>
                            <p class="text-xs text-gray-500">
                                Daily summary to admin@claimpulse.com
                            </p>
                        </div>
                    </div>
                    <button class="text-indigo-600 text-sm">Edit settings</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Danger zone (visible on all tabs) -->
    <div class="mt-8 border border-red-200 rounded-xl bg-red-50/30 p-5">
        <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
            <div>
                <h4 class="font-semibold text-red-700 flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i> Danger Zone
                </h4>
                <p class="text-sm text-red-600">
                    Permanently delete your account and all associated data. This
                    action cannot be undone.
                </p>
            </div>
            <button class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700">
                Delete Account
            </button>
        </div>
    </div>
    <script>
        (function() {
            // ---------- FUNCTIONAL SETTINGS TABS ----------
            const tabs = document.querySelectorAll(".settings-tab");
            const sections = {
                profile: document.getElementById("section-profile"),
                notifications: document.getElementById("section-notifications"),
                security: document.getElementById("section-security"),
                team: document.getElementById("section-team"),
                integrations: document.getElementById("section-integrations"),
            };

            function activateTab(tabId) {
                // Hide all sections
                Object.values(sections).forEach((section) => {
                    if (section) section.classList.add("hidden");
                });
                // Show selected section
                if (sections[tabId]) sections[tabId].classList.remove("hidden");
                // Update tab button styles
                tabs.forEach((tab) => {
                    const btnTabId = tab.getAttribute("data-tab");
                    if (btnTabId === tabId) {
                        tab.classList.remove("text-gray-500", "hover:text-gray-700");
                        tab.classList.add(
                            "text-indigo-600",
                            "border-b-2",
                            "border-indigo-600",
                        );
                    } else {
                        tab.classList.remove(
                            "text-indigo-600",
                            "border-b-2",
                            "border-indigo-600",
                        );
                        tab.classList.add("text-gray-500", "hover:text-gray-700");
                    }
                });
            }

            // Add click listeners to each tab
            tabs.forEach((tab) => {
                tab.addEventListener("click", (e) => {
                    const tabId = tab.getAttribute("data-tab");
                    if (tabId) activateTab(tabId);
                });
            });

            // Ensure Profile is active by default (in case of any mismatch)
            activateTab("profile");
        })();
    </script>
</x-layouts.staff>
