<?php

use App\Http\Controllers\Auth\RecoverPasswordController;
use App\Http\Controllers\MailController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EventController;
use App\Http\Controllers\AdminController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

use App\Http\Controllers\StaticController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\FilesController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PollController;

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
    Route::get("/admin/user", "showAdminUsersPage")->name("showAdminUsersPage");
    Route::get("/admin/event", "showAdminEventsPage")->name("showAdminEventsPage");
    Route::get("/admin/tag", "showAdminTagsPage")->name("showAdminTagsPage");
    Route::post("/admin/tag/delete/{id}", "deleteTag")->name("deleteTag");
    Route::post("/admin/tag/create", "createTag")->name("createTag");
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
    Route::get('/tagconfig/{id}', 'showTagConfigurationPage')->name('showTagConfigurationPage');
    Route::post('/editevents/{id}', 'editevents')->name('editevents');
    Route::post('/createevents', 'create')->name('createevents');
    Route::post('/deleteevents/{id}', 'delete')->name('deleteevents');
    Route::post('/invitetoevent/{id}', 'inviteToEvent')->name('invitetoevent');
    Route::post('/tagconfig/disconnect', 'disconnectTag')->name('disconnectTag');
    Route::post('/tagconfig/connect', 'connectTag')->name('connectTag');
    Route::get('/notifications', 'showNotificationsPage')->name('showNotificationsPage');
    Route::get('/myevents', 'showUserEvents')->name('showMyEvents');
    Route::post('/requesttojoin/{event_id}/{user_id}', 'requestToJoin')->name('requestToJoin');
    Route::post('/cancelevent/{event_id}', 'cancelEvent')->name('cancelevent');
    Route::get('/submissions/{id}', 'showSubmissions')->name('showSubmissions');
    Route::get('/home/filterByDate', 'filterByDate')->name('filterByDate');
    Route::get('/home/filterByTag', 'filterByTags')->name('filterByTags');
});

// Participants
Route::controller(ParticipantController::class)->group(function () {
    Route::get('/participants/{id}','showManageParticipants')->name('showManageParticipants');
    Route::post('/home/{id}','addParticipants')->name('addHomeParticipant');
    Route::post('/events/{id}','addParticipants')->name('addParticipant');
    Route::post('/notifications/accept/{id_event}/{id_user}','addParticipantFromRequest')->name('addParticipantFromRequest');
    Route::post('notifications/refuse/{id_notification}','refuseParticipantFromRequest')->name('refuseParticipantFromRequest');
    Route::post('/participants/remove/{id_participant}','removeParticipant')->name('removeParticipant');
    Route::delete('/event/{id}/leave', 'leaveEvent')->name('leaveEvent');
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

Route::controller(RecoverPasswordController::class)->group(function () {
    Route::get('/recover-password', 'showRecoverPasswordForm')->name('showRecoverPassword');
    Route::get('/reset-password/{token}', 'showResetPasswordForm')->name('showResetPassword');
    Route::post('/reset-password/{token}', 'resetPassword')->name('resetPassword');
});

// Emails
Route::controller(MailController::class)->group(function () {
    Route::post('/recover-password/send', 'sendPasswordRecoveryEmail')->name('sendPasswordRecoveryEmail');
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

//comments
Route::controller(CommentsController::class)->group(function () {
    Route::post('/events/{id}/comments', "createComment")->name("createcomment");
    Route::delete('/events/{id}/deletecomment', "deleteComment")->name("deletecomment");
});

//files
Route::controller(FilesController::class)->group(function () {
    Route::post('/events/{id}/createfile', "createFile")->name("createfile");
    Route::delete('/events/{id}/deletefile', "deleteFile")->name("deletefile");
    Route::post('/download/{id}', 'downloadFile')->name('downloadFile');
});

//likes
Route::controller(LikeController::class)->group(function () {
    Route::post('/file/{id_file}/like', "addLike")->name("addLike");
    Route::post('/file/{id_file}/dislike', "addDislike")->name("addDislike");
    Route::delete('/file/{id_file}/removelike', "deleteLike")->name("removeLike");
});

//polls
Route::controller(PollController::class)->group(function () {
    Route::get('polls/{id_event}', "showPolls")->name("showPolls");
    Route::post('polls/{id_option}/vote', "addVote")->name("vote");
    Route::post('polls/{id_poll}/addoption', "addOption")->name("addOption");
    Route::post('polls/{id_option}/removeoption', "removeOption")->name("removeOption");
    Route::post('polls/{id_poll}/removepoll', "removePoll")->name("deletePoll");
    Route::post('polls/{id_event}/createpoll', "createPoll")->name("createPoll");
    Route::post('polls/{id_option}/removevote', "removeVote")->name("removeVote");
});
