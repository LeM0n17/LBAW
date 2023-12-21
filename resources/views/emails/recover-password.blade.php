@extends('layouts.app')

@section('content')
<link href="{{url('css/login.css')}}" rel="stylesheet">
Hi {{ $mailData['name'] }},<br>
<br>
Forgot your password?<br>
We received a request to reset the password for your account.<br>
<br>
To reset your password, click on the button below.<br>
<a class="button button-outline" href="{{ route('resetPassword', ['token' => $mailData['token'], 'email' => $mailData['email'] ]) }}">Reset password</a>
<br>
Best regards,<br>
The Jammer Team
@endsection
