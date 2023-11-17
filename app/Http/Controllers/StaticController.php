<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Item;

class StaticController extends Controller
{
    public function showAboutUsPage()
    {
        return view("static.aboutus");
    }

    public function showFaqPage()
    {
        return view("static.faq");
    }

    public function showContactsPage()
    {
        return view("static.contacts");
    }
}
