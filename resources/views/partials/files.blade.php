<div>
    <h4>{{ $file->name }}</h4>
    <form method="POST" action="{{ route('downloadFile', ['id' => $file->id]) }}">
        {{ csrf_field() }}
        <button type="submit"> Download </button>
    </form>
    <h5>By: {{ $file->user->name }}</h5>
</div>
