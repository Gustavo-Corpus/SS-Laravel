<div class="dropdown notification-dropdown">
    <button class="btn btn-link position-relative" type="button" id="notificationsDropdown" data-bs-toggle="dropdown">
        <i class="bi bi-bell-fill"></i>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ auth()->user()->unreadNotifications->count() }}
            </span>
        @endif
    </button>
    <ul class="dropdown-menu dropdown-menu-end" style="width: 300px;">
        <li class="dropdown-header">Notificaciones</li>
        @forelse(auth()->user()->notifications->take(5) as $notification)
            <li>
                <a class="dropdown-item {{ $notification->read_at ? 'text-muted' : '' }}"
                   href="/tickets/{{ $notification->data['ticket_id'] }}">
                    <div class="d-flex align-items-center">
                        <i class="bi {{ $notification->data['tipo'] === 'creacion' ? 'bi-plus-circle' : 'bi-arrow-clockwise' }} me-2"></i>
                        <div>
                            <div class="small">{{ $notification->data['mensaje'] }}</div>
                            <div class="smaller text-muted">
                                {{ $notification->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                </a>
            </li>
        @empty
            <li><span class="dropdown-item">No hay notificaciones</span></li>
        @endforelse
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item text-center" href="/notificaciones">
                Ver todas las notificaciones
            </a>
        </li>
    </ul>
</div>
