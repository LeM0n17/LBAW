@extends('layouts.app')

@section('content')
    <link href="{{ url('css/home.css') }}" rel="stylesheet">
    @include('partials.search-bar')
    <div id="searchResults">
        <section id="featured" class="homesection">
            <h2 class="title">Running Events</h2>
            <div class="sidescroller">
                @each('partials.eventcard', $running_events, 'event')
            </div>
        </section>

        <section id="featured" class="homesection">
            <h2 class="title">Upcoming Events</h2>
            <div class="sidescroller">
                @each('partials.eventcard', $upcoming_events, 'event')
            </div>
        </section>

        <section id="featured" class="homesection">
            <h2 class="title">Finished Events</h2>
            <div class="sidescroller">
                @each('partials.eventcard', $finished_events, 'event')
            </div>
        </section>
    </div>

    <a href="/createevents"><button id="createevent">+</button></a>
@endsection
