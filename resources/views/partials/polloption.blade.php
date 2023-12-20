<div id="Poll">
    <h4>{{ $option->name }}</h4>
    <p>Nº of votes:{{ $option->countVotes() }}</p>
    @if($option->countVotes() > 0)
        <p>Percentage:{{ $option->getVotePercentage() }}</p>
    @endif
    @if($option->hasVoted())
        <p>You have voted for this option</p>
        <form action="{{ route('removeVote', $option->id) }}" method="POST">
            @csrf
            <button type="submit">Remove Vote</button>
        </form>
    @else
        <form action="{{ route('vote', $option->id) }}" method="POST">
            @csrf
            <button type="submit">Vote</button>
        </form>
    @endif
</div>