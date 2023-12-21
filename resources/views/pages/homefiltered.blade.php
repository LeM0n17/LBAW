@extends('layouts.app')

@section('content')
    @include('partials.filters', $tags)
    <link href="{{ url('css/home.css') }}" rel="stylesheet">
    <section id="filterResults" class="homesection">
        <h2 class="title">Filtered Events</h2>
        <div class="sidescroller">
            @each('partials.eventcard', $events, 'event')
        </div>
    </section>

    <a href="/createevents"><button id="createevent">+</button></a>
@endsection
