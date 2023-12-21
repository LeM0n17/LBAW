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
        $users = User::whereDoesntHave('admin')->get();
        $this->authorize('showAdminPage', Auth::user());

        return view('admin.users', [
            'users' => $users
        ]);
    }

    public function showAdminEventsPage():View
    {
        $events = Events::all();
        $this->authorize('showAdminPage', Auth::user());

        return view('admin.events', [
            'events' => $events
        ]);
    }

    public function deleteUser(Request $request)
    {
        Log::info('AdminController::deleteUser');
        $userId = $request->route('id');
        $userToDelete = User::findOrFail($userId);

        $this->authorize('deleteUser', Auth::user());

        $userToDelete->fill([
            'name' => 'Deleted User',
            'password' => "anon",
            'email' => 'anon'.$userId.'@anon.com'
        ]);

        $userToDelete->save();

        return redirect()->to('/admin/user')
            ->withSuccess('User Banned with success!')
            ->withErrors('Error');
    }

    public function deleteEvent(Request $request)
    {
        $id = $request->route('id');
        $event = Events::find($id);

        $this->authorize('deleteEvent', Auth::user());

        $event->delete();
        return redirect()->to("/admin/event")
            ->withSuccess('Event deleted!')
            ->withErrors('Error');
    }

    public function deleteTag(Request $request)
    {
        $id = $request->route('id');
        $tag = Tag::find($id);

        $this->authorize('deleteTag', Auth::user());

        $tag->delete();
        return redirect()->to('/admin/tag')
            ->withSuccess('Tag deleted!')
            ->withErrors('Error');
    }

    public function createTag(Request $request) 
    {
        $this->authorize('createTag', Auth::user());
        Tag::create([
            'name' => $request->tagname
        ]);

        return redirect()->to('/admin/tag')
            ->withSuccess('Tag created!')
            ->withErrors('Error');
    }
}