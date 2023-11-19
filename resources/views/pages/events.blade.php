@extends('layouts.app')

@section('content')
<link href="{{ url('css/event.css') }}" rel="stylesheet">
<div class="eventinfo">
    <h2 id = "title">{{ $event->name }}</h2>
    @if (Auth::user()->id == $event->host->id)
        <button type="button"><a href="/editevents/{{ $event->id }}"> Configure </a></button><br>
        <form method="POST" action="{{ route('deleteevents', ['id' => $event->id]) }}">
            {{ csrf_field() }}
            <button type="submit"><a href="/deleteevents/{{ $event->id }}"> Delete </a></button>
        </form>
    @endif
    <label id="creator">By <b>{{ $event->host->name }}</b></label>
    <label id="duration">{{ $event->start }} - {{ $event->end_ }}</label>
    <button type="button"> Request to Join </button>
    <p id="description">{{ $event->description }}</p>
</div>
@endsection