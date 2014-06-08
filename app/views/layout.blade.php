<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Achievments</title>
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/css/style.css" />
</head>

<body>
<!-- Fixed navbar -->
<div class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="/achievments">Достижения</a></li>
                <li><a href="/users">Пользователи</a></li>
            </ul>

            <ul class="nav navbar-nav navbar-right">
                @if (Auth::check())
                    <li><a href="/my">Мои успехи</a></li>
                    <li><a href="/logout">Выйти</a></li>
                @else
                    <li><a href="/login">Войти</a></li>
                @endif
            </ul>

        </div><!--/.nav-collapse -->
    </div>
</div>

<!-- Begin page content -->
<div class="container">
    @if (Session::has('errors'))
        @foreach (Session::get('errors') as $error)
            <div class="alert alert-danger">{{ $error }} <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>
        @endforeach
    @endif

    @yield('content')
</div>

<div id="footer">
    <div class="container">
        <p class="text-muted">&copy; Rutorika 2014</p>
    </div>
</div>

<script src="//code.jquery.com/jquery-2.1.1.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
</body>
</html>