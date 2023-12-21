@extends('layouts.app')

@section('content')
    <link href="{{ url('css/profile.css') }}" rel="stylesheet">
    
    <div class="main">
        <form method="POST" action="{{ route('editprofile') }}" enctype="multipart/form-data">
        {{ csrf_field() }}
            <div>
                <a class="button" href="{{ url('/profile') }}">Cancel</a>
                <button type="submit">Save</button>
            </div>
            <br>
                @if (Auth::user()->image == "")
                    <img alt="Profile Picture" src="{{URL::asset('/images/default_pfp.png')}}" height="150" width="150" style="padding: none;">
                @else
                    <img alt="Profile Picture" src="{{URL::asset('storage/'.Auth::user()->image)}}" height="150" width="150" style="padding: none;">
                @endif
                <br>
                <label for="image">Profile Picture:</label>
                <input id="image" type="file" enctype="multipart/form-data" name="image" src="" alt="Submit Image" accept="image/png, image/jpeg" width="100%" height="48">
                <br>
                <label for="name">Username:</label>
                <input type="text" id="name" name="name" value="{{ Auth::user()->name }}"><br><br>
                <label for="email">Email:</label>
                <input type="text" id="email" name="email" value="{{ Auth::user()->email }}"><br><br>
        </form>
        
    </div>
@endsection