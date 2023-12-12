<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\View\View;

use App\Models\Events;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    private function publicEvents() {
        return Events::where('types', 'public')->orderBy('id');
    }

    public function showAdminPage():View
    {
        // Retrieve events for the user ordered by ID.
        $events = $this->publicEvents()->get(); 
        $users = User::all();

        $this->authorize('showAdminPage', Auth::user());

        return view('admin.admin', [
            'events' => $events,
            'users' => $users
        ]);
    }

    public function showAdminUsersPage():View
    {
        $users = User::all();
        $this->authorize('showAdminPage', Auth::user());

        return view('admin.users', [
            'users' => $users
        ]);
    }

    public function showAdminEventsPage():View
    {
        $events = $this->publicEvents()->get();
        $this->authorize('showAdminPage', Auth::user());

        return view('admin.events', [
            'events' => $events
        ]);
    }

    public function deleteUser(Request $request)
    {
        Log::info('AdminController::deleteUser');
        $userId = $request->route('id');
        $user = User::findOrFail($userId);

        //$this->authorize('deleteUser', $user);

        $user->fill([
            'name' => 'Deleted User',
            'password' => "anon",
            'email' => 'anon'.$userId.'@anon.com'

        ]);

        $user->save();

        return redirect()->to('/admin');
    }

    public function deleteEvent(Request $request)
    {
        // Find the card.
        $id = $request->route('id');
        $event = Events::find($id);

        //$this->authorize('delete', $user);

        // Delete the card and return it as JSON.
        $event->delete();
        return redirect()->to("/admin")
            ->withSuccess('Event deleted!')
            ->withErrors('Error');
    }
}