<div class="Poll">
    @if(Auth::user()->id == $poll->event->host->id)
    <div id="topsection">
        <h3><strong>{{ $poll->title }}</strong></h3>
        <form action="{{ route('deletePoll', $poll->id) }}" method="POST">
            @csrf
            <button type="submit" id="deleteButton">Delete</button>
        </form>
    </div>
    <p>Total votes: {{ $poll->getTotalVotes() }}</p>
    <form action="{{ route('addOption', $poll->id) }}" method="POST" id="addOption">
        @csrf
        <input type="text" name="option_name" placeholder="Enter option name">
        <button type="submit">Add Option</button>
    </form>
    @else
    <div id="topsection">
        <h3><strong>{{ $poll->title }}</strong></h3>
    </div>
    <p>Total votes: {{ $poll->getTotalVotes() }}</p>
    @endif

    @if(count($poll->options) > 0)
        @each('partials.polloption', $poll->options, 'option')
    @endif
</div>

