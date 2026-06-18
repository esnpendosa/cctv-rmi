<div class="dropdown" wire:poll.15s>
    <button class="btn btn-link position-relative p-2" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="color: var(--color-text-secondary); text-decoration: none;" aria-label="Notifications, {{ $unreadCount }} unread">
        <i class="bi bi-bell-fill" style="font-size: 1.25rem;"></i>
        @if($unreadCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem; padding: 0.35em 0.5em;">
                {{ $unreadCount }}
                <span class="visually-hidden">unread notifications</span>
            </span>
        @endif
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow-2 border-0 py-0" aria-labelledby="notificationDropdown" style="width: 320px; border-radius: var(--radius-md); overflow: hidden;">
        <li class="p-3 d-flex justify-content-between align-items-center" style="background-color: var(--color-surface-raised); border-bottom: 1px solid rgba(0,0,0,0.05);">
            <span class="fw-bold" style="color: var(--color-text-tertiary);">Notifikasi</span>
            @if($unreadCount > 0)
                <button type="button" wire:click="markAllAsRead" class="btn btn-link p-0 text-decoration-none" style="font-size: 0.85rem; color: var(--color-text-inverse);">Tandai semua dibaca</button>
            @endif
        </li>
        <div style="max-height: 280px; overflow-y: auto;">
            @forelse($notifications as $notification)
                <li class="border-bottom" style="transition: background-color var(--motion-duration-instant) ease;">
                    <div class="d-flex align-items-start p-3 gap-2 position-relative">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold text-dark" style="font-size: var(--font-size-sm);">
                                    {{ $notification->data['title'] ?? 'Notifikasi Baru' }}
                                </span>
                                <span class="text-muted" style="font-size: 0.75rem;">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <p class="mb-1 text-secondary" style="font-size: var(--font-size-xs); line-height: 1.4;">
                                {{ $notification->data['message'] ?? '' }}
                            </p>
                            <button type="button" wire:click="markAsRead('{{ $notification->id }}')" class="btn btn-link p-0 text-decoration-none mt-1" style="font-size: 0.75rem; color: var(--color-text-inverse);" aria-label="Mark notification as read">
                                <i class="bi bi-check2-all"></i> Tandai dibaca
                            </button>
                        </div>
                    </div>
                </li>
            @empty
                <li class="p-4 text-center text-muted">
                    <i class="bi bi-bell-slash d-block mb-2 text-secondary" style="font-size: 2rem;"></i>
                    <span style="font-size: var(--font-size-sm);">Tidak ada notifikasi baru</span>
                </li>
            @endforelse
        </div>
    </ul>
</div>
