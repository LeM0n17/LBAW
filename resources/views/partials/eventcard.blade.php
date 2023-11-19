<div class="eventcard" id="{{ $event->id }}">
    <h3>{{ $event->name }}</h3>
    <label>By {{ $event->id_host }}</label>
    <label>{{ $event->start }} - {{ $event->end_ }}</label>
    <button type="button"> Request to Join </button>
</div>