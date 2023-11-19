<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Models\Events;

class EventController extends Controller
{
    /**
     * Show the card for a given id.
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

    /**
     * Shows all events.
     */
    public function list(){
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');

        } else {
            // The user is logged in.

            // Get events for user ordered by id.
            $this->authorize('list', Events::class);

        // Retrieve events for the user ordered by ID.
            $events = Auth::user()->events()->orderBy('id')->get();

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

        // Check if the current user is authorized to create this card.
        $this->authorize('create', $event);

        // Set card details.
        $event->name = $request->input('name');
        $event->id_host = Auth::user()->id;

        // Save the card and return it as JSON.
        $event->save();
        return response()->json($event);
    }

    /**
     * Delete a card.
     */
    public function delete(Request $request, $id)
    {
        // Find the card.
        $event = Events::find($id);

        // Check if the current user is authorized to delete this card.
        $this->authorize('delete', $event);

        // Delete the card and return it as JSON.
        $event->delete();
        return response()->json($event);
    }

    public function editEvents(Request $request, $id)
    {
        // Find the card.
        $event = Events::findorFail($id);

        // Check if the current user is authorized to edit this event.
        $this->authorize('edit', $event);

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'end_' => 'required',
            'description' => 'required',
            'types' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('/events')
                        ->withErrors($validator)
                        ->withInput();
        }

        $event->fill([
            'name' => $request->input('name'),
            'end_' => $request->input('end_'),
            'description' => $request->input('description'),
            'types' => $request->input('types'),
        ]);

        $event->save();
        return redirect()->intended('/events')
            ->withSuccess('Events updated!')
            ->withErrors('Error');
    }
}
