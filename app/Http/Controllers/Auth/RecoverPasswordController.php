<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RecoverPasswordController extends Controller
{
    public function show() {
        return view('auth/recover-password');
    }

    /**
     * Recover password.
     */
    public function recoverPassword(Request $request) {

        $user = User::where('email', '=', $request->recoverAttemp)->first();
        if (!$user) return redirect()->route('login')->with('error', "Invalid email");

        if (Hash::check($request->recoverToken, $user->password)) {
            // verify if the passwords match
            if($request->recoverPassword1 != $request->recoverPassword2)
                return redirect()->route('login')->with('match_error', "Passwords don't match")
                    ->with('email_attemp', $request->recoverAttemp);

            // verify if the password has the right length
            if(strlen($request->recoverPassword1) < 8)
                return redirect()->route('login')->with('size_error', "Password must have at least 8 characters")
                    ->with('email_attemp', $request->recoverAttemp);

            $user->password = bcrypt($request->recoverPassword1);
            $user->save();

            return redirect()->route('login')->with('success', "Your password has been changed successfully");
        }
        return redirect()->route('login')->with('invalid_token', "Invalid token. Please try again.")
            ->with('email_attemp', $request->recoverAttemp);
    }

    /**
     * Sends an email requesting the user to reset their password.
     */
    public function sendEmail(Request $request) {
        $user = User::where('email', $request->email);

        // verify if the user exists
        if (!$user)
            return;

        // generate a random token
        $token = Str::random();

        $data = array('name' => $user->name,
            'token' => $token);

        Mail::send('partials.mail', $data, function($message) {
            $message->subject('Recover your password!');
            $message->from('OnlyFEUP@gmail.com','OnlyFEUP');
            $message->to('user@gmail.com', 'OnlyFEUP User');
        });

        $user->password = bcrypt($token);
        $user->save();
    }
}