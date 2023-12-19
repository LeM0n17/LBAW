@extends('layouts.app')

@section('content')
    <link href="{{url('css/login.css')}}" rel="stylesheet">
    <form method="POST" action="{{ route('resetPassword', ['token' => $token, 'email' => $email]) }}">
        {{ csrf_field() }}

        Please input your new password.

        <label for="password">Password</label>
        <input id="password" type="password" name="password" required autofocus>

        <label for="confirmPassword">Confirm password</label>
        <input id="confirmPassword" type="password" name="confirmPassword" required autofocus>

        @if ($errors)
            <span class="error">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </span>
        @endif

        <button type="submit">
            Submit
        </button>
    </form>
@endsection
