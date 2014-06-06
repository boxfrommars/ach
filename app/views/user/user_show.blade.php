@extends('layout')

@section('content')
    <h1>Профиль</h1>
    <div class="media">
        <div class="pull-left">
            <img class="img-thumbnail" src="/img/user/{{{ $user->image }}}" />
        </div>
        <div class="media-body">
            <h4 class="media-heading"><a href="/users/{{{ $user->id }}}">{{{ $user->name }}}</a></h4>
            <ul class="list-inline">
                @foreach ($user->achievments as $achievment)
                <li><a href="/achievments/{{{ $achievment->id }}}" title="{{{ $achievment->title }}}"><img class="img-thumbnail achievment-image-icon" src="/img/achievment/{{{ $achievment->image }}}" /></a></li>
                @endforeach
            </ul>

            <ul class="list-inline">
                @foreach ($user->groups as $group)
                <li><a class="label label-group label-{{{ $group->code }}}" href="/groups/{{{ $group->id }}}">{{{ $group->title }}}</a></li>
                @endforeach
            </ul>
        </div>
    </div>
@stop