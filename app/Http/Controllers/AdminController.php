<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\View\View;

use App\Models\Events;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    private function getEvents() {
        return Events::all();
    }

    public function showAdminPage():View
    {
        // Retrieve events for the user ordered by ID.
        $events = $this->getEvents(); 
        $users = User::where('name','!=','Deleted User')->get();

        $this->authorize('showAdminPage', Auth::user());

        return view('admin.admin');
    }

    public function showAdminTagsPage():View 
    {
        $tag = Tag::all();
        $this->authorize('showAdminPage', Auth::user());

        return view('admin.tags', [
            'tags' => $tag
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

        return redirect()->to('/admin/user');
    }

    public function deleteEvent(Request $request)
    {
        // Find the card.
        $id = $request->route('id');
        $event = Events::find($id);

        //$this->authorize('delete', $user);

        // Delete the card and return it as JSON.
        $event->delete();
        return redirect()->to("/admin/event")
            ->withSuccess('Event deleted!')
            ->withErrors('Error');
    }

    public function deleteTag(Request $request)
    {
        $id = $request->route('id');
        $tag = Tag::find($id);

        $tag->delete();
        return redirect()->to('/admin/tag')
            ->withSuccess('Tag deleted!')
            ->withErrors('Error');
    }
}