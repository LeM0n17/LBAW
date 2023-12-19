<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

use App\Models\Events;
use App\Models\Tag;
use App\Models\Notifications;
use App\Models\User;
use App\Models\TagConnection;

class EventController extends Controller
{
    /**
     * Get all the public events.
     */
    private function getEvents() {
        return Events::where(function ($query)
        {
            $query->where("types", "public")
                ->orWhere("id_host","=", Auth::user()->id);
        });
    }

    /**
     * Show the event for a given id.
     */
    public function show(string $id): View 
    {
        // Get the card.
        $event = Events::findOrFail($id);

        // Check if the current user can see (show) the card.
        $this->authorize('show', $event);  

        // Use the pages.card template to display the card.
        return view('event.events', [
            'event' => $event
        ]);
    }

    public function showEditEvents(string $id): View 
    {
        // Get the card.
        $event = Events::findOrFail($id);

        // Use the pages.card template to display the card.
        return view('event.editevents', [
            'event' => $event
        ]);
    }

    public function showCreateEvents(): View
    {
        return view('event.createevents');
    }

    /**
     * Shows all events.
     */
    public function list() {
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');

        } elseif (Auth::user()->isAdmin()) {
            return redirect('/admin');
            
        } else {
            // The user is logged in.

            // Get events for user ordered by id.
            $this->authorize('list', Events::class);

            $events = $this->getEvents()->get();

            $mytime = Carbon::now();

            $running_events = $events->where("start", "<", $mytime->toDateTimeString())
                                ->where("end_",">", $mytime->toDateTimeString());
            $upcoming_events = $events->where("start", ">", $mytime->toDateTimeString());
            $finished_events = $events->where("end_","<", $mytime->toDateTimeString());

            // Use the pages.events template to display all events.
            return view('pages.home', ['running_events' => $running_events, 'upcoming_events' => $upcoming_events, 'finished_events' => $finished_events]);
        }
    }

    /**
     * Creates a new card.
     */
    public function create(Request $request)
    {
        // Create a blank new Card.
        $event = new Events();
        $this->authorize('create', $event);  

        // Check if the current user is authorized to edit this event.

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'startdate' => 'required|date|after:today',
            'enddate' => 'required|date|after:startdate',
            'privacy' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            Log::info('Validation failed: ' . $validator->errors());
            return redirect()->to("/createevents/{$event->id}")
                ->withErrors($validator)
                ->withInput();
        }

        $event->fill([
            'id_host' => Auth::id(),
            'name' => $request->input('title'),
            'start' => $request->input('startdate'),
            'end_' => $request->input('enddate'),
            'types' => $request->input('privacy'),
            'description' => $request->input('description'),
        ]);

        $event->save();
        return redirect()->to("/events/{$event->id}")
            ->withSuccess('Event created!')
            ->withErrors('Error');
    }

    /**
     * Delete a card.
     */
    public function delete(Request $request)
    {
        // Find the card.
        $id = $request->route('id');
        $event = Events::find($id);

        $this->authorize('delete', $event);  

        // Delete the card and return it as JSON.
        $event->delete();
        return redirect()->to("/home")
            ->withSuccess('Event deleted!')
            ->withErrors('Error');
    }

    /**
     * Perform a full-text search on the events.
     */
    public function search(Request $request) {
        if (!Auth::check())
            // Not logged in, redirect to login.
            return redirect('/login');

        $search = $request->input('search');

        $query = Auth::user()->isAdmin() ?
                    Events::select() : $this->getEvents();

        if (!empty($search))
            $query = $query->whereRaw('(events.name = ? OR events.tsvectors @@ to_tsquery(\'english\', ?))', [$search, $search])
                        ->orderByRaw('ts_rank(events.tsvectors, to_tsquery(\'english\', ?)) DESC', [$search]);

        if ($request->ajax()) {
            Log::info('Returning search_results view');
            return view('pages.search_results', ['events' => $query->get()]);
        } else {
            Log::info('Returning search view');
            return view('pages.search', ['events' => $query->get()]);
        }
    }

    public function editEvents(Request $request)
    {
        // Find the card.
        $id = $request->route('id');
        $event = Events::findorFail($id);

        $this->authorize('editEvents', $event);

        $rules = ['description' => 'required',];
        $rules['title'] = 'required';
        $rules['startdate'] = 'required|date|before:enddate';
        $rules['enddate'] = 'required|date|after:startdate';
        $rules['privacy'] = 'required';
        $rules['enddate'] = 'required|date|after:startdate';

        // Check if the current user is authorized to edit this event.

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Log::info('Validation failed: ' . $validator->errors());
            return redirect()->to("/editevents/{$event->id}")
                ->withErrors($validator)
                ->withInput();
        }

        $event->fill([
            'name' => $request->input('title'),
            'start' => $request->input('startdate'),
            'end_' => $request->input('enddate'),
            'types' => $request->input('privacy'),
            'description' => $request->input('description'),
        ]);

        $event->save();
        return redirect()->to("/events/{$event->id}")
            ->withSuccess('Events updated!')
            ->withErrors('Error');
    }

    public function showNotificationsPage()
    {
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');

        } else {
            // The user is logged in.

            // Get notifications for user ordered by id.
            $this->authorize('list', Notifications::class);

            // Retrieve notifications for the user ordered by ID.
            $notifications = Auth::user()->notification()->orderBy('id')->get();

            // The current user is authorized to list notifications.

            // Use the pages.events template to display all notifications.
            return view("pages.notifications", ['notifications' => $notifications]);
        }
    }

    public function inviteToEvent(Request $request, $id)
    {
        $notification = new Notifications();

        $validator = Validator::make($request->all(), [
            'email' => 'required'
        ]);

        if ($validator->fails()) {
            Log::info('Validation failed: ' . $validator->errors());
            return redirect()->to("/events/{$id}")
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::where('email', $request->input('email'))->first();

        $notification->fill([
            'id_developer' => $user->id,
            'id_event' => $id,
            'type' => 'invitation',
            'content' => 'please join',
            'time' => date("Y-m-d H:i:s")
        ]);

        $notification->save();

        return redirect()->to("/participants/{$id}")
            ->withSuccess('User invited!')
            ->withErrors('Error');
    }

    public function showUserEvents()
    {
        $userId = Auth::id();

        // Get the events where the user is participating
        $participatingEvents = Events::whereHas('participants', function ($query) use ($userId) {
            $query->where('id_participant', $userId);
        })->get();

        // Get the events where the user is hosting
        $hostingEvents = Events::where('id_host', $userId)->get();

        return view("pages.myevents", [
            'participatingEvents' => $participatingEvents,
            'hostedEvents' => $hostingEvents
        ]);
    }

    public function showTagConfigurationPage(Request $request)
    {
        $id = $request->route('id');
        $event = Events::findorFail($id);

        $this->authorize('editEvents', $event);

        $tags = $event->tags;

        return view("event.configuretag", ['tags' => $tags, 'event' => $event]);
    }

    public function connectTag(Request $request)
    {
        $tag_id = $request->tag_id;
        $event_id = $request->event_id;

        $tag = Tag::findorFail($tag_id);
        $event = Events::findorFail($event_id);

        $this->authorize('editEvents', $event);

        TagConnection::create([
            'id_event' => $event_id,
            'id_tag' => $tag_id,
        ]);

        return redirect()->to("/tagconfig/{$event_id}")
            ->withSuccess('Tag connected!')
            ->withErrors('Error');
    }

    public function disconnectTag(Request $request)
    {
        $tag_id = $request->tag_id;
        $event_id = $request->event_id;

        $event = Events::findorFail($event_id);
        $connection = TagConnection::where('id_tag', $tag_id)->where('id_event', $event_id)->firstOrFail();

        $this->authorize('editEvents', $event);

        $connection->delete();
        return redirect()->to("/tagconfig/{$event_id}")
            ->withSuccess('Tag disconnected!')
            ->withErrors('Error');
    }
}
