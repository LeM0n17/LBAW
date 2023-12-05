<div>
    <h4>{{ $comment->username }}</h4>
    <p>{{ $comment->description }}</p>
    <form method="POST" action="{{ route('deletecomment', ['id' => $comment->id]) }}">
        {{ csrf_field() }}
        <button type="submit"> Delete </button>
    </form>
</div>
