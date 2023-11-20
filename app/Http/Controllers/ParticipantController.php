<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Participant;
use App\Models\Events;
use Illuminate\View\View;

class ParticipantController extends Controller
{
    public function addParticipants(Request $request)
    {
        $participant = new Participant();

        $userId = Auth::id();
        $user = User::findOrFail($userId);

        $eventId = $request->route('id');

        $participant->fill([
            'id_participant' => $user->id,
            '$id_event' => $eventId
        ]);

        $user->save();

        return redirect()->intended('/manageparticipants');
    }

    public function removeParticipant(Request $request)
    {
        $participantId = $request->route('id_participant');
        $eventId = $request->route('id_event');

        $participant = Participant::where('id_participant', $participantId)->where('id_event', $eventId)->firstOrFail();

        $participant->delete();

        return redirect()->intended('/manageparticipants');
    }
    public function showManageParticipants(string $eventId): View 
    {
        $event = Events::findOrFail($eventId);
        $participants = Participant::where('id_event', $event->id)->get();
        // Get the card.
        $event = Events::findOrFail($eventId);
        // Check if the current user can see (show) the card.
        $this->authorize('show', $event);  
        // Use the pages.card template to display the card.
        return view('pages.manageparticipants', [
            'event' => $event,
            'participants' => $participants
        ]);
    }
}
