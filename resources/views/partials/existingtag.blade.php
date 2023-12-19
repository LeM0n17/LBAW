<form action="{{ route('disconnectTag', ['tag_id' => $tag->tag->id, 'event_id' => $tag->event->id]) }}" method="POST">
    {{ csrf_field() }}
    <button type="submit" class="existingtag">{{ $tag->tag->name }}</button>
</form> 