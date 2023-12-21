<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\MailModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RecoverPasswordController extends Controller
{
    /**
     * Show the form for recovering the password.
     */
    public function showRecoverPasswordForm() {
        return view('auth/recover-password');
    }

    /**
     * Show the form for resetting the password.
     */
    public function showResetPasswordForm(Request $request) {
        return view('auth/reset-password',
            ['token' => $request->token, 'email' => $request->email]);
    }

    /**
     * Recover password.
     */
    public function resetPassword(Request $request) {
        // verify if the user exists
        $user = User::where('email', $request->email)->first();

        if (!$user)
            return redirect()->route('login')
                ->withErrors("Invalid email!");

        // verify if the password has at least 8 characters
        if (strlen($request->password) < 8)
            return redirect()
                ->route('showResetPassword', ['token' => $request->token, 'email' => $request->email])
                ->withErrors("The password must have at least 8 characters!");

        // verify if the passwords match
        if ($request->password != $request->confirmPassword)
            return redirect()
                ->route('showResetPassword', ['token' => $request->token, 'email' => $request->email])
                ->withErrors("The passwords do not match!");

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('login')
            ->withSuccess("Your password has successfully been changed!");
    }
}