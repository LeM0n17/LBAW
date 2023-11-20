<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\View\View;

use App\Models\Events;
use App\Models\User;

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

        return view('pages.admin', [
            'events' => $events,
            'users' => $users
        ]);
    }

    public function deleteUser(Request $request)
    {
        $userId = $request->route('id');
        $user = User::findOrFail($userId);
        $user->delete();

        return redirect()->intended('/admin');
    }

    public function deleteEvent(Request $request)
    {
        // Find the card.
        $id = $request->route('id');
        $event = Events::find($id);

        $this->authorize('delete', $event);  

        // Delete the card and return it as JSON.
        $event->delete();
        return redirect()->to("/admin")
            ->withSuccess('Event deleted!')
            ->withErrors('Error');
    }
}