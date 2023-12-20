@extends('layouts.app')

@section('content')
<link href="{{ url('css/notifications.css') }}" rel="stylesheet">
<section id="requestNotification" class="notificationsection">
    <h2 class="title">Notifications</h2>
    <div class="sidescroller">
        @each('partials.notificationcard', $requestnotifications, 'notification')
    </div>
</section>
<section id="CancelledNotification" class="notificationsection">
    <h2 class="title">CancelledNotifications</h2>
    <div class="sidescroller">
        @each('partials.notificationcard', $cancellednotifications, 'notification')
    </div>
</section>

@endsection