<?php

class AchievmentController extends BaseController
{

    public function getMain()
    {
        return View::make('layout');
    }

    public function getUsers()
    {
        $users = User::with('achievments', 'groups')->get();

        return View::make('user.user_list', array('users' => $users));
    }

    public function getUser($id)
    {
        $user = User::with('achievments', 'groups')->find($id);
        if ($user === null) {
            App::abort(404, 'Page not found');
        }

        return View::make('user.user_show', array('user' => $user));
    }

    public function getAchievments()
    {
        $achievments = Achievment::with('users', 'groups')->get();

        return View::make('achievment.achievment_list', array('achievments' => $achievments));
    }

    public function getAchievment($id)
    {
        $achievment = Achievment::with('users.groups', 'users.achievments', 'groups')->find($id);
        if ($achievment === null) {
            App::abort(404, 'Page not found');
        }

        return View::make('achievment.achievment_show', array('achievment' => $achievment));
    }
}