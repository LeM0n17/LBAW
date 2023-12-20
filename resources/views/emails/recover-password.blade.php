Hi {{ $mailData['name'] }},<br>
<br>
Forgot your password?<br>
We received a request to reset the password for your account.<br>
<br>
To reset your password, click on the button below.<br>
<br>
Or click <a href="{{ route('resetPassword', ['token' => $mailData['token'], 'email' => $mailData['email'] ]) }}">here</a>.<br>
<br>
Best regards,<br>
The Jammer Team
