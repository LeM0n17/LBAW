@extends('layouts.app')

@section('content')
<link href="{{ url('css/notifications.css') }}" rel="stylesheet">
<section id="requestNotification" class="notificationsection">
    <h2 class="title">Request Notifications</h2>
    <div class="sidescroller">
        @each('partials.notificationcard', $requestnotifications, 'notification')
    </div>
</section>
<section id="CancelledNotification" class="notificationsection">
    <h2 class="title">Cancelled Notifications</h2>
    <div class="sidescroller">
        @each('partials.cancellednotification', $cancellednotifications, 'notification')
    </div>
</section>
<section id="InvitationNotification" class="notificationsection">
    <h2 class="title">Invite Notifications</h2>
    <div class="sidescroller">
        @each('partials.invitationnotification', $invitationnotifications, 'notification')
    </div>
</section>


@endsection