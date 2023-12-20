<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Item;
use App\Models\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

        $user->fill([
            'name' => 'Deleted User',
            'password' => "anon",
            'email' => 'anon'.$userId.'@anon.com'

        ]);

        $user->save();

        return redirect()->intended('/login');
    }

    public function saveEditProfileChanges(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:250',
            'email' => 'required|email|max:250|'. Rule::unique('users')->ignore($user->id),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if (!is_null($request->image))
        {
            $path = Storage::put("public/pfps", $request->file('image'));
            $whatIWant = substr($path, strpos($path, "/") + 1);

            $user->fill([
                'image' => $whatIWant
            ]);
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
