@extends('layouts.app')

@section('content')
<link href="{{ url('css/polls.css') }}" rel="stylesheet">
    <section id="polls">
        @if(Auth::user()->id == $event->host->id)
        <form action="{{ route('createPoll', $event->id) }}" method="POST" id="createPoll">
            @csrf
            <h3>Create Poll:</h3>
            <input type="text" name="poll_title" placeholder="Enter poll title">
            <button type="submit">Create</button>
        </form>
        @endif
        <h2 class="title">Polls:</h2>
        <div class="sidescroller">
            @if(count($event->polls) == 0)
                <p>No polls yet</p>
            @else
                @each('partials.poll', $event->polls, 'poll')
            @endif
        </div>
    </section>
@endsection
