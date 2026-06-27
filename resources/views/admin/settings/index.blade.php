<x-layouts.staff>
    <!-- Settings Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-sliders-h text-blue-500 text-2xl"></i>
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
            class="settings-tab px-5 py-2.5 text-sm font-medium text-blue-600 border-b-2 border-blue-600">
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
                    <i class="fas fa-user-circle text-blue-500 mr-2"></i>
                    Administrator Profile
                </h3>
            </div>
            <div class="p-6 space-y-5">
                <div class="flex flex-col sm:flex-row gap-6">
                    <div class="shrink-0 text-center">
                        <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto">
                            <i class="fas fa-user-shield text-blue-600 text-4xl"></i>
                        </div>
                        <button class="mt-2 text-xs text-blue-600 hover:underline">
                            Change avatar
                        </button>
                    </div>
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label><input
                                type="text" value="{{ Auth::user()->name }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-800" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label><input
                                type="email" value="{{ Auth::user()->email }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label><input
                                type="text" value="{{ Auth::user()->role }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Department</label><input
                                type="text" value="{{ Auth::user()->department?->name ?? 'N/A' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" />
                        </div>
                    </div>
                </div>
                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" onclick="location.reload();"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
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
                    <i class="fas fa-bell text-blue-500 mr-2"></i> Notification
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
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-300 flex justify-end">
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">
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
                    <i class="fas fa-lock text-blue-500 mr-2"></i> Security &
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
                        class="px-3 py-1.5 border border-blue-300 text-blue-600 rounded-lg text-sm hover:bg-blue-50">
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
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-300 flex justify-end gap-3">
                <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
                    Cancel
                </button>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">
                    Update Password
                </button>
            </div>
        </div>
    </div>

    <!-- Integrations -->
    <div id="section-integrations" class="settings-section hidden">
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="font-semibold text-gray-800">
                    <i class="fas fa-plug text-blue-500 mr-2"></i> Connected
                    Services
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fa fa-cog fa-spin text-sm fa-fw text-blue-600"></i>
                        </div>
                        <div>
                            <p class="font-medium">Genova Insure</p>
                            <p class="text-xs text-gray-500">
                                Connected to Genova Insure
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
                            <i class="fa fa-cog fa-spin text-sm fa-fw text-green-600"></i>
                        </div>
                        <div>
                            <p class="font-medium">GLIMS</p>
                            <p class="text-xs text-gray-500">
                                Connected to GLIMS
                            </p>
                        </div>
                    </div>
                    <button class="px-3 py-1 border border-gray-300 rounded-lg text-sm">
                        Connect
                    </button>
                </div>
                <!-- GLIMS Integration -->
                {{-- <div class="border border-gray-200 rounded-xl p-4" id="glims-integration-card">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-database text-green-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">GLIMS</p>
                                <p class="text-xs text-gray-500">Oracle policy database — on-premise only</p>
                            </div>
                        </div>
                        <div id="glims-connection-badge"
                            class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-500">
                            Checking...
                        </div>
                    </div>

                    <div id="glims-sync-info" class="text-xs text-gray-500 mb-3 hidden">
                        <i class="fas fa-clock mr-1"></i>
                        <span id="glims-last-run">Never synced</span>
                    </div>

                    <div id="glims-progress" class="hidden mb-3">
                        <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                            <span id="glims-progress-message">Syncing...</span>
                            <span id="glims-progress-count"></span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                            <div id="glims-progress-bar"
                                class="bg-green-500 h-1.5 rounded-full transition-all duration-500" style="width: 0%">
                            </div>
                        </div>
                    </div>

                    <div id="glims-result" class="hidden text-xs rounded-lg p-2 mb-3"></div>

                    <button id="glims-sync-btn" onclick="triggerGlimsSync()" disabled
                        class="w-full py-2 px-4 bg-green-600 hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg transition flex items-center justify-center gap-2">
                        <i class="fas fa-sync-alt"></i>
                        <span>Sync GLIMS Data</span>
                    </button>
                </div> --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-cloud-upload-alt text-yellow-600 text-sm"></i>
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
            </div>
        </div>
    </div>

    <!-- Danger zone (visible on all tabs) -->
    {{-- <div class="mt-8 border border-red-200 rounded-xl bg-red-50/30 p-5">
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
    </div> --}}
    <script>
        // const GLIMS_TRIGGER_URL = '{{ route('staff.glims.sync.trigger') }}';
        // const GLIMS_STATUS_URL = '{{ route('staff.glims.sync.status') }}';
        // const CSRF = document.querySelector('meta[name="csrf-token"]').content;

        // let statusPoller = null;

        // ── Check status on page load ──────────────────────────────
        // async function checkGlimsStatus() {
        //     try {
        //         const res = await fetch(GLIMS_STATUS_URL);
        //         const data = await res.json();

        //         updateConnectionBadge(data.connected);
        //         updateSyncUI(data);

        //         // If a sync is running, start polling
        //         if (data.status === 'running') {
        //             startPolling();
        //         }
        //     } catch (e) {
        //         updateConnectionBadge(false);
        //     }
        // }

        // function updateConnectionBadge(connected) {
        //     const badge = document.getElementById('glims-connection-badge');
        //     const btn = document.getElementById('glims-sync-btn');

        //     if (connected) {
        //         badge.className = 'text-xs px-2 py-1 rounded-full bg-green-100 text-green-700';
        //         badge.innerHTML = '<i class="fas fa-circle text-[8px] mr-1"></i>Connected';
        //         btn.disabled = false;
        //     } else {
        //         badge.className = 'text-xs px-2 py-1 rounded-full bg-red-100 text-red-600';
        //         badge.innerHTML = '<i class="fas fa-circle text-[8px] mr-1"></i>Offline';
        //         btn.disabled = true;
        //     }
        // }

        // function updateSyncUI(data) {
        //     const info = document.getElementById('glims-sync-info');
        //     const lastRun = document.getElementById('glims-last-run');
        //     const progress = document.getElementById('glims-progress');
        //     const bar = document.getElementById('glims-progress-bar');
        //     const msg = document.getElementById('glims-progress-message');
        //     const count = document.getElementById('glims-progress-count');
        //     const result = document.getElementById('glims-result');
        //     const btn = document.getElementById('glims-sync-btn');

        //     if (data.status === 'running') {
        //         progress.classList.remove('hidden');
        //         result.classList.add('hidden');
        //         msg.textContent = data.message ?? 'Syncing...';
        //         count.textContent = data.synced ? `${data.synced} policies` : '';

        //         // Animate bar (indeterminate since we don't know total)
        //         bar.style.width = '60%';
        //         btn.disabled = true;
        //         btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Syncing...</span>';

        //     } else if (data.status === 'completed') {
        //         progress.classList.add('hidden');
        //         result.classList.remove('hidden');
        //         result.className = 'text-xs rounded-lg p-2 mb-3 bg-green-50 text-green-700 border border-green-200';
        //         result.innerHTML = `<i class="fas fa-check-circle mr-1"></i>${data.message}`;
        //         info.classList.remove('hidden');
        //         lastRun.textContent = 'Last sync: ' + (data.last_run_at ?? 'just now');
        //         btn.disabled = false;
        //         btn.innerHTML = '<i class="fas fa-sync-alt"></i><span>Sync GLIMS Data</span>';

        //     } else if (data.status === 'failed') {
        //         progress.classList.add('hidden');
        //         result.classList.remove('hidden');
        //         result.className = 'text-xs rounded-lg p-2 mb-3 bg-red-50 text-red-700 border border-red-200';
        //         result.innerHTML = `<i class="fas fa-exclamation-circle mr-1"></i>${data.message}`;
        //         btn.disabled = false;
        //         btn.innerHTML = '<i class="fas fa-sync-alt"></i><span>Retry Sync</span>';
        //     }
        // }

        // ── Trigger sync ───────────────────────────────────────────
        // async function triggerGlimsSync() {
        //     const btn = document.getElementById('glims-sync-btn');
        //     btn.disabled = true;
        //     btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Starting...</span>';

        //     try {
        //         const res = await fetch(GLIMS_TRIGGER_URL, {
        //             method: 'POST',
        //             headers: {
        //                 'X-CSRF-TOKEN': CSRF,
        //                 'Accept': 'application/json'
        //             },
        //         });
        //         const data = await res.json();

        //         if (data.success) {
        //             startPolling();
        //         } else {
        //             const result = document.getElementById('glims-result');
        //             result.classList.remove('hidden');
        //             result.className = 'text-xs rounded-lg p-2 mb-3 bg-red-50 text-red-700 border border-red-200';
        //             result.innerHTML = `<i class="fas fa-exclamation-circle mr-1"></i>${data.message}`;
        //             btn.disabled = false;
        //             btn.innerHTML = '<i class="fas fa-sync-alt"></i><span>Sync GLIMS Data</span>';
        //         }
        //     } catch (e) {
        //         btn.disabled = false;
        //         btn.innerHTML = '<i class="fas fa-sync-alt"></i><span>Sync GLIMS Data</span>';
        //     }
        // }

        // ── Poll status every 3s while running ────────────────────
        // function startPolling() {
        //     if (statusPoller) return; // already polling
        //     statusPoller = setInterval(async () => {
        //         const res = await fetch(GLIMS_STATUS_URL);
        //         const data = await res.json();
        //         updateSyncUI(data);

        //         if (data.status !== 'running') {
        //             clearInterval(statusPoller);
        //             statusPoller = null;
        //         }
        //     }, 3000);
        // }

        // Run on load
        // checkGlimsStatus();

        (function() {
            // ---------- FUNCTIONAL SETTINGS TABS ----------
            const tabs = document.querySelectorAll(".settings-tab");
            const sections = {
                profile: document.getElementById("section-profile"),
                notifications: document.getElementById("section-notifications"),
                security: document.getElementById("section-security"),
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
                            "text-blue-600",
                            "border-b-2",
                            "border-blue-600",
                        );
                    } else {
                        tab.classList.remove(
                            "text-blue-600",
                            "border-b-2",
                            "border-blue-600",
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
