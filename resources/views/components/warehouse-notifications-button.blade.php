@props(['unreadCount' => 0])

<div class="notifications-dropdown">
    <button type="button" class="btn btn-link position-relative" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-bell fs-5"></i>
        @if($unreadCount > 0)
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            <span class="visually-hidden">unread notifications</span>
        </span>
        @endif
    </button>

    <div class="dropdown-menu dropdown-menu-end p-0 border-0 shadow" style="width: 400px;">
        <div class="p-3">
            <x-warehouse-notifications-panel :notifications="$notifications" />
        </div>
    </div>
</div>

<style>
    .notifications-dropdown .btn-link {
        color: #6c757d;
        text-decoration: none;
        padding: 0.5rem;
    }

    .notifications-dropdown .btn-link:hover {
        color: #0d6efd;
    }

    .notifications-dropdown .dropdown-menu {
        margin-top: 0.5rem;
        border-radius: 0.5rem;
    }

    .badge {
        font-size: 0.65rem;
        padding: 0.35em 0.65em;
    }
</style>

<script>
    // Update unread count in real-time
    window.Echo.private('warehouse')
        .notification(() => {
            const badge = document.querySelector('.notifications-dropdown .badge');
            if (badge) {
                const count = parseInt(badge.textContent);
                badge.textContent = count + 1 > 99 ? '99+' : (count + 1);
            } else {
                const button = document.querySelector('.notifications-dropdown .btn-link');
                button.insertAdjacentHTML('beforeend', `
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    1
                    <span class="visually-hidden">unread notifications</span>
                </span>
            `);
            }
        });
</script>
