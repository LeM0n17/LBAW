@extends('layouts.app')

@section('content')
<link href="{{ url('css/event.css') }}" rel="stylesheet">
<form id="search-bar" method="POST" action="{{ route('invitetoevent', ['id' => $event->id]) }}">
    {{ csrf_field() }}
    <input type="text" placeholder="Email to invite..." id="email" name="email">
    @if ($errors->has('email'))
        <span class="error">{{ $errors->first('email') }}</span>
    @endif
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <button type="submit">Invite</button>
</form>

<div id="invitationlist">
    @each('partials.participant', $participants, 'participant')
</div>
@endsection
