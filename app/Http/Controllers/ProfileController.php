<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Item;
use App\Models\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function showProfilePage()
    {
        return view("profile.profile");
    }

    public function showEditProfilePage()
    {
        return view("profile.editprofile");
    }

    public function saveEditProfileChanges(Request $request)
    {
        // Get current user
        $userId = Auth::id();
        $user = User::findOrFail($userId);

        // Validate the data submitted by user
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:250',
            'email' => 'required|email|max:220|'. Rule::unique('users')->ignore($user->id),
        ]);

        // if fails redirects back with errors
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Fill user model
        $user->fill([
            'name' => $request->name,
            'email' => $request->email
        ]);

        // Save user to database
        $user->save();

        return redirect()->intended('/profile')
            ->withSuccess('Profile updated!')
            ->withErrors('Error');
    }
}
