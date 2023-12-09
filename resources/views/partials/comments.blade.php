<div>
    <h4>{{ $comment->user->name }}</h4>
    <p>{{ $comment->content }}</p>
    <p>{{ $comment->time }}</p>
    @if(Auth::id() == $comment->id_writer || Auth::id() == $comment->event->id_host)
        <form method="POST" action="{{ route('deletecomment', ['id' => $comment->id]) }}">
            {{ csrf_field() }}
            @method('DELETE')
            <button type="submit"> Delete </button>
        </form>
    @endif
</div>
