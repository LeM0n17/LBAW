<div class="eventcard" id="{{ $event->id }}">
    <h3><a href="/events/{{ $event->id }}">{{ $event->name }}</a></h3>
    <label>By {{ $event->host->name }}</label>
    <label>{{ $event->start }} - {{ $event->end_ }}</label>
    <button type="button"> Request to Join </button>
</div>