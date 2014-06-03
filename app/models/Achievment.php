<?php

class Achievment extends Eloquent {

    protected $table = 'achievments';

    // User >-< Achievment many to many relationship
    public function users()
    {
        return $this->belongsToMany('User', 'user_achievments', 'id_achievment', 'id_user');
    }

    // Achievment >-< Group many to many relationship
    public function groups()
    {
        return $this->belongsToMany('Group', 'achievment_groups', 'id_achievment', 'id_group');
    }
}
