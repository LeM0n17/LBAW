@extends('layouts.app')

@section('content')
<section id="cards">
    <div class="sidescroller">
        @each('partials.eventcard', $events, 'event')
    </div>
</section>
@endsection