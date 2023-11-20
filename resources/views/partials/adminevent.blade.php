<div class="eventcard" id="{{ $event->id }}">
    <h3>{{ $event->name }}</h3>
    <label>By <b>{{ $event->host->name }}</b></label>
    <form method="POST" action="{{ route('deleteEvent', ['id' => $event->id]) }}">
        {{ csrf_field() }}
        <button type="submit">Delete</button>
    </form>
</div>
