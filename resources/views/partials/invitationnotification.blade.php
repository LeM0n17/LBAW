<div class="eventcard" id="{{ $notification->id }}">
    <h3><a href="/events/{{ $notification->id_event }}">{{ $notification->event->name }}</a></h3>
    <p>Invitation sent to {{ $notification->developer->name}}</p>
    <form method="POST" action="{{ route('refuseParticipantFromRequest', ['id_notification' => $notification->id]) }}">
        {{ csrf_field() }}
        <button type="submit"> Delete Notification </button>
    </form>
</div>