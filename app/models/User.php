<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');

    // User >-< Achievment many to many relationship
    public function achievments()
    {
        return $this->belongsToMany('Achievment', 'user_achievments', 'id_user', 'id_achievment')->withPivot('is_approved');
    }

    // User >-< Group many to many relationship
    public function groups()
    {
        return $this->belongsToMany('Group', 'user_groups', 'id_user', 'id_group');
    }

}
