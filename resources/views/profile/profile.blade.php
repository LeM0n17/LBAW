@extends('layouts.app')

@section('content')
    <link href="{{ url('css/profile.css') }}" rel="stylesheet">
    
    <div class="main">
        <form method="POST" action="{{ route('deleteprofile') }}">
        {{ csrf_field() }}
            <a class="button" href="{{ url('/editprofile') }}">Edit Profile</a>
            <button value="submit">Delete Account</button>
            <a class="button" href="{{ route('showMyEvents') }}">My Events</a> 
            <a class="button" href="{{ url('/logout') }}">Logout</a>
        </form>
        <br>
        @if (Auth::user()->image == "")
            <img src="{{URL::asset('/images/default_pfp.png')}}" height="150" width="150" style="padding: none;">
        @else
            <img src="{{URL::asset('storage/'.Auth::user()->image)}}" height="150" width="150" style="padding: none;">
        @endif
        <h2> {{ Auth::user()->name }}</h2>
        <h3> {{ Auth::user()->email }}</h3>
        
    </div>
@endsection