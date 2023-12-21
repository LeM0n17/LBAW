@extends('layouts.app')

@section('content')
<link href="{{ url('css/event.css') }}" rel="stylesheet">
<div class="eventinfo">
    <h2 id = "title">{{ $event->name }}</h2>
    <label id="creator">By <b>{{ $event->host->name }}</b></label>
    <label id="duration">{{ $event->start }} - {{ $event->end_ }}</label>

    <a href="{{ url('/events/'.strval($event->id)) }}"><button>Event Page</button></a>

    <p id="description">{{ $event->description }}</p>
</div>
<div class="commentsection">
    @each('partials.files', $event->files, 'file')
    <form action="{{ route('createfile', ['id' => $event->id]) }}" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <label for="name">New Submission(participants only):</label>
            <textarea name="name" id="name" placeholder="Enter your submission's name"></textarea>
            <input id="file" type="file" enctype="multipart/form-data" name="file" src="" alt="Submit File" width="100%" height="48">
        </div>
        <button type="submit">Submit</button>
    </form>
</div>
@endsection