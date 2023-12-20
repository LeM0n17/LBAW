@extends('layouts.app')

@section('content')
    <section id="polls">
        <h2 class="title">Polls</h2>
        @if(Auth::user()->id == $event->host->id)
        <form action="{{ route('createPoll', $event->id) }}" method="POST">
            @csrf
            <input type="text" name="poll_title" placeholder="Enter poll title">
            <button type="submit">Create Poll</button>
        </form>
        @endif
        <div class="sidescroller">
            @if(count($event->polls) == 0)
                <p>No polls yet</p>
            @else
                @each('partials.poll', $event->polls, 'poll')
            @endif
        </div>
    </section>
@endsection