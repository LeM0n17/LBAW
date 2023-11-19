@extends('layouts.app')

@section('content')
<div class="eventinfo">
    <h2>{{ $event->name }}</h2>
    <label>By {{ $event->id_host }}</label>
    <label>{{ $event->start }} - {{ $event->end_ }}</label>
    <button type="button"> Request to Join </button>
</div>
@endsection