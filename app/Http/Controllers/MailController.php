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
        $user = User::where('email', '=', $request->email)->get();

        // verify if the user exists
        if ($user == NULL)
            return redirect()->route('home')
                ->with('error', 'That email does not belong to any user!');

        // generate a random token
        $token = Str::random();

        $mailData = array(
            'name' => $user->name,
            'token' => $token,
        );

        Mail::to($request->email)->send(new MailModel($mailData));

        $user->password = bcrypt($token);
        $user->save();

        return redirect()->route('home');
    }
}
