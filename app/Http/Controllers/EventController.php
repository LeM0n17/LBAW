<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\Events;
use App\Models\Tag;
use App\Models\Notifications;
use App\Models\User;
use App\Models\TagConnection;
use App\Models\File;

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
            return view('pages.home', ['running_events' => $running_events, 'upcoming_events' => $upcoming_events, 'finished_events' => $finished_events, 'tags' => Tag::all()]);
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
     * Delete an event.
     */
    public function delete(Request $request)
    {
        // Find the card.
        $id = $request->route('id');
        $event = Events::find($id);

        $this->authorize('delete', $event);  

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
            $this->authorize('list', Notifications::class);

            // Retrieve notifications for the user ordered by ID.
            $cancellednotifications = Notifications::where('id_developer', Auth::user()->id)
            ->where('type', 'cancellation')
            ->get();

            $requestnotifications = Auth::user()
            ->requestNotifications()
            ->get();

            $invitationnotifications = Auth::user()
            ->invitationNotifications()->get();

            // The current user is authorized to list notifications.

            // Use the pages.events template to display all notifications.
            return view("pages.notifications", ['requestnotifications' => $requestnotifications, 'cancellednotifications' => $cancellednotifications, 'invitationnotifications' => $invitationnotifications]);
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
            return redirect()->to("/participants/{$id}")
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $user = User::where('email', $request->input('email'))->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return redirect()->to("/participants/{$id}")
                ->withErrors(['email' => 'No user found with this email'])
                ->withInput();
        }
        

        $notification->fill([
            'id_developer' => $user->id,
            'id_event' => $id,
            'type' => 'invitation',
            'content' => 'You received an invite to join',
            'time' => date("Y-m-d H:i:s")
        ]);

        $notification->save();

        return redirect()->to("/participants/{$id}")
            ->withSuccess('User has been invited!')
            ->withErrors('Participant doesnt exist!');
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
            'content' => 'wants to join',
            'time' => date("Y-m-d H:i:s")
        ]);

        $notification->save();
        Log::info("notification type: $notification->type,");

        return redirect()->to("/events/{$event_id}")
            ->withSuccess('Request sent successfully!');
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
        $alltags = Tag::all();
        $filteredtags = $alltags->reject(function($element) use ($id){
            return TagConnection::where('id_event', $id)->where('id_tag', $element->id)->count() > 0;
        });

        return view("event.configuretag", ['tags' => $tags, 'event' => $event, 'alltags' => $filteredtags]);
    }

    public function connectTag(Request $request)
    {
        $tag_id = $request->tag_id;
        $event_id = $request->event_id;

        $tag = Tag::findorFail($tag_id);
        $event = Events::findorFail($event_id);

        if (TagConnection::where('id_event', $event_id)->where('id_tag', $tag_id)->count() <= 0) {
            $this->authorize('editEvents', $event);

            TagConnection::create([
                'id_event' => $event_id,
                'id_tag' => $tag_id,
            ]);
        }

        return redirect()->to("/tagconfig/{$event_id}")
            ->withSuccess('Tag connected!')
            ->withErrors('Error');
    }

    public function disconnectTag(Request $request)
    {
        $tag_id = $request->tag_id;
        $event_id = $request->event_id;

        $event = Events::findorFail($event_id);

        $this->authorize('editEvents', $event);

        TagConnection::where('id_tag', $tag_id)->where('id_event', $event_id)->delete();
        return redirect()->to("/tagconfig/{$event_id}")
            ->withSuccess('Tag disconnected!')
            ->withErrors('Error');
    }

        public function createCancelledNotificationsForEvent($event_id)
    {
        $event = Events::find($event_id);
        $participants = $event->participants;

        if ($participants->isEmpty()) {
            return true;
        }

        foreach ($participants as $participant) {
            $notification = new Notifications();

            $notification->fill([
                'id_developer' => $participant->id_participant,
                'id_event' => $event->id,
                'type' => 'cancellation',
                'content' => 'has been cancelled',
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
                ->withSuccess('Event has been cancelled!');
        } else {
            return redirect()->to("/events/{$eventId}")
                ->withSuccess('Error cancelling event!');
        }
    }
    
    public function showSubmissions(string $id): View 
    {
        // Get the card.
        $event = Events::findOrFail($id);

        // Check if the current user can see (show) the card.
        $this->authorize('show', $event);  

        // Use the pages.card template to display the card.
        return view('pages.submissions', [
            'event' => $event
        ]);
    }

    public function filterByDate(Request $request)
    {
        $date = $request->input('date');

        if ($date != "")
        {
            $events = Events::where('start', '>', $date)
            ->get();
        }
        else
        {
            $events = Events::all();
        }

        return view('pages.homefiltered', [
            'events' => $events, 'tags' => Tag::all()
        ]);
    }

    public function filterByTags(Request $request)
    {
        $tags = $request->input('tags');

        $events = Events::whereHas('tags', function ($query) use ($tags) {
            if (!is_null($tags))
            {
                $query->whereIn('id_tag', $tags);
            }
        })->get();

        return view('pages.homefiltered', [
            'events' => $events, 'tags' => Tag::all()
        ]);
    }
}