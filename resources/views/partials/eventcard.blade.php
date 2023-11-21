<div class="eventcard" id="{{ $event->id }}">
    <h3><a href="/events/{{ $event->id }}">{{ $event->name }}</a></h3>
    <label>By <b>{{ $event->host->name }}</b></label>
    <label>{{ $event->start }} - {{ $event->end_ }}</label>
    @if ($event->notifications->contains('id_developer', Auth::user()->id))
        <form method="POST" action="{{ route('addHomeParticipant', ['id' => $event->id]) }}">
            {{ csrf_field() }}
            <button type="submit"> Accept Invite </button>
        </form>
    @endif
</div>
