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
    <div class="sidecontainer">
    <p>{{ $comment->likesCount() }} likes</p>
    @if($comment->isLikedBy(Auth::user()))
        <form method="POST" action="{{route('removeLike', ['id_comment' => $comment->id])}}">
            {{ csrf_field() }}
            @method('DELETE')
            <button type="submit" style="color:red">&#x25B2;</button>
        </form>
    @else
        <form method="POST" action="{{route('addLike', ['id_comment' => $comment->id])}}">
            {{ csrf_field() }}
            <button type="submit" style="color:blue">&#x25B2;</button>
        </form>
    @endif
    @if($comment->isDislikedBy(Auth::user()))
        <form method="POST" action="{{route('removeLike', ['id_comment' => $comment->id])}}">
            {{ csrf_field() }}
            @method('DELETE')
            <button type="submit" style="color:red">&#x25BC;</button>
        </form>
    @else
        <form method="POST" action="{{route('addDislike', ['id_comment' => $comment->id])}}">
            {{ csrf_field() }}
            <button type="submit" style="color:blue">&#x25BC;</button>
        </form>
    @endif
    <p>{{ $comment->dislikesCount() }} dislikes</p>
    </div>
</div>
