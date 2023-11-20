@extends('layouts.app')

@section('content')
<link href="{{ url('css/home.css') }}" rel="stylesheet">
<div id="search-bar">
    <form method="get" action="{{ route('search') }}">
        @csrf
        <input type="text" placeholder="Search..." name="search">
        <button type="submit">Search</button>
    </form>
</div>
<section id="featured" class="homesection">
    <h2 class="title">Featured Events</h2>
    <div class="sidescroller">
        @each('partials.eventcard', $events, 'event')
    </div>
</section>

<a href="/createevents"><button id="createevent">+</button></a>
@endsection
