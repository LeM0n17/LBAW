<div class="eventcard" id="{{ $event->id }}">
    <h3><a href="/events/{{ $event->id }}">{{ $event->name }}</a></h3>
    <label>By <b>{{ $event->host->name }}</b></label>
    <label>{{ $event->start }} - {{ $event->end_ }}</label>
    @if ($event->notifications->contains('id_developer', Auth::user()->id))
        <button type="button"> Accept Invite </button>
    @endif
</div>