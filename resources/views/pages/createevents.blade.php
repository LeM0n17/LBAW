@extends('layouts.app')
@section('content')
<link href="{{ url('css/event.css') }}" rel="stylesheet">
<div class="editevent">
    <h2>New Event'</h2>
    <form method="POST" action="{{ route('createevents') }}">
    {{ csrf_field() }}
            <div>
                <a class="button" href="">Cancel</a>
                <button type="submit">Submit</button>
            </div>
            <br>
            <label for="title">Title:</label>
            <input type="text" id="title" name="title"><br><br>
            <label for="startdate">Start:</label>
            <input type="datetime-local" id="startdate" name="startdate"><br><br>
            <label for="enddate">End:</label>
            <input type="datetime-local" id="enddate" name="enddate"><br><br>
            <label for="privacy">Privacy:</label>
            <select id="privacy" name="privacy">
                <option value="public">Public</option>
                <option value="private">Private</option>
                <option value="protected">Protected</option>
            </select><br><br>
            <label for="description">Description:</label>
            <textarea id="description" name="description"></textarea><br><br>
    </form>
</div>
@endsection