@extends('layouts.app')

@section('content')
<link href="{{ url('css/event.css') }}" rel="stylesheet">
<div class="editevent">
    <h2>Edit event '{{ $event->name }}'</h2>
    <form method="POST" action="{{ route('editevents', ['id' => $event->id]) }}">
    {{ csrf_field() }}
            <div>
                <a class="button" href="{{ url('/events') }}">Cancel</a>
                <button type="submit">Save</button>
            </div>
            <br>
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="{{ $event->name }}"><br><br>
            <label for="startdate">Start:</label>
            <input type="datetime-local" id="startdate" name="startdate" value="{{ $event->start }}"><br><br>
            <label for="enddate">End:</label>
            <input type="datetime-local" id="enddate" name="enddate" value="{{ $event->end_ }}"><br><br>
            <label for="privacy">Privacy:</label>
            <select id="privacy" name="privacy" value="{{ $event->type }}">
                <option value="public">Public</option>
                <option value="private">Private</option>
                <option value="protected">Protected</option>
            </select><br><br>
            <label for="description">Description:</label>
            <textarea id="description" name="description">{{ $event->description }}</textarea><br><br>
    </form>
</div>
@endsection