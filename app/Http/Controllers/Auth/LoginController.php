<?php
 
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LoginController extends Controller
{

    /**
     * Display a login form.
     */
    public function showLoginForm()
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect('/admin');
        } elseif (Auth::check()) {
            return redirect('/home');
        } else {
            return view('auth.login');
        }
    }

    /**
     * Handle an authentication attempt.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
 
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->isAdmin()) {
                return redirect()->intended('/admin');
            }
 
            return redirect()->intended('/home');
        }
 
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Log out the user from application.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')
            ->withSuccess('You have logged out successfully!');
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
     * Sends an email
     */
    public function sendEmail(Request $request) {
        $user = User::where('email', '=', $request->email)->first();

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
