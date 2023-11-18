<div class="eventcard" id="{{ $event->id }}">
    <h2>{{ $event->name }}</h2>
    <label>By {{ $event->id_host }}</label><br>
    <label>{{ $event->start }} - {{ $event->end_ }}</label><br>
    <button type="button"> Request to Join </button>
</div>