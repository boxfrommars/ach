@extends('layout')

@section('content')
<h1>Достижения</h1>
<ul>
    @foreach ($achievments as $achievment)
    <li><a href="/achievments/{{{ $achievment->id }}}">{{{ $achievment->title }}}</a></li>
    @endforeach
</ul>

@stop