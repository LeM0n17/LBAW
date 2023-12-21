<div class="comment">
    <h4 id="user">{{ $comment->user->name }}</h4>
    <p id="content">{{ $comment->content }}</p>
    <p id="time">{{ $comment->time }}</p>
    @if(Auth::id() == $comment->id_writer || Auth::id() == $comment->event->id_host)
        <form method="POST" action="{{ route('deletecomment', ['id' => $comment->id]) }}" id="deletecom">
            {{ csrf_field() }}
            @method('DELETE')
            <button type="submit"> Delete </button>
        </form>
    @endif
</div>
