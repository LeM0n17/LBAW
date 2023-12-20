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
    <form action="{{ route('createcomment', ['id' => $event->id]) }}" method="POST">
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
            <label for="content">New Comment(participants only):</label>
            <textarea name="content" id="content" placeholder="Enter your comment here..."></textarea>
        </div>
        <button type="submit">Add Comment</button>
    </form>
</div>
@endsection