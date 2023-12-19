<form action="{{ route('disconnectTag', ['tag_id' => $tag->tag->id, 'event_id' => $tag->event->id]) }}" method="POST">
    <button type="submit" class="existingtag">{{ $tag->tag->name }}</button>
</form> 