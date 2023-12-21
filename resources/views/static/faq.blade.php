@extends('layouts.app')

@section('content')
    <link href="{{ url('css/static.css') }}" rel="stylesheet">
    
    <div class="FAQ">
        <h2>FAQ</h2>
        <div>How do I...</div>
        <div class="question">...create an event?</div>
        <div class="answer">Click on the "+" button on the botton right of the Home page. Fill out the form and click "Submit".</div>
        <div class="question">...edit an event?</div>
        <div class="answer">Click on the event you want to edit. Click on the "Configure" button. Edit the form and click "Save". Remember you can only edit an event you have created.</div>
        <div class="question">...delete an event?</div>
        <div class="answer">Only Admins can delete an event. Start by cancelling it and if necessary contact an admin to delete it for you</div>
        <div class="question">...cancel an event?</div>
        <div class="answer">Click on the event you want to cancel. Click on the "Cancel" button. Remember you can only cancel an event you have created.</div>
        <div class="question">...join an event?</div>
        <div class="answer">Click on the event you want to join. Click on the "Request To Join" button and wait for the answer of the host.</div>
    </div>
@endsection