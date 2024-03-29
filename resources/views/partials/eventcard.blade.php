<div class="eventcard" id="event">
    <link href="{{ url('css/event.css') }}" rel="stylesheet">
    <h3><a href="/events/{{ $event->id }}">{{ $event->name }}</a></h3>
    <label>By <b>{{ $event->host->name }}</b></label>
    <label>{{ $event->start }} - {{ $event->end_ }}</label>
    <div class="tagcontainer">
        @each('partials.tagdisplay', $event->tags, 'tag')
    </div>
    @if ($event->notifications->where('type', 'invitation')->contains('id_developer', Auth::user()->id))
        <form method="POST" action="{{ route('addHomeParticipant', ['id' => $event->id]) }}">
            {{ csrf_field() }}
            <button type="submit"> Accept Invite </button>
        </form>
    @endif
</div>
