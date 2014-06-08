@extends("layout")

@section("content")

<h3>Вход</h3>

{{ Form::open(array('role' => 'form', 'class' => 'form-horizontal')) }}
<div class="form-group">
    {{ Form::label("email", "Email", array('class' => 'col-sm-2 control-label')) }}
    <div class="col-sm-4">
        {{ Form::text("email", Input::old("email"), array('class' => 'form-control')) }}
    </div>
</div>
<div class="form-group">
    {{ Form::label("password", "Пароль", array('class' => 'col-sm-2 control-label')) }}
    <div class="col-sm-4">
        {{ Form::password("password", array('class' => 'form-control')) }}
    </div>
</div>
<div class="form-group">
    <div class="col-sm-offset-2 col-sm-4">
        <div class="checkbox">
            <label>
                <input type="checkbox" name="is_remember"> Запомнить меня
            </label>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="col-sm-offset-2 col-sm-4">
        {{ Form::submit("Войти", array('class' => 'btn btn-default')) }}
    </div>
</div>
{{ Form::close() }}

@stop

