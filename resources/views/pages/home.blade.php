@extends('layouts.app')

@section('content')
<link href="{{ url('css/home.css') }}" rel="stylesheet">
<section id="featured" class="homesection">
    <h2 class="title">Featured Events</h2>
    <div class="sidescroller">
        @each('partials.eventcard', $events, 'event')
    </div>
</section>

@endsection