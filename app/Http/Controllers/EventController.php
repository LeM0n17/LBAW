<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

use App\Models\Events;
use App\Models\Notifications;
use App\Models\User;

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
        return view('pages.events', [
            'event' => $event
        ]);
    }

    public function showEditEvents(string $id): View 
    {
        // Get the card.
        $event = Events::findOrFail($id);

        // Use the pages.card template to display the card.
        return view('pages.editevents', [
            'event' => $event
        ]);
    }

    public function showCreateEvents(): View
    {
        return view('pages.createevents');
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
            $cancellednotifications = Notifications::where('id_developer', Auth::user()->id)
            ->where('type', 'cancellation')
            ->get();

            $requestnotifications = Auth::user()
            ->notification()
            ->where('type', 'request')
            ->whereHas('event', function ($query) {
                $query->where('id_host', Auth::id());
            })
            ->orderBy('id')
            ->get();

            // The current user is authorized to list notifications.

            // Use the pages.events template to display all notifications.
            return view("pages.notifications", ['requestnotifications' => $requestnotifications, 'cancellednotifications' => $cancellednotifications]);
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

    public function requestToJoin(Request $request)
    {
        $notification = new Notifications();

        $user_id = $request->route('user_id');
        $event_id = $request->route('event_id');

        Log::info("User ID: $user_id, Event ID: $event_id");

        $user = User::where('id', $user_id)->first();

        $notification->fill([
            'id_developer' => $user->id,
            'id_event' => $event_id,
            'type' => 'request',
            'content' => 'asking to join',
            'time' => date("Y-m-d H:i:s")
        ]);

        $notification->save();

        return redirect()->to("/events/{$event_id}")
            ->withSuccess('Request Successfull!');
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

    public function createCancelledNotificationsForEvent($event_id)
    {
        $event = Events::find($event_id);
        $participants = $event->participants;

        if ($participants->isEmpty()) {
            return false;
        }

        foreach ($participants as $participant) {
            $notification = new Notifications();

            $notification->fill([
                'id_developer' => $participant->id_participant,
                'id_event' => $event->id,
                'type' => 'cancellation',
                'content' => 'Event has been cancelled',
                'time' => date("Y-m-d H:i:s")
            ]);

            $notification->save();
        }

        return true;
}
 
    public function cancelEvent($eventId)
    {
        $event = Events::find($eventId);

        if ($this->createCancelledNotificationsForEvent($event->id)) {
            $event->name .= " - Cancelled";
            $event->save();
            return redirect()->to("/events/{$eventId}")
                ->withSuccess('Event title updated!');
        } else {
            return redirect()->to("home")
                ->withSuccess('Error cancelling event!');
        }
    }
}
