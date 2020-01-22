<?php

namespace App\Models\Users;

class User extends \Illuminate\Database\Eloquent\Model{

	protected $fillable = ['username', 'password', 'first_name', 'last_name', 'email', 'email_verified', 'scope'];

	protected $table = 'oauth_users';

}