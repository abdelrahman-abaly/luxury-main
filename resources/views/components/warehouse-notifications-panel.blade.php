@props(['notifications' => []])

<div class="notifications-panel">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">
            <i class="fas fa-bell text-warning me-2"></i>
            Notifications
        </h5>
        <div>
            <button type="button" class="btn btn-sm btn-outline-primary me-1" onclick="markAllAsRead()">
                <i class="fas fa-check-double me-1"></i>
                Mark All as Read
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearAll()">
                <i class="fas fa-trash me-1"></i>
                Clear All
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="btn-group btn-group-sm w-100 mb-3">
        <button type="button" class="btn btn-outline-secondary active" data-filter="all">
            All
        </button>
        <button type="button" class="btn btn-outline-danger" data-filter="alert">
            Alerts
        </button>
        <button type="button" class="btn btn-outline-warning" data-filter="warning">
            Warnings
        </button>
        <button type="button" class="btn btn-outline-info" data-filter="info">
            Info
        </button>
    </div>

    <!-- Notifications List -->
    <div class="notifications-list">
        @forelse($notifications as $notification)
        <x-warehouse-notification
            :type="$notification->data['type'] ?? 'info'"
            :icon="$notification->data['icon'] ?? 'info-circle'"
            :title="$notification->data['title']"
            :message="$notification->data['message']"
            :time="$notification->created_at->diffForHumans()"
            :link="$notification->data['link'] ?? null" />
        @empty
        <div class="text-center py-4">
            <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
            <h6 class="text-muted">No notifications</h6>
            <p class="text-muted small mb-0">You're all caught up!</p>
        </div>
        @endforelse
    </div>

    <!-- Load More -->
    @if($notifications->hasMorePages())
    <div class="text-center mt-3">
        <button type="button" class="btn btn-outline-primary btn-sm" onclick="loadMoreNotifications()">
            <i class="fas fa-sync me-1"></i>
            Load More
        </button>
    </div>
    @endif
</div>

<style>
    .notifications-panel {
        min-width: 300px;
        max-width: 400px;
    }

    .notifications-list {
        max-height: 400px;
        overflow-y: auto;
    }

    .notifications-list::-webkit-scrollbar {
        width: 6px;
    }

    .notifications-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .notifications-list::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }

    .notifications-list::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>

<script>
    let currentPage = 1;
    let currentFilter = 'all';

    function markAllAsRead() {
        fetch('/warehouse/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).then(() => {
            document.querySelectorAll('.notification-item').forEach(item => {
                item.classList.add('read');
            });
        });
    }

    function clearAll() {
        if (!confirm('Are you sure you want to clear all notifications?')) return;

        fetch('/warehouse/notifications/clear-all', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).then(() => {
            document.querySelector('.notifications-list').innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                <h6 class="text-muted">No notifications</h6>
                <p class="text-muted small mb-0">You're all caught up!</p>
            </div>
        `;
        });
    }

    function loadMoreNotifications() {
        currentPage++;
        fetch(`/warehouse/notifications?page=${currentPage}&filter=${currentFilter}`)
            .then(response => response.text())
            .then(html => {
                document.querySelector('.notifications-list').insertAdjacentHTML('beforeend', html);
            });
    }

    // Filter buttons
    document.querySelectorAll('.btn-group button[data-filter]').forEach(button => {
        button.addEventListener('click', function() {
            // Update active state
            document.querySelectorAll('.btn-group button').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            // Update filter and reload notifications
            currentFilter = this.dataset.filter;
            currentPage = 1;

            fetch(`/warehouse/notifications?filter=${currentFilter}`)
                .then(response => response.text())
                .then(html => {
                    document.querySelector('.notifications-list').innerHTML = html;
                });
        });
    });

    // Real-time notifications with Laravel Echo
    window.Echo.private('warehouse')
        .notification((notification) => {
            const notificationHtml = `
            <x-warehouse-notification
                type="${notification.data.type || 'info'}"
                icon="${notification.data.icon || 'info-circle'}"
                title="${notification.data.title}"
                message="${notification.data.message}"
                time="Just now"
                link="${notification.data.link || ''}"
            />
        `;

            const list = document.querySelector('.notifications-list');
            const empty = list.querySelector('.text-center');
            if (empty) {
                list.innerHTML = notificationHtml;
            } else {
                list.insertAdjacentHTML('afterbegin', notificationHtml);
            }
        });
</script>
