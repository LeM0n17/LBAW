@extends('layouts.app')

@section('content')
<link href="{{ url('css/event.css') }}" rel="stylesheet">
<div class="eventinfo">
    <h2 id = "title">{{ $event->name }}</h2>
    @if (Auth::user()->id == $event->host->id)
        <button type="button"><a href="/editevents/{{ $event->id }}"> Configure </a></button><br>
        <button type="button"><a href="/participants/{{ $event->id }}"> Participants </a></button><br>
        <form method="POST" action="{{ route('deleteevents', ['id' => $event->id]) }}">
            {{ csrf_field() }}
            <button type="submit"> Delete </button>
        </form>
    @endif
    <label id="creator">By <b>{{ $event->host->name }}</b></label>
    <label id="duration">{{ $event->start }} - {{ $event->end_ }}</label>
    @if ($event->notifications->contains('id_developer', Auth::user()->id))
        <form method="POST" action="{{ route('addParticipant', ['id' => $event->id]) }}">
            {{ csrf_field() }}
            <button type="button"> Accept Invite </button>
        </form>
    @endif
    <p id="description">{{ $event->description }}</p>
</div>
@endsection