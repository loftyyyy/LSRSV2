{{-- Notification Bell Dropdown Component --}}
<div class="relative" id="notificationDropdown">
    {{-- Bell Button --}}
    <button type="button" onclick="toggleNotificationDropdown()" class="relative inline-flex items-center justify-center w-10 h-10 rounded-xl border border-neutral-300 bg-white text-neutral-700 hover:bg-violet-600 hover:text-white dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200 dark:hover:bg-violet-600 dark:hover:text-black transition-colors duration-300 ease-in-out">
        <x-icon name="bell" class="h-5 w-5" />
        {{-- Badge --}}
        <span id="notificationBadge" class="hidden absolute -top-1 -right-1 flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold text-white bg-rose-500 rounded-full">
            0
        </span>
    </button>

    {{-- Dropdown Panel --}}
    <div id="notificationPanel" class="hidden absolute right-0 mt-2 w-96 max-h-[500px] overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-xl dark:border-neutral-800 dark:bg-neutral-950 z-50">
        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-neutral-200 dark:border-neutral-800">
            <div class="flex items-center gap-2">
                <x-icon name="bell" class="h-4 w-4 text-violet-600 dark:text-violet-400" />
                <span class="text-sm font-semibold text-neutral-900 dark:text-white">Notifications</span>
                <span id="notificationCount" class="px-1.5 py-0.5 text-[10px] font-bold text-violet-700 bg-violet-100 dark:text-violet-300 dark:bg-violet-900/30 rounded-full">0</span>
            </div>
            <div class="flex items-center gap-1">
                <button type="button" onclick="refreshNotifications()" class="p-1.5 rounded-lg text-neutral-400 hover:text-neutral-700 hover:bg-neutral-100 dark:hover:text-neutral-200 dark:hover:bg-neutral-800 transition-colors" title="Refresh">
                    <x-icon name="refresh-cw" class="h-3.5 w-3.5" />
                </button>
                <button type="button" onclick="markAllNotificationsAsRead()" class="p-1.5 rounded-lg text-neutral-400 hover:text-neutral-700 hover:bg-neutral-100 dark:hover:text-neutral-200 dark:hover:bg-neutral-800 transition-colors" title="Mark all as read">
                    <x-icon name="check-check" class="h-3.5 w-3.5" />
                </button>
            </div>
        </div>

        {{-- Notification List --}}
        <div id="notificationList" class="max-h-[380px] overflow-y-auto">
            {{-- Loading state --}}
            <div id="notificationLoading" class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-6 w-6 border-2 border-violet-600 border-t-transparent"></div>
            </div>

            {{-- Empty state --}}
            <div id="notificationEmpty" class="hidden flex flex-col items-center justify-center py-8 px-4">
                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-neutral-100 dark:bg-neutral-800 mb-3">
                    <x-icon name="bell-off" class="h-6 w-6 text-neutral-400" />
                </div>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">No notifications</p>
                <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">You're all caught up!</p>
            </div>

            {{-- Notifications container --}}
            <div id="notificationItems" class="divide-y divide-neutral-100 dark:divide-neutral-800"></div>
        </div>

        {{-- Footer --}}
        <div class="px-4 py-2 border-t border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-900/50">
            <button type="button" onclick="dismissAllNotifications()" class="w-full text-xs text-neutral-500 hover:text-neutral-700 dark:hover:text-neutral-300 transition-colors">
                Dismiss all notifications
            </button>
        </div>
    </div>
</div>

<script>
    // Notification state
    let notificationsLoaded = false;
    let notificationPanelOpen = false;

    // Toggle dropdown
    function toggleNotificationDropdown() {
        const panel = document.getElementById('notificationPanel');
        notificationPanelOpen = !notificationPanelOpen;
        
        if (notificationPanelOpen) {
            panel.classList.remove('hidden');
            if (!notificationsLoaded) {
                loadNotifications();
            }
        } else {
            panel.classList.add('hidden');
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('notificationDropdown');
        if (dropdown && !dropdown.contains(e.target)) {
            document.getElementById('notificationPanel').classList.add('hidden');
            notificationPanelOpen = false;
        }
    });

    // Load notifications
    function loadNotifications() {
        const loading = document.getElementById('notificationLoading');
        const empty = document.getElementById('notificationEmpty');
        const items = document.getElementById('notificationItems');

        loading.classList.remove('hidden');
        empty.classList.add('hidden');
        items.innerHTML = '';

        fetch('/api/rentals/notifications?unread_only=0&per_page=20', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            loading.classList.add('hidden');
            notificationsLoaded = true;

            if (data.success) {
                updateNotificationBadge(data.counts.unread);
                document.getElementById('notificationCount').textContent = data.counts.unread;

                const notifications = data.notifications.data || [];
                if (notifications.length === 0) {
                    empty.classList.remove('hidden');
                } else {
                    renderNotifications(notifications);
                }
            }
        })
        .catch(error => {
            loading.classList.add('hidden');
            console.error('Failed to load notifications:', error);
        });
    }

    // Render notifications
    function renderNotifications(notifications) {
        const container = document.getElementById('notificationItems');
        container.innerHTML = '';

        notifications.forEach(notification => {
            const item = createNotificationItem(notification);
            container.appendChild(item);
        });
    }

    // Create notification item element
    function createNotificationItem(notification) {
        const div = document.createElement('div');
        div.className = `flex items-start gap-3 px-4 py-3 hover:bg-neutral-50 dark:hover:bg-neutral-900/50 transition-colors ${notification.is_read ? 'opacity-60' : ''}`;
        div.id = `notification-${notification.notification_id}`;

        const priorityColors = {
            'urgent': 'bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400',
            'high': 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400',
            'normal': 'bg-sky-100 text-sky-600 dark:bg-sky-900/30 dark:text-sky-400',
            'low': 'bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400'
        };

        const iconNames = {
            'due_reminder': 'calendar-clock',
            'overdue_alert': 'alert-triangle',
            'return_confirmation': 'check-circle',
            'extension_reminder': 'calendar-plus',
            'deposit_pending': 'wallet'
        };

        const priorityColor = priorityColors[notification.priority] || priorityColors['normal'];
        const iconName = iconNames[notification.type] || 'bell';

        // Format date
        const date = new Date(notification.created_at);
        const timeAgo = getTimeAgo(date);

        div.innerHTML = `
            <div class="flex items-center justify-center w-9 h-9 rounded-lg ${priorityColor} flex-shrink-0">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${getIconSvg(iconName)}
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-xs font-semibold text-neutral-900 dark:text-white truncate">${notification.title}</p>
                    <span class="text-[10px] text-neutral-400 flex-shrink-0">${timeAgo}</span>
                </div>
                <p class="text-xs text-neutral-600 dark:text-neutral-400 mt-0.5 line-clamp-2">${notification.message}</p>
                <div class="flex items-center gap-2 mt-2">
                    ${!notification.is_read ? `<button onclick="markNotificationRead(${notification.notification_id})" class="text-[10px] text-violet-600 dark:text-violet-400 hover:underline">Mark as read</button>` : ''}
                    <button onclick="dismissNotification(${notification.notification_id})" class="text-[10px] text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300">Dismiss</button>
                    ${notification.rental ? `<a href="/rentals?highlight=${notification.rental_id}" class="text-[10px] text-violet-600 dark:text-violet-400 hover:underline">View rental</a>` : ''}
                </div>
            </div>
        `;

        return div;
    }

    // Get icon SVG path
    function getIconSvg(name) {
        const icons = {
            'calendar-clock': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path><circle cx="12" cy="14" r="3" stroke-width="2"></circle>',
            'alert-triangle': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>',
            'check-circle': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
            'calendar-plus': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2zm7-8v4m-2-2h4"></path>',
            'wallet': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>',
            'bell': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>'
        };
        return icons[name] || icons['bell'];
    }

    // Get time ago string
    function getTimeAgo(date) {
        const seconds = Math.floor((new Date() - date) / 1000);
        const intervals = [
            { label: 'y', seconds: 31536000 },
            { label: 'mo', seconds: 2592000 },
            { label: 'd', seconds: 86400 },
            { label: 'h', seconds: 3600 },
            { label: 'm', seconds: 60 }
        ];
        for (const interval of intervals) {
            const count = Math.floor(seconds / interval.seconds);
            if (count >= 1) return `${count}${interval.label} ago`;
        }
        return 'Just now';
    }

    // Update badge
    function updateNotificationBadge(count) {
        const badge = document.getElementById('notificationBadge');
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }

    // Refresh notifications
    function refreshNotifications() {
        notificationsLoaded = false;
        loadNotifications();
    }

    // Mark notification as read
    function markNotificationRead(id) {
        fetch(`/api/rentals/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = document.getElementById(`notification-${id}`);
                if (item) item.classList.add('opacity-60');
                refreshNotificationCount();
            }
        });
    }

    // Dismiss notification
    function dismissNotification(id) {
        fetch(`/api/rentals/notifications/${id}/dismiss`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = document.getElementById(`notification-${id}`);
                if (item) item.remove();
                refreshNotificationCount();
                checkEmptyState();
            }
        });
    }

    // Mark all as read
    function markAllNotificationsAsRead() {
        fetch('/api/rentals/notifications/read-all', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelectorAll('#notificationItems > div').forEach(item => {
                    item.classList.add('opacity-60');
                });
                updateNotificationBadge(0);
                document.getElementById('notificationCount').textContent = '0';
            }
        });
    }

    // Dismiss all
    function dismissAllNotifications() {
        fetch('/api/rentals/notifications/dismiss-all', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('notificationItems').innerHTML = '';
                document.getElementById('notificationEmpty').classList.remove('hidden');
                updateNotificationBadge(0);
                document.getElementById('notificationCount').textContent = '0';
            }
        });
    }

    // Refresh count
    function refreshNotificationCount() {
        fetch('/api/rentals/notifications/count', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateNotificationBadge(data.unread_count);
                document.getElementById('notificationCount').textContent = data.unread_count;
            }
        });
    }

    // Check empty state
    function checkEmptyState() {
        const items = document.getElementById('notificationItems');
        const empty = document.getElementById('notificationEmpty');
        if (items.children.length === 0) {
            empty.classList.remove('hidden');
        }
    }

    // Load notification count on page load
    document.addEventListener('DOMContentLoaded', function() {
        refreshNotificationCount();
        // Auto-refresh count every 2 minutes
        setInterval(refreshNotificationCount, 120000);
    });
</script>
