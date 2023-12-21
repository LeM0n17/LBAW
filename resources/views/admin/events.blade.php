@extends('layouts.app')
@section('content')
<link href="{{ url('css/admin.css') }}" rel="stylesheet">
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
<section id="adminevents">
    <h2>Events</h2>
    <div class="spread">
        @each('partials.adminevent', $events, 'event')
    </div>
</section>
@endsection