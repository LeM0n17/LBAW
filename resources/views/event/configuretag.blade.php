@extends('layouts.app')

@section('content')
<link href="{{ url('css/event.css') }}" rel="stylesheet">
<form method="POST" action="{{ route('connectTag', ['event_id' => $event->id]) }}">
    <select name="tag_id" id="tag_id">

    </select>
    <button type="submit">Add</button>
</form>
<div id="currenttags">
    <h2>Tags:</h2>
    <div class="spread">
        
    </div>
</div>

@endsection
