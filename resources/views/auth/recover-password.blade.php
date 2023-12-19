@extends('layouts.app')

@section('content')
    <link href="{{url('css/login.css')}}" rel="stylesheet">
    <form method="POST" action="{{ route('sendPasswordRecoveryEmail') }}">
        {{ csrf_field() }}

        Please input the e-mail you registered with.

        <label for="email">E-mail</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>

        @if ($errors)
            <span class="error">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </span>
        @endif

        <button type="submit">
            Send
        </button>
    </form>
@endsection
