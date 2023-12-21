<?php

namespace App\Http\Controllers;

use App\Mail\MailModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MailController extends Controller
{
    /**
     * Sends an email requesting the user to reset their password.
     */
    public function sendPasswordRecoveryEmail(Request $request) {
        $username = User::where('email', $request->email)->value('name');

        // verify if the user exists
        if ($username == NULL)
            return redirect()->route('showRecoverPassword')
                ->withErrors( 'That email does not belong to any user!');

        // generate a random token
        $token = Str::random();

        $mailData = array(
            'name' => $username,
            'email' => $request->email,
            'subject' => 'Recover your password!',
            'token' => $token,
        );

        Mail::to($request->email)->send(new MailModel($mailData));
        return redirect()->route('login')
                ->withSuccess('A password reset link has been sent to your email!');
    }
}
