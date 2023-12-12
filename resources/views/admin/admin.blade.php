@extends('layouts.app')
@section('content')
<link href="{{ url('css/admin.css') }}" rel="stylesheet">
<div class="buttonholder">
    <a href="/admin/event"><button class="redirect">Events</button></a>
    <a href="/admin/user"><button class="redirect">Users</button></a>
    <a href="/admin/tag"><button class="redirect">Tags</button></a>
</div>

@endsection