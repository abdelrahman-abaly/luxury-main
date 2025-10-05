@props(['type' => 'info', 'icon' => 'info-circle', 'title', 'message', 'time', 'link' => null])

<div class="notification-item mb-2 animate__animated animate__fadeIn">
    <div class="card border-{{ $type }}">
        <div class="card-body p-2">
            <div class="d-flex align-items-center">
                <!-- Icon -->
                <div class="notification-icon bg-{{ $type }} bg-opacity-10 rounded-circle p-2 me-3">
                    <i class="fas fa-{{ $icon }} text-{{ $type }}"></i>
                </div>

                <!-- Content -->
                <div class="flex-grow-1">
                    <h6 class="mb-1 notification-title">{{ $title }}</h6>
                    <p class="mb-0 notification-message small text-muted">{{ $message }}</p>
                    <small class="notification-time text-muted">{{ $time }}</small>
                </div>

                <!-- Action Buttons -->
                <div class="ms-2 d-flex flex-column align-items-center">
                    @if($link)
                    <a href="{{ $link }}" class="btn btn-sm btn-outline-{{ $type }} mb-1">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    @endif
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="dismissNotification(this)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .notification-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .notification-title {
        font-size: 0.9rem;
        font-weight: 600;
    }

    .notification-message {
        font-size: 0.85rem;
    }

    .notification-time {
        font-size: 0.75rem;
    }

    .notification-item {
        transition: all 0.3s ease;
    }

    .notification-item.dismissing {
        transform: translateX(100%);
        opacity: 0;
    }
</style>

<script>
    function dismissNotification(button) {
        const item = button.closest('.notification-item');
        item.classList.add('dismissing');
        setTimeout(() => item.remove(), 300);
    }
</script>
