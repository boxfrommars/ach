<?php

class AchievmentController extends BaseController
{

    public function getMain()
    {
        return ['url' => '/'];
    }

    public function getUsers()
    {
        $users = User::all();

        return $users;
    }

    public function getUser($id)
    {
        $user = User::find($id);
        if ($user === null) {
            App::abort(404, 'Page not found');
        }

        return $user;
    }

    public function getAchievments()
    {
        $achievments = Achievment::all();

        return $achievments;
    }

    public function getAchievment($id)
    {
        $achievment = Achievment::find($id);
        if ($achievment === null) {
            App::abort(404, 'Page not found');
        }

        return $achievment;
    }
}