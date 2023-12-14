@extends('layouts.app')
@section('content')
<link href="{{ url('css/admin.css') }}" rel="stylesheet">
<section id="admintags">
    <h2>Tags</h2>
    <div class="spread">
        @each('partials.admintag', $tags, 'tag')
    </div>
</section>
@endsection