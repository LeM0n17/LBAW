<div id="Option">
    <div class="optionHead">
        <h4 class="optionName">• {{ $option->name }}</h4>
        @if($option->hasVoted())
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
    <p>Nº of votes:{{ $option->countVotes() }} ({{ $option->getVotePercentage() }}%)</p>
    @if($option->countVotes() > 0)
        <p>Percentage:{{ $option->getVotePercentage() }}</p>
    @endif
</div>