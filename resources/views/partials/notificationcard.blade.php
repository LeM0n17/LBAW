<div class="notification" id="{{ $notification->id }}">
    <div class="buttoncontainer">
        <p><strong>{{ $notification->developer->name}}</strong> {{ $notification->content}} <strong><a href="/events/{{ $notification->id_event }}">{{ $notification->event->name }}</a></strong></p>
        <form method="POST" action="{{ route('addParticipantFromRequest', ['id_event' => $notification->event->id, 'id_user' => $notification->id_developer]) }}">
            {{ csrf_field() }}
            <button type="submit"> Accept Request </button>
        </form>
        <form method="POST" action="{{ route('refuseParticipantFromRequest', ['id_notification' => $notification->id]) }}">
            {{ csrf_field() }}
            <button type="submit"> Refuse Request </button>
        </form>
    </div>
</div>