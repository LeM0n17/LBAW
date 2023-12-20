<div id="Poll">
    <h3>{{ $poll->title }}</h3>
    <p>Total votes: {{ $poll->getTotalVotes() }}</p>
    @if(count($poll->options) > 0)
        @each('partials.polloption', $poll->options, 'option')
    @endif
    @if(Auth::user()->id == $poll->event->host->id)
        <form action="{{ route('deletePoll', $poll->id) }}" method="POST">
            @csrf
            <button type="submit">Delete Poll</button>
        </form>
        <form action="{{ route('addOption', $poll->id) }}" method="POST">
            @csrf
            <input type="text" name="option_name" placeholder="Enter option name">
            <button type="submit">Add Option</button>
        </form>
    @endif
</div>

    
</div>
