<div id ="filters">
    <form id="dateForm" method="get" action="{{ route('filterByDate') }}">
        @csrf
        <p>Events starting from:</p>
        <input id="date" type="date" name="date">
        <button type="submit">Filter Date</button>
    </form>
    <form id="tagForm" method="get" action="{{ route('filterByTags') }}">
        @csrf
        <div class="sidescroller">
        @foreach($tags as $tag)
            <div class="tagoption">
                <label for="tag{{ $tag->id }}"><input type="checkbox" id="tag{{ $tag->id }}" name="tags[]" value="{{ $tag->id }}">{{ $tag->name }}</label>
            </div>
        @endforeach
        <button type="submit">Filter Tags</button>
        </div>
    </form> 
</div>
