{{-- Rental Settings Modal --}}
<div id="rentalSettingsModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-2 py-4 bg-black/60 backdrop-blur-sm overflow-y-auto">
    <div class="w-full max-w-2xl bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 rounded-3xl shadow-2xl flex flex-col max-h-[calc(100vh-2rem)] my-auto">
        {{-- Header --}}
        <div class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 dark:bg-neutral-900/50 rounded-t-3xl">
            <div class="flex items-center gap-4">
                {{-- Settings Icon --}}
                <div class="h-12 w-12 rounded-full bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center text-white shadow-lg">
                    <x-icon name="settings" class="h-6 w-6" />
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-500">Configuration</p>
                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Rental Settings</h3>
                </div>
            </div>
            <button onclick="closeRentalSettingsModal()" class="text-neutral-500 hover:text-neutral-800 dark:hover:text-neutral-200 text-xl transition-colors duration-200">&times;</button>
        </div>

        {{-- Content --}}
        <div class="flex-1 overflow-y-auto p-6">
            {{-- Loading State --}}
            <div id="rentalSettingsLoading" class="flex items-center justify-center py-12">
                <div class="flex flex-col items-center gap-3">
                    <div class="h-8 w-8 animate-spin rounded-full border-2 border-violet-600 border-t-transparent"></div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Loading settings...</p>
                </div>
            </div>

            {{-- Settings Form --}}
            <form id="rentalSettingsForm" class="hidden space-y-6">
                {{-- Penalty Settings Section --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2 pb-2 border-b border-neutral-200 dark:border-neutral-800">
                        <x-icon name="alert-triangle" class="h-5 w-5 text-rose-500" />
                        <h4 class="text-sm font-semibold text-neutral-900 dark:text-white uppercase tracking-wide">Penalty Settings</h4>
                    </div>

                    {{-- Penalty Rate Per Day --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block">
                                <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    Penalty Rate per Day
                                </span>
                                <div class="relative mt-2">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-500 dark:text-neutral-400 text-sm">₱</span>
                                    <input type="number" name="penalty_rate_per_day" id="settingPenaltyRate" step="0.01" min="0" max="999999.99"
                                           class="w-full rounded-xl border border-neutral-300 bg-white pl-8 pr-4 py-3 text-sm text-neutral-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white transition-colors duration-200" />
                                </div>
                            </label>
                            <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-500">Amount charged per day for overdue rentals</p>
                        </div>

                        <div>
                            <label class="block">
                                <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    Grace Period (Hours)
                                </span>
                                <input type="number" name="penalty_grace_period_hours" id="settingGracePeriod" min="0" max="720"
                                       class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white transition-colors duration-200" />
                            </label>
                            <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-500">Hours after due date before penalty starts</p>
                        </div>
                    </div>

                    {{-- Max Penalty Days --}}
                    <div>
                        <label class="block">
                            <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                Maximum Penalty Days
                            </span>
                            <input type="number" name="max_penalty_days" id="settingMaxPenaltyDays" min="0" max="365"
                                   class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white transition-colors duration-200" />
                        </label>
                        <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-500">Maximum number of days to charge penalty (0 = unlimited)</p>
                    </div>
                </div>

                {{-- Notification Settings Section --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2 pb-2 border-b border-neutral-200 dark:border-neutral-800">
                        <x-icon name="bell" class="h-5 w-5 text-amber-500" />
                        <h4 class="text-sm font-semibold text-neutral-900 dark:text-white uppercase tracking-wide">Notification Settings</h4>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block">
                                <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    Reminder Days Before Due
                                </span>
                                <input type="number" name="notification_due_days_before" id="settingReminderDays" min="0" max="30"
                                       class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white transition-colors duration-200" />
                            </label>
                            <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-500">Days before due date to send reminder</p>
                        </div>

                        <div>
                            <label class="flex items-center gap-3 mt-8">
                                <input type="checkbox" name="notification_overdue_enabled" id="settingOverdueNotifications"
                                       class="h-5 w-5 rounded border-neutral-300 text-violet-600 focus:ring-violet-500 dark:border-neutral-600 dark:bg-neutral-800" />
                                <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    Enable Overdue Notifications
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- General Settings Section --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2 pb-2 border-b border-neutral-200 dark:border-neutral-800">
                        <x-icon name="sliders-horizontal" class="h-5 w-5 text-violet-500" />
                        <h4 class="text-sm font-semibold text-neutral-900 dark:text-white uppercase tracking-wide">General Settings</h4>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block">
                                <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    Default Rental Period (Days)
                                </span>
                                <input type="number" name="default_rental_days" id="settingDefaultRentalDays" min="1" max="365"
                                       class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white transition-colors duration-200" />
                            </label>
                            <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-500">Default number of days for new rentals</p>
                        </div>

                        <div>
                            <label class="block">
                                <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    Maximum Extensions Allowed
                                </span>
                                <input type="number" name="max_extension_count" id="settingMaxExtensions" min="0" max="100"
                                       class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white transition-colors duration-200" />
                            </label>
                            <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-500">Maximum number of extensions per rental (0 = unlimited)</p>
                        </div>
                    </div>
                </div>

                {{-- Error Display --}}
                <div id="rentalSettingsError" class="hidden p-4 rounded-xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800/50">
                    <div class="flex items-center gap-2 text-rose-700 dark:text-rose-400">
                        <x-icon name="alert-circle" class="h-5 w-5 flex-shrink-0" />
                        <p id="rentalSettingsErrorText" class="text-sm font-medium"></p>
                    </div>
                </div>

                {{-- Success Display --}}
                <div id="rentalSettingsSuccess" class="hidden p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/50">
                    <div class="flex items-center gap-2 text-emerald-700 dark:text-emerald-400">
                        <x-icon name="check-circle" class="h-5 w-5 flex-shrink-0" />
                        <p class="text-sm font-medium">Settings saved successfully!</p>
                    </div>
                </div>
            </form>
        </div>

        {{-- Footer --}}
        <div class="flex-shrink-0 flex items-center justify-end gap-3 px-6 py-4 border-t border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 dark:bg-neutral-900/50 rounded-b-3xl">
            <button type="button" onclick="closeRentalSettingsModal()" class="px-5 py-2.5 text-sm font-medium text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-xl transition-colors duration-200">
                Cancel
            </button>
            <button type="button" id="saveRentalSettingsBtn" onclick="saveRentalSettings()"
                    class="px-5 py-2.5 text-sm font-medium text-white dark:text-black bg-violet-600 hover:bg-violet-700 disabled:bg-neutral-300 disabled:text-neutral-500 dark:disabled:bg-neutral-700 dark:disabled:text-neutral-500 rounded-xl transition-colors duration-200 flex items-center gap-2">
                <x-icon name="save" class="h-4 w-4" />
                <span>Save Settings</span>
            </button>
        </div>
    </div>
</div>

<script>
    // Rental Settings Modal State
    globalThis.rentalSettingsModalState = {
        isOpen: false,
        settings: {},
        isSaving: false
    };

    // Open settings modal
    globalThis.openRentalSettingsModal = function openRentalSettingsModal() {
        globalThis.rentalSettingsModalState.isOpen = true;

        var loading = document.getElementById('rentalSettingsLoading');
        var form = document.getElementById('rentalSettingsForm');
        var error = document.getElementById('rentalSettingsError');
        var success = document.getElementById('rentalSettingsSuccess');

        if (loading) {
            loading.classList.remove('hidden');
            loading.classList.add('flex');
        }
        if (form) form.classList.add('hidden');
        if (error) error.classList.add('hidden');
        if (success) success.classList.add('hidden');

        var modal = document.getElementById('rentalSettingsModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        fetchRentalSettings();
    };

    // Close settings modal
    globalThis.closeRentalSettingsModal = function closeRentalSettingsModal() {
        globalThis.rentalSettingsModalState.isOpen = false;

        var modal = document.getElementById('rentalSettingsModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    };

    // Fetch rental settings from API
    function fetchRentalSettings() {
        axios.get('/api/rentals/settings')
            .then(function(response) {
                if (response.data.success) {
                    globalThis.rentalSettingsModalState.settings = response.data.data.settings;
                    populateSettingsForm(response.data.data.settings);
                } else {
                    showSettingsError('Failed to load settings');
                }
            })
            .catch(function(error) {
                console.error('Error fetching rental settings:', error);
                showSettingsError(error.response?.data?.message || 'Failed to load settings');
            })
            .finally(function() {
                var loading = document.getElementById('rentalSettingsLoading');
                if (loading) {
                    loading.classList.add('hidden');
                    loading.classList.remove('flex');
                }
                var form = document.getElementById('rentalSettingsForm');
                if (form) form.classList.remove('hidden');
            });
    }

    // Populate the form with settings values
    function populateSettingsForm(settings) {
        settings.forEach(function(setting) {
            switch (setting.setting_key) {
                case 'penalty_rate_per_day':
                    document.getElementById('settingPenaltyRate').value = setting.setting_value;
                    break;
                case 'penalty_grace_period_hours':
                    document.getElementById('settingGracePeriod').value = setting.setting_value;
                    break;
                case 'max_penalty_days':
                    document.getElementById('settingMaxPenaltyDays').value = setting.setting_value;
                    break;
                case 'notification_due_days_before':
                    document.getElementById('settingReminderDays').value = setting.setting_value;
                    break;
                case 'notification_overdue_enabled':
                    document.getElementById('settingOverdueNotifications').checked = Boolean(parseInt(setting.setting_value));
                    break;
                case 'default_rental_days':
                    document.getElementById('settingDefaultRentalDays').value = setting.setting_value;
                    break;
                case 'max_extension_count':
                    document.getElementById('settingMaxExtensions').value = setting.setting_value;
                    break;
            }
        });
    }

    // Save rental settings
    function saveRentalSettings() {
        if (globalThis.rentalSettingsModalState.isSaving) return;

        globalThis.rentalSettingsModalState.isSaving = true;

        var saveBtn = document.getElementById('saveRentalSettingsBtn');
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span><span>Saving...</span>';

        document.getElementById('rentalSettingsError').classList.add('hidden');
        document.getElementById('rentalSettingsSuccess').classList.add('hidden');

        var settingsData = {
            penalty_rate_per_day: Math.min(parseFloat(document.getElementById('settingPenaltyRate').value) || 0, 999999.99),
            penalty_grace_period_hours: Math.min(parseInt(document.getElementById('settingGracePeriod').value) || 0, 720),
            max_penalty_days: Math.min(parseInt(document.getElementById('settingMaxPenaltyDays').value) || 0, 365),
            notification_due_days_before: Math.min(parseInt(document.getElementById('settingReminderDays').value) || 0, 30),
            notification_overdue_enabled: document.getElementById('settingOverdueNotifications').checked ? 1 : 0,
            default_rental_days: Math.max(1, Math.min(parseInt(document.getElementById('settingDefaultRentalDays').value) || 1, 365)),
            max_extension_count: Math.min(parseInt(document.getElementById('settingMaxExtensions').value) || 0, 100)
        };

        axios.put('/api/rentals/settings', { settings: settingsData })
            .then(function(response) {
                if (response.data.success) {
                    document.getElementById('rentalSettingsSuccess').classList.remove('hidden');
                    setTimeout(function() {
                        closeRentalSettingsModal();
                    }, 1500);
                } else {
                    showSettingsError(response.data.message || 'Failed to save settings');
                }
            })
            .catch(function(error) {
                console.error('Error saving rental settings:', error);
                showSettingsError(error.response?.data?.message || 'Failed to save settings');
            })
            .finally(function() {
                globalThis.rentalSettingsModalState.isSaving = false;
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg><span>Save Settings</span>';
            });
    }

    // Show error message
    function showSettingsError(message) {
        document.getElementById('rentalSettingsErrorText').textContent = message;
        document.getElementById('rentalSettingsError').classList.remove('hidden');
    }
</script>
