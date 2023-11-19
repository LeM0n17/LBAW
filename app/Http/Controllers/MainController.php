<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

use App\Models\Card;

class MainController extends Controller
{
    public function showHomePage()
    {
        return view("pages.home");
    }
}
