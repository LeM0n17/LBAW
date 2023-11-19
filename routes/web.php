<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\EventController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

use App\Http\Controllers\StaticController;
use App\Http\Controllers\MainController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home
Route::redirect('/', '/login');


// Authentication
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'authenticate');
    Route::get('/logout', 'logout')->name('logout');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
});

// Static
Route::controller(StaticController::class)->group(function () {
    Route::get('/aboutus', "showAboutUsPage");
    Route::get('/faq', "showFaqPage");
    Route::get('/contacts', "showContactsPage");
});

// Main
Route::controller(MainController::class)->group(function () {
    Route::get('/home', "showHomePage");
});