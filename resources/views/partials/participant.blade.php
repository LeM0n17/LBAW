<form class="invitation" id="{{ $participant->id }}">
    <label id="username"><strong>{{ $participant->participant->name }}</strong></label>
    <form method="POST" action="{{ route('removeParticipant', ['id_participant' => $participant->participant->id, 'id_event' => $participant->event->id]) }}">
        <button type="submit">Kick</button>
    </form>
</form>