@extends('layouts.app')

@section('content')
    <link href="{{ url('css/profile.css') }}" rel="stylesheet">
    
    <div class="main">
        <form method="POST" action="{{ route('deleteprofile') }}">
        {{ csrf_field() }}
            <a class="button" href="{{ url('/editprofile') }}">Edit Profile</a>
            <button value="submit">Delete Account</button>
            <a class="button" href="{{ url('/logout') }}">Logout</a>
        </form>
        <br>
        <div class="fa-regular fa-user fa-2xl profile-pic"></div>
        <h2> {{ Auth::user()->name }}</h2>
        <h3> {{ Auth::user()->email }}</h3>
        
    </div>
@endsection