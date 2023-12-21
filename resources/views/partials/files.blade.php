<div class="comment">
    <h4>{{ $file->name }}</h4>
    <h6>By: {{ $file->user->name }}</h6>
    <form method="POST" action="{{ route('downloadFile', ['id' => $file->id]) }}">
        {{ csrf_field() }}
        <button type="submit"> Download </button>
    </form>
    <div class="sidecontainer">
        <p>{{ $file->likesCount() }} likes</p>
        @if($file->isLikedBy(Auth::user()))
            <form method="POST" action="{{route('removeLike', ['id_file' => $file->id])}}">
                {{ csrf_field() }}
                @method('DELETE')
                <button type="submit" style="color:red">&#x25B2;</button>
            </form>
        @else
            <form method="POST" action="{{route('addLike', ['id_file' => $file->id])}}">
                {{ csrf_field() }}
                <button type="submit" style="color:blue">&#x25B2;</button>
            </form>
        @endif
        @if($file->isDislikedBy(Auth::user()))
            <form method="POST" action="{{route('removeLike', ['id_file' => $file->id])}}">
                {{ csrf_field() }}
                @method('DELETE')
                <button type="submit" style="color:red">&#x25BC;</button>
            </form>
        @else
            <form method="POST" action="{{route('addDislike', ['id_file' => $file->id])}}">
                {{ csrf_field() }}
                <button type="submit" style="color:blue">&#x25BC;</button>
            </form>
        @endif
        <p>{{ $file->dislikesCount() }} dislikes</p>
        </div>
</div>
