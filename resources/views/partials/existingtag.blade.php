<form action="{{ route('', ['id' => $tag->tag->id]) }}" method="POST">
    <button type="submit" class="existingtag">{{ $tag->tag->name }}</button>
</form> 