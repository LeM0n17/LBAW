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

    @if (Auth::user()->id == $event->host->id)
        <div class="buttoncontainer">
            <button type="button"><a href="/tagconfig/{{ $event->id }}"> Tags </a></button>
            <button type="button"><a href="/editevents/{{ $event->id }}"> Configure </a></button>
            <button type="button"><a href="/participants/{{ $event->id }}"> Participants </a></button>
            <form method="POST" action="{{ route('deleteevents', ['id' => $event->id]) }}">
                {{ csrf_field() }}
                <button type="submit"> Delete </button>
            </form>
            <form method="POST" action="{{ route('cancelevent', ['event_id' => $event->id]) }}">
                {{ csrf_field() }}
                <button type="submit"> Cancel </button>
            </form>
        </div>
    @endif
    @if ($event->notifications->where('type', 'invite')->contains('id_developer', Auth::user()->id))
        <form method="POST" action="{{ route('addHomeParticipant', ['id' => $event->id]) }}">
            {{ csrf_field() }}
            <button type="submit"> Accept Invite </button>
        </form>
    @endif
    @if (!$event->participants->contains('id_participant', Auth::user()->id))
        @if (!$event->notifications->where('type', 'request')->contains('id_developer', Auth::user()->id))
            <form method="POST" action="{{ route('requestToJoin', ['event_id' => $event->id, 'user_id' => Auth::user()->id]) }}">
                {{ csrf_field() }}
                <button type="submit"> Request to Join </button>
            </form>
        @else
            <p id="request">Request Sent waiting on answer from host</p>
        @endif
    @endif
    @if($event->participants->contains('id_participant', Auth::user()->id))
        <button type="button"><a href="/polls/{{ $event->id }}"> Polls </a></button>
        <form method="POST" action="{{ route('leaveEvent', ['id' => $event->id]) }}">
            {{ csrf_field() }}
            @method('DELETE')
            <button type="submit"> Leave Event </button>
        </form>
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
@endsection