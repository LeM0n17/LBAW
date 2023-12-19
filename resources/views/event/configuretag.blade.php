@extends('layouts.app')

@section('content')
<link href="{{ url('css/event.css') }}" rel="stylesheet">
<form method="POST" action="{{ route('') }}">
    <select name="newtag" id="newtag">

    </select>
    <button type="submit">Add</button>
</form>
<div id="currenttags">
    <h2>Tags:</h2>
    <div class="spread">
        
    </div>
</div>

@endsection
