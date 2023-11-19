@extends('layouts.app')

@section('content')
    <link href="{{ url('css/profile.css') }}" rel="stylesheet">
    
    <div class="main">
        <form method="POST" action="{{ route('editprofile') }}">
        {{ csrf_field() }}
            <div>
                <a class="button" href="{{ url('/profile') }}">Cancel</a>
                <button type="submit">Save</button>
            </div>
            <br>
            <div class="fa-regular fa-user fa-2xl profile-pic"></div>
                <label for="name">Username:</label>
                <input type="text" id="name" name="name" value="{{ Auth::user()->name }}"><br><br>
                <label for="email">Email:</label>
                <input type="text" id="email" name="email" value="{{ Auth::user()->email }}"><br><br>
        </form>
        
    </div>
@endsection