<?php

class AuthController extends BaseController
{
    public function getLogin()
    {
        return View::make('auth.login');
    }

    public function postLogin()
    {
        // проверяем заполнены ли поля формы
        $validator = Validator::make(Input::all(), array(
            'email' => 'required',
            'password' => 'required'
        ));

        $credentials = array(
            'email' => Input::get('email'),
            'password' => Input::get('password'),
        );

        // если Auth::attempt вторым параметром принимает true, то приложение запоминает пользователя на неопределённое время
        // подробнее см. http://laravel.com/docs/security#authenticating-users
        $isRemember = Input::get('is_remember');

        if ($validator->passes() && Auth::attempt($credentials, $isRemember)) {
            // в случае успешной аутентификации редиректим на главную или на страницу с которой пользоваля средиректили на страницу логина
            return Redirect::intended('/');
        } else {
            // в случае неуспешной -- редиректим назад на форму, заполняя поля введёнными данными, также записываем в flash-сообщение ошибки
            return Redirect::back()
                ->withInput()
                ->with('errors', array('Неправильный логин или пароль'));
        }
    }

    public function logout()
    {
        Auth::logout();

        return Redirect::to('/');
    }
}