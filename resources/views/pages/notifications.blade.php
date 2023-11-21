@extends('layouts.app')

@section('content')
<link href="{{ url('css/home.css') }}" rel="stylesheet">
<section id="featured" class="homesection">
    <h2 class="title">Notifications</h2>
    <div class="sidescroller">
        @each('partials.notificationcard', $notifications, 'notification')
    </div>
</section>

@endsection