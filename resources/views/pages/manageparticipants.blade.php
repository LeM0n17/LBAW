@extends('layouts.app')

@section('content')
<link href="{{ url('css/event.css') }}" rel="stylesheet">
<form id="search-bar" method="POST" action="{{ route('invitetoevent', ['id' => $event->id]) }}">
    {{ csrf_field() }}
    <input type="text" placeholder="Email to invite..." id="search-field" id="email" name="email">
    <button type="submit">Invite</button>
</form>
<div id="invitationlist">
    @each('partials.participant', $participants, 'participant', $event, 'event')
</div>
@endsection
