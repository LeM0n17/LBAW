@extends('layouts.app')

@section('content')
    <link href="{{ url('css/home.css') }}" rel="stylesheet">
    @include('partials.search-bar')
    <div id="searchResults">
        <section id="featured" class="homesection">
            <h2 class="title">Participating</h2>
            <div class="sidescroller">
                @each('partials.eventcard', $participatingEvents, 'event')
            </div>
            <h2 class="title">Hosting</h2>
            <div class="sidescroller">
                @each('partials.eventcard', $hostedEvents, 'event')
            </div>
        </section>
    </div>

    <a href="/createevents"><button id="createevent">+</button></a>
@endsection
