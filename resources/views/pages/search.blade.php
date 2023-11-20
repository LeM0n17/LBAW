@extends('layouts.app')

@section('content')
<link href="{{ url('css/home.css') }}" rel="stylesheet">
<div id="search-bar">
        <input type="text" placeholder="Search..." id="search-field">
        <button type="submit" onclick="location.href='/search/'+ document.getElementById('search-field').value">Search</button>
</div>
<section id="featured" class="homesection">
    <h2 class="title">Featured Events</h2>
    <div class="sidescroller">
        @each('partials.eventcard', $events, 'event')
    </div>
</section>

<a href="/createevents"><button id="createevent">+</button></a>
@endsection
