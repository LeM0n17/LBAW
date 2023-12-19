@extends('layouts.app')

@section('content')
<link href="{{ url('css/event.css') }}" rel="stylesheet">
<form method="POST" action="{{ route('connectTag', ['event_id' => $event->id]) }}">
    <h3>Add tag:</h3>
    <select name="tag_id" id="tag_id">
        @each('partials.tagoption', $alltags, 'tag')
    </select>
    <button type="submit">Add</button>
</form>
<div id="currenttags">
    <h2>Current tags:</h2>
    <div class="spread">
        @each('partials.existingtag', $tags, 'tag')
    </div>
</div>

@endsection
