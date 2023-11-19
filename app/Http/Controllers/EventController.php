<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

use App\Models\Events;

class EventController extends Controller
{
    /**
     * Get all the public events.
     */
    private function publicEvents() {
        return Events::where('types', 'public')->orderBy('id');
    }

    public function showEditEvents()
    {
        return view("pages.editevents");
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

        // Check if the current user can see (show) the card.
        $this->authorize('show', $event);  

        // Use the pages.card template to display the card.
        return view('pages.editevents', [
            'event' => $event
        ]);
    }

    public function showCreateEvents(string $id): View
    {
        // Get the card.
        $event = Events::findOrFail($id);
        // Check if the current user can see (show) the card.
        $this->authorize('show', $event);  
        // Use the pages.card template to display the card.
        return view('pages.createevents', [
            'event' => $event
        ]);
    }

    /**
     * Shows all events.
     */
    public function list() {
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');

        } else {
            // The user is logged in.

            // Get events for user ordered by id.
            $this->authorize('list', Events::class);

            // Retrieve events for the user ordered by ID.
            $events = $this->publicEvents()->get();

            // The current user is authorized to list events.

            // Use the pages.events template to display all events.
            return view('pages.home', ['events' => $events]);
        }
    }

    /**
     * Creates a new card.
     */
    public function create(Request $request)
    {
        // Create a blank new Card.
        $event = new Events();

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
    public function delete(Request $request, $id)
    {
        // Find the card.
        $event = Events::find($id);

        // Delete the card and return it as JSON.
        $event->delete();
        return redirect()->to("/home")
            ->withSuccess('Event deleted!')
            ->withErrors('Error');
    }

    /**
     * Perform a full-text search on the events.
     */
    public function search(String $input) {
        $events = Auth::user()->isAdmin() ?
                    // if the user is an administrator, search all events
                    Events::whereRaw("tsvectors @@ to_tsquery(?)", [$input])
                        ->orderByRaw("ts_rank(tsvectors, to_tsquery(?)) ASC", [$input])
                        ->get() :

                    // if the user is NOT an administrator, search public events
                    $this->publicEvents()
                        ->whereRaw("tsvectors @@ to_tsquery(?)", [$input])
                        ->orderByRaw("ts_rank(tsvectors, to_tsquery(?)) ASC", [$input])
                        ->get();

        return view('pages.home', ['events' => $events]);
    }

    public function editEvents(Request $request, $id)
    {
        // Find the card.
        $id = $request->route('id');
        $event = Events::findorFail($id);

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

}
