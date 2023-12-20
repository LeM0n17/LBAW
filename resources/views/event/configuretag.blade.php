@extends('layouts.app')

@section('content')
<link href="{{ url('css/event.css') }}" rel="stylesheet">
@include('partials.tagpagecontent', ['alltags' => $alltags, 'tags' => $tags])
@endsection
