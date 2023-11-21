<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EventController;
use App\Http\Controllers\AdminController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

use App\Http\Controllers\StaticController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ParticipantController;

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

// Admin
Route::controller(AdminController::class)->group(function () {
    Route::get("/admin", "showAdminPage")->name("showAdminPage");
    Route::post("/admin/user/{id}", "deleteUser")->name("deleteUser");
    Route::post("/admin/event/{id}", "deleteEvent")->name("deleteEvent");
});

// Events
Route::controller(EventController::class)->group(function () {
    Route::get('/home', 'list')->name('events');
    Route::get('/search', 'search')->name('search');
    Route::get('/events/{id}', 'show')->name('event');
    Route::get('/editevents/{id}', 'showEditEvents')->name('showeditevents');
    Route::get('/createevents', 'showCreateEvents')->name('showcreateevents');
    Route::post('/editevents/{id}', 'editevents')->name('editevents');
    Route::post('/createevents', 'create')->name('createevents');
    Route::post('/deleteevents/{id}', 'delete')->name('deleteevents');
    Route::post('/invitetoevent/{id}', 'inviteToEvent')->name('invitetoevent');
    Route::get('/notifications', 'showNotificationsPage')->name('showNotificationsPage');
});

// Participants
Route::controller(ParticipantController::class)->group(function () {
    Route::get('/participants/{id}','showManageParticipants')->name('showManageParticipants');
    Route::post('/home/{id}','addParticipants')->name('addHomeParticipant');
    Route::post('/events/{id}','addParticipants')->name('addParticipant');
    Route::post('/participants/remove/{id_participant}','removeParticipant')->name('removeParticipant');
});

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

// Static
Route::controller(ProfileController::class)->group(function () {
    Route::get('/profile', "showProfilePage");
    Route::post('/deleteprofile', "deleteProfile")->name("deleteprofile");
    Route::get('/editprofile', "showEditProfilePage")->name("editprofile");
    Route::post('/editprofile', "saveEditProfileChanges");
});
