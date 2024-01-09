@if (!($user->name == "Deleted User"))
<div class="user" id="{{ $user->id }}">
    <label id="username"><strong>{{ $user->name }}</strong></label>
    <form method="POST" action="{{ route('deleteUser', ['id' => $user->id]) }}">
        {{ csrf_field() }}
        <button type="submit">Ban</button>
    </form>
</div>
@endif