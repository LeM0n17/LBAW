<div id ="filters">
    <form id="dateForm" method="get" action="{{ route('filterByDate') }}">
        @csrf
        <p>List all the events that start after given date:</p>
        <input id="date" type="date" name="date">
        <button type="submit">Filter by Date</button>
    </form>
    <form id="tagForm" method="get" action="{{ route('filterByTags') }}">
        @csrf
        @foreach($tags as $tag)
            <div>
                <input type="checkbox" id="tag{{ $tag->id }}" name="tags[]" value="{{ $tag->id }}">
                <label for="tag{{ $tag->id }}">{{ $tag->name }}</label>
            </div>
        @endforeach
        <button type="submit">Filter by Tag</button>
    </form> 
</div>