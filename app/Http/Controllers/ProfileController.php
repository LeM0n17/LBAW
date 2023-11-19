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

    public function deleteProfile(Request $request)
    {
        $userId = Auth::id();
        $user = User::findOrFail($userId);

        Auth::logout();

        $user->delete();

        return redirect()->intended('/login');
    }

    public function saveEditProfileChanges(Request $request)
    {
        $userId = Auth::id();
        $user = User::findOrFail($userId);

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:250',
            'email' => 'required|email|max:250|'. Rule::unique('users')->ignore($user->id),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user->fill([
            'name' => $request->name,
            'email' => $request->email
        ]);

        $user->save();

        return redirect()->intended('/profile')
            ->withSuccess('Profile updated!')
            ->withErrors('Error');
    }
}
