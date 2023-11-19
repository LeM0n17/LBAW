@extends('layouts.app')

@section('content')
<link href="{{ url('css/home.css') }}" rel="stylesheet">
<div id="search-bar">
    <form action="">
        <input type="text" placeholder="Search..." name="q">
        <button type="submit">Submit</button>
    </form>
</div>
<section id="featured" class="homesection">
    <h2 class="title">Featured Events</h2>
    <div class="sidescroller">
        @each('partials.eventcard', $events, 'event')
    </div>
</section>

<button id="createevent"><a href="/createevents">+</a></button>
@endsection
