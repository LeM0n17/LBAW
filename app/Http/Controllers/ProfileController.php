<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Item;

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

}
