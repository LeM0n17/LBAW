<div class="user" id="{{ $tag->id }}">
    <label id="username"><strong>{{ $tag->name }}</strong></label>
    <form method="POST" action="{{ route('deleteUser', ['id' => $tag->id]) }}">
        {{ csrf_field() }}
        <button type="submit">Delete</button>
    </form>
</div>