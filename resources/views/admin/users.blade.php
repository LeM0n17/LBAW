@extends('layouts.app')
@section('content')
<link href="{{ url('css/admin.css') }}" rel="stylesheet">
<section id="adminusers">
    <h2>Users</h2>
    <div class="spread">
        @each('partials.user', $users, 'user')
    </div>
</section>
@endsection