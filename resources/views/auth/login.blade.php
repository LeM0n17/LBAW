@extends('layouts.app')

@section('content')
<link href="{{url('css/login.css')}}" rel="stylesheet">
<form method="POST" action="{{ route('login') }}">
    {{ csrf_field() }}

    <label for="email">E-mail</label>
    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>

    <div id="password-labels">
        <a id="forgot-password" href="/recover-password">Forgot password?</a>
        <label for="password" >Password</label>
    </div>

    <input id="password" type="password" name="password" required>


    @if ($errors)
        <span class="error">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </span>
    @endif

    <label>
        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
    </label>

    <button type="submit">
        Login
    </button>
    <a class="button button-outline" href="{{ route('register') }}">Register</a>
    @if (session('success'))
        <p class="success">
            {{ session('success') }}
        </p>
    @endif
</form>
@endsection