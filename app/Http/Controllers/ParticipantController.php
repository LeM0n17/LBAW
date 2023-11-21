<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Participant;
use App\Models\Events;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use App\Models\Notifications;

class ParticipantController extends Controller
{
    public function addParticipants(Request $request)
    {
        $participant = new Participant();

        $userId = Auth::id();

        $eventId = $request->route('id');

        $participant->fill([
            'id_participant' => $userId,
            'id_event' => $eventId
        ]);

        $participant->save();

        // Delete the notification
        $notification = Notifications::where('id_developer', $userId)
            ->where('id_event', $eventId)
            ->first();

        if ($notification) {
            $notification->delete();
        }

        return redirect()->to("/events/{$eventId}");
    }

    public function removeParticipant(Request $request)
    {
        $participantId = $request->route('id_participant');
        $eventId = $request->route('id_event');

        $participant = Participant::where('id_participant', $participantId)->where('id_event', $eventId)->firstOrFail();

        $participant->delete();

        return redirect()->to("/events/{$eventId}");
    }
    public function showManageParticipants(string $eventId): View 
    {
        $event = Events::findOrFail($eventId);
        $participants = Participant::where('id_event', $event->id)->get();

        $event = Events::findOrFail($eventId);

        $this->authorize('show', $event);  

        return view('pages.manageparticipants', [
            'event' => $event,
            'participants' => $participants
        ]);
    }
}
