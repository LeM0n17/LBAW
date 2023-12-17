@extends('layouts.app')
@section('content')
<link href="{{ url('css/admin.css') }}" rel="stylesheet">
<form method="POST" action="{{ route('createTag') }}"> 
    {{ csrf_field() }}
    <h2>Create Tag:</h2>
    <input type="text" name="tagname" id="tagname">
    <button type="submit">Create</button>
</form>
<section id="admintags">
    <h2>Tags</h2>
    <div class="spread">
        @each('partials.admintag', $tags, 'tag')
    </div>
</section>
@endsection