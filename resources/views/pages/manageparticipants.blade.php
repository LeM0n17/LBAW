@extends('layouts.app')

@section('content')
<form id="search-bar" method="POST">
    <input type="text" placeholder="Invite..." id="search-field">
    <button type="submit">Invite</button>
</form>
<div id="invitationlist">
    @each("partials.participant", $participants, 'participant')
</div>
@endsection