<?php

class Achievments extends Eloquent {

    protected $table = 'achievments';

    // User >-< Achievment many to many relationship
    public function users()
    {
        return $this->belongsToMany('User', 'user_achievments', 'id_achievment', 'id_user');
    }
}
