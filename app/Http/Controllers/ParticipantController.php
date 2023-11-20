<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Participant;

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
}
