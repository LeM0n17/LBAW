@extends('layouts.app')

@section('content')
    <link href="{{ url('css/home.css') }}" rel="stylesheet">
    @include('partials.search-bar')
    <div id="searchResults">
        @if(count($running_events) > 0)
            <section id="featured" class="homesection">
                <h2 class="title">Running Events</h2>
                <div class="sidescroller">
                    @each('partials.eventcard', $running_events, 'event')
                </div>
            </section>
        @endif

        @if(count($upcoming_events) > 0)
            <section id="featured" class="homesection">
                <h2 class="title">Upcoming Events</h2>
                <div class="sidescroller">
                    @each('partials.eventcard', $upcoming_events, 'event')
                </div>
            </section>
        @endif

        @if(count($finished_events) > 0)
            <section id="featured" class="homesection">
                <h2 class="title">Finished Events</h2>
                <div class="sidescroller">
                    @each('partials.eventcard', $finished_events, 'event')
                </div>
            </section>
        @endif
    </div>

    <a href="/createevents"><button id="createevent">+</button></a>
@endsection
