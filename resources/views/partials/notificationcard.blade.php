<div class="eventcard" id="{{ $notification->id }}">
    <h3><a href="/events/{{ $notification->id_event }}">{{ $notification->event->name }}</a></h3>
    <h4>{{ $notification->developer->name}}</h4>
    <p>{{ $notification->content }}</p>
    <form method="POST" action="{{ route('addParticipantFromRequest', ['id_event' => $notification->event->id, 'id_user' => $notification->id_developer]) }}">
        {{ csrf_field() }}
        <button type="submit"> Accept Request </button>
    </form>
    <form method="POST" action="{{ route('refuseParticipantFromRequest', ['id_notification' => $notification->id]) }}">
        {{ csrf_field() }}
        <button type="submit"> Refuse Request </button>
    </form>
</div>