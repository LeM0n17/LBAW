@extends('layouts.app')

@section('content')
<link href="{{ url('css/event.css') }}" rel="stylesheet">
<div class="eventinfo">
    <h2 id = "title">{{ $event->name }}</h2>
    <label id="creator">By <b>{{ $event->host->name }}</b></label>
    <label id="duration">{{ $event->start }} - {{ $event->end_ }}</label>
    <div class="tagcontainer">
        @each('partials.tagdisplay', $event->tags, 'tag')
    </div>
    @if ($event->participants->contains('id_participant', Auth::user()->id))
        <label id="userin">You are already registered in this event!</label>
    @elseif ($event->notifications->contains('id_developer', Auth::user()->id))
        <form method="POST" action="{{ route('addHomeParticipant', ['id' => $event->id]) }}" id="joinform">
            {{ csrf_field() }}
            <button type="submit"> Accept Invite </button>
        </form>
    @endif
    @if (Auth::user()->id == $event->host->id)
        <div class="buttoncontainer">
            <button type="button"><a href="/editevents/{{ $event->id }}"> Configure </a></button>
            <button type="button"><a href="/tagconfig/{{ $event->id }}"> Tags </a></button>
            <button type="button"><a href="/participants/{{ $event->id }}"> Participants </a></button>
            <form method="POST" action="{{ route('deleteevents', ['id' => $event->id]) }}" id="deleteform">
                {{ csrf_field() }}
                <button type="submit" id="deletebutton"> Delete </button>
            </form>
            <form method="POST" action="{{ route('cancelevent', ['event_id' => $event->id]) }}">
                {{ csrf_field() }}
                <button type="submit"> Cancel </button>
            </form>
        </div>
    @endif
    <hr>
    <p id="description">{{ $event->description }}</p>
</div>
<div class="commentsection">
    @each('partials.comments', $event->comments, 'comment')
    <form action="{{ route('createcomment', ['id' => $event->id]) }}" method="POST">
        {{ csrf_field() }}
        <div>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <label for="content">New Comment(participants only):</label>
            <textarea name="content" id="content" placeholder="Enter your comment here..."></textarea>
        </div>
        <button type="submit">Add Comment</button>
    </form>
</div>