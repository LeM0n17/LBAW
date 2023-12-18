<div class="eventcard" id="{{ $notification->id }}">
    <h3><a href="/events/{{ $notification->id_event }}">{{ $notification->event->name }}</a></h3>
    <h4>{{ $notification->developer->name}}</h4>
    <p>{{ $notification->content }}</p>
</div>