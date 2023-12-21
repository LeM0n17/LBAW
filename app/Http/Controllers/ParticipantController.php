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

    public function addParticipantFromRequest(Request $request)
    {
        $participant = new Participant();

        $userId = $request->route('id_user');

        $eventId = $request->route('id_event');

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

        return redirect()->to("/notifications");
    }

    public function refuseParticipantFromRequest(Request $request)
    {
        $notification_id = $request->route('id_notification');

        // Delete the notification
        $notification = Notifications::where('id', $notification_id)->first();

        if ($notification) {
            $notification->delete();
        }

        return redirect()->to("/notifications");
    }

    public function removeParticipant(Request $request)
    {
        $participantId = $request->route('id_participant');
        $eventId = $request->input('eventid');

        $participant = Participant::where('id_participant', $participantId)->where('id_event', $eventId)->delete();

        return redirect()->to("/events/{$eventId}")
            ->withSuccess('Participant removed successfully')
            ->withError('Participant not removed');
    }

    public function leaveEvent(Request $request)
    {
        $eventId = $request->route('id');
        $userId = Auth::id();

        $participant = Participant::where('id_participant', $userId)->where('id_event', $eventId)->delete();

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
