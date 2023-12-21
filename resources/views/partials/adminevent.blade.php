<div class="eventcard" id="{{ $event->id }}">

    <h3><a href="/events/{{ $event->id }}">{{ $event->name }}</a></h3>
    <label>By <b>{{ $event->host->name }}</b></label>
    <form method="POST" action="{{ route('deleteEvent', ['id' => $event->id]) }}">
        {{ csrf_field() }}
        <button type="submit">Delete</button>
    </form>
    
</div>
