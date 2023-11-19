@extends('layouts.app')

@section('content')
<link href="{{ url('css/event.css') }}" rel="stylesheet">
<div class="eventinfo">
    <h2 id = "title"><a href="/editevents/{{ $event->id }}">{{ $event->name }}</a></h2>
    <label id="creator">By <b>{{ $event->host->name }}</b></label>
    <label id="duration">{{ $event->start }} - {{ $event->end_ }}</label>
    <button type="button"> Request to Join </button>
    <p id="description">{{ $event->description }}</p>
</div>
@endsection