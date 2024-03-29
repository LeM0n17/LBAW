<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

use App\Models\User;

class RegisterController extends Controller
{
    /**
     * Display a login form.
     */
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        $credentials_name = $request->only('name');
        if ($credentials_name["name"] == "Deleted User")
        {
            return redirect()->intended('/register')
            ->withErrors('Chosen name is not allowed');
        }

        $request->validate([
            'name' => 'required|string|max:250',
            'email' => 'required|email|max:250|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $credentials = $request->only('email', 'password');
        Auth::attempt($credentials);

        if (!is_null($request->image))
        {
            $path = Storage::put("public/pfps", $request->file('image'));
            $whatIWant = substr($path, strpos($path, "/") + 1);

            Auth::user()->fill([
                'image' => $whatIWant
            ]);
    
            Auth::user()->save();
        }

        $request->session()->regenerate();

        return redirect()->intended('/home')
            ->withSuccess('You have successfully registered & logged in!');
    }
}
