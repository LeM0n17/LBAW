@if (!Auth::user()->id == $participant->participant->id)
<div class="participant" id="{{ $participant->id }}">
    <label id="username"><strong>{{ $participant->participant->name }}</strong></label>
    <form method="POST" action="{{ route('removeParticipant', ['id_participant' => $participant->id_participant]) }}">
        {{ csrf_field() }}
        <input type="hidden" name="eventid" value="{{ $participant->event->id }}">
        <button type="submit">Remove</button>
    </form>
</div>
@endif